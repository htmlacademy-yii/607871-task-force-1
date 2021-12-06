<?php


namespace frontend\controllers;


use frontend\models\Profile;
use frontend\models\User;
use yii\web\Controller;

class SignupController extends Controller
{
    public function actionIndex()
    {
        $user = new User();
        $profile = new Profile();
        if (\Yii::$app->request->getIsPost()) {

            if ($user->load(\Yii::$app->request->post()) && $profile->load(\Yii::$app->request->post())) {
                $isValid = $user->validate();
                $isValid = $profile->validate() && $isValid;

                if ($isValid) {
                    $passwordHash = \Yii::$app->security->generatePasswordHash($user->getAttribute('password'));
                    $user->setAttribute('password', $passwordHash);
                    $db = \Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {
                        $user->save(false);
                        $userId = $user->getId();
                        $profile->setAttribute('user_id', $userId);
                        $profile->save(false);
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