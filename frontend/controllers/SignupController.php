<?php


namespace frontend\controllers;


use frontend\models\forms\CreateUserForm;

class SignupController extends SecuredController
{
    /** Метод отображает страницу "Регистрация", и выполняет обработку данных, полученных с этой формы.
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $createUserForm = new CreateUserForm();

        if (\Yii::$app->request->getIsPost()) {
            $createUserForm->load(\Yii::$app->request->post());
            $createUserForm->validate();
            if (!$createUserForm->errors && $createUserForm->saveFields()) {
                $this->redirect('/');
            }
        }
        if(\Yii::$app)

        return $this->render('index', ['createUserForm' => $createUserForm]);
    }
}