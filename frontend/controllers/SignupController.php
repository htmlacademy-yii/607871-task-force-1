<?php


namespace frontend\controllers;


use frontend\models\Profile;
use frontend\models\User;

class SignupController extends SecuredController
{
    public function actionIndex()
    {
        $user = new User(['scenario' => User::SCENARIO_CREATE_USER]);
        $profile = new Profile();
        if (\Yii::$app->request->getIsPost()) {

            if ($user->load(\Yii::$app->request->post()) && $profile->load(\Yii::$app->request->post())) {
                $user->validate();
                $profile->validate();

                if (!$user->errors && !$profile->errors) {
                    $passwordHash = \Yii::$app->security->generatePasswordHash($user->password);
                    $user->password_hash = $passwordHash;
                     $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $user->save();
                        $userId = $user->getId();
                        $profile->user_id = $userId;
                        $profile->save();
                        $transaction->commit();
                        $this->redirect('/');
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }
        return $this->render('index', ['user' => $user, 'profile' => $profile]);
    }

}