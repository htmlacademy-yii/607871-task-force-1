<?php


namespace frontend\controllers;


use frontend\models\forms\UploadFilesForm;
use frontend\models\forms\UserAccountForm;
use frontend\models\User;
use yii\web\UploadedFile;

class AccountController extends SecuredController
{
    /**
     * Метод, отвечающий за отображение страницы "Мой профиль" и обработку запроса на обновление данных,
     * полученного с этой страницы.
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $userAccountForm = new UserAccountForm();
        $uploadFilesModel = new UploadFilesForm(['scenario' => UploadFilesForm::SCENARIO_UPDATE_ACCOUNT]);
        $user = User::findOne(\Yii::$app->user->identity->id);

        if (\Yii::$app->request->getIsPost()) {
            $userAccountForm->load(\Yii::$app->request->post());
            $userAccountForm->validate();

            $uploadFilesModel->files = UploadedFile::getInstances($uploadFilesModel, 'files');
            $uploadFilesModel->avatar = UploadedFile::getInstance($uploadFilesModel, 'avatar');
            $uploadFilesModel->validate();

            if (!$userAccountForm->errors && !$uploadFilesModel->errors && $userAccountForm->saveFields($uploadFilesModel)) {
                return $this->redirect('/account');
            }
        }

        return $this->render('index', [
            'userAccountForm' => $userAccountForm,
            'uploadFilesModel' => $uploadFilesModel,
            'user' => $user,
        ]);
    }
}