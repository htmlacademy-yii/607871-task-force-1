<?php


namespace frontend\controllers;


use frontend\models\Category;
use frontend\models\forms\UploadFilesForm;
use frontend\models\Profile;
use frontend\models\TaskFiles;
use frontend\models\User;
use frontend\models\UserCategory;
use frontend\models\UserPortfolio;
use frontend\models\UserSettings;
use yii\web\UploadedFile;

class AccountController extends SecuredController
{
    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $uploadFilesModel = new UploadFilesForm(['scenario' => UploadFilesForm::SCENARIO_UPDATE_ACCOUNT]);
        $profileModel = new Profile();
        $userModel = new User();
        $userSettingsModel = new UserSettings();
        $user = User::findOne(\Yii::$app->user->identity->id);

        if (\Yii::$app->request->getIsPost()) {

            $userUpdated = $this->updateUser($user);
            $userSettings = $this->updateUserSettings($user);
            $uploadFilesModel->files = UploadedFile::getInstances($uploadFilesModel, 'files');
            $uploadFilesModel->avatar = UploadedFile::getInstance($uploadFilesModel, 'avatar');
            $uploadFilesModel->validate();

            $profileUpdated = $this->updateProfile($uploadFilesModel, $user);

            if (!$userUpdated->errors && !$profileUpdated->errors && !$userSettings->errors && !$uploadFilesModel->errors) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    //Сохранение данных пользователя
                    $user->save();
                    //Сохранение данных пользовательского профиля
                    $profileUpdated->save();
                    //Сохранение настроек пользователя
                    $userSettings->save();
                    //Сохранение категорий пользователя
                    $this->updateUserCategories($user);
                    //Сохранения пользовательского портфолио
                    $this->updateUserPortfolio($uploadFilesModel, $user);

                    $transaction->commit();
                    return $this->redirect('/account');
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                }

            } else {
                $userModel->addErrors($user->errors);
                $profileModel->addErrors($user->profile->errors);
                $userSettingsModel->addErrors($userSettings->errors);
            }
        }
        return $this->render('index', [
            'profileModel' => $profileModel,
            'uploadFilesModel' => $uploadFilesModel,
            'userModel' => $userModel,
            'user' => $user,
            'userSettingsModel' => $userSettingsModel,
        ]);
    }

    /**
     * @param User $user
     * @throws \yii\base\Exception
     */

    private function updateUser(User $user): User
    {
        $user->scenario = User::SCENARIO_UPDATE_USER;
        $user->load(\Yii::$app->request->post());
        $passwordHash = \Yii::$app->security->generatePasswordHash($user->password);
        $user->password = $passwordHash;
        $user->password_repeat = $passwordHash;
        $user->validate();
        return $user;
    }

    /**
     * @param User $user
     * @throws \yii\db\Exception
     */
    private function updateUserCategories(User $user): void
    {
        $user->deactivateAllUserCategories();

        if ($user->new_categories_list) {
            foreach ($user->new_categories_list as $key => $category_id) {
                $userCategory = $user->getUserCategory($category_id);
                if (!$userCategory) {
                    $userCategory = new UserCategory();
                    $userCategory->user_id = $user->id;
                    $userCategory->category_id = $category_id;
                }
                $userCategory->active = UserCategory::USER_CATEGORY_ACTIVE_SET;
                $userCategory->save();
            }
        }
    }

    private function updateProfile(UploadFilesForm $uploadFilesModel, User $user): Profile
    {
        $profile = $user->profile;
        $profile->scenario = Profile::SCENARIO_ACCOUNT_INPUT_RULES;
        $profile->load(\Yii::$app->request->post());
        $profileIsValid = $profile->validate();
        if ($profileIsValid) {
            $profile->scenario = Profile::SCENARIO_DEFAULT;
            $profile->birth_date = date('Y-m-d', strtotime($profile->birth_date));
            $profile->avatar = $this->updateUserAvatar($uploadFilesModel, $user);
        }
        return $profile;
    }

    private function updateUserAvatar(UploadFilesForm $uploadFilesModel, $user): ?string
    {
        if (isset($uploadFilesModel->avatar)) {
            $newAvatarFileName = UploadFilesForm::uploadFile($uploadFilesModel->avatar);
            return $newAvatarFileName ? \Yii::$app->params['defaultUploadDirectory'] . $newAvatarFileName : null;
        }
        return $user->avatar;
    }


    private function updateUserSettings(User $user): UserSettings
    {
        $userSettings = $user->userSettings;

        if (!isset($userSettings)) {
            $userSettings = new UserSettings();
            $userSettings->user_id = $user->id;
        }
        $userSettings->deactivateAll();
        $userSettings->load(\Yii::$app->request->post());
        $userSettings->validate();
        return $userSettings;
    }

    private function updateUserPortfolio(UploadFilesForm $uploadFilesModel, User $user): void
    {
        if ($uploadFilesModel->files) {
            $user->deleteUserPortfolio();
        }
        foreach ($uploadFilesModel->files as $file) {
            $newFileName = UploadFilesForm::uploadFile($file); //загрузка файла из временной папки в uploads
            if ($newFileName) {
                $userPortfolio = new UserPortfolio();
                $userPortfolio->user_id = $user->id;
                $userPortfolio->file = \Yii::$app->params['defaultUploadDirectory'] . $newFileName;
                $userPortfolio->save();
            }
        }
    }
}