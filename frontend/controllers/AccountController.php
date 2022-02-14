<?php


namespace frontend\controllers;


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
    public function actionIndex()
    {
        $uploadFilesModel = new UploadFilesForm();
        $profileModel = new Profile();
        $userModel = new User();
        $userSettingsModel = new UserSettings();
        $user = User::findOne(\Yii::$app->user->identity->id);

        if (\Yii::$app->request->getIsPost()) {

            $user->scenario = User::SCENARIO_UPDATE_USER;
            $user->load(\Yii::$app->request->post());
            $userIsValid = $user->validate();

            $profile = $user->profile;
            $profile->scenario = Profile::SCENARIO_ACCOUNT_INPUT_RULES;
            $profile->load(\Yii::$app->request->post());


            $userSettings = $user->userSettings;
            $userSettings->load(\Yii::$app->request->post());
            $userSettingsIsValid = $userSettings->validate();

            $uploadFilesModel->files= UploadedFile::getInstances($uploadFilesModel, 'files');

           $uploadFilesIsValid = $uploadFilesModel->validate();

            //$newFileName = UploadFilesForm::uploadFile($uploadFilesModel->avatar);
           // $profile->avatar = $newFileName;

            $profileIsValid = $profile->validate();

            if ($userIsValid && $profileIsValid && $userSettingsIsValid && $uploadFilesIsValid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    //Сохранение данных пользователя
                    $passwordHash = \Yii::$app->security->generatePasswordHash($user->password);
                    $user->password = $passwordHash;
                    $user->password_repeat = $passwordHash;
                    $user->save();

                    //Сохранение данных пользовательского профиля
                    $profile->scenario = Profile::SCENARIO_DEFAULT;
                    $profile->birth_date = date('Y-m-d', strtotime($profile->birth_date));
                    $profile->save();

                    //Сохранение настроек пользователя
                    $userSettings->save();

                    //Сохранение категорий пользователя
                   /* foreach ($user->new_categories_list as $key => $category_id) {
                        $userCategory = new UserCategory();
                        $userCategory->user_id = $user->id;
                        $userCategory->category_id = $category_id;
                        $userCategory->active = UserCategory::USER_CATEGORY_ACTIVE_SET;
                        $userCategory->save();
                    }*/

                    //Сохранения пользовательского портфолио
                    foreach ($uploadFilesModel->files as $file) {
                        $newFileName = UploadFilesForm::uploadFile($file); //загрузка файла из временной папки в uploads
                        if ($newFileName) {
                            $userPortfolio = new UserPortfolio();
                            $userPortfolio->user_id = $user->id;
                            $userPortfolio->file = '/uploads/' . $newFileName;
                            $userPortfolio->save();
                        }
                    }

                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    var_dump($e->getMessage());
                }

            } else {
                $userModel->addErrors($user->errors);
                $profileModel->addErrors($profile->errors);
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


}