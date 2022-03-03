<?php


namespace frontend\controllers;


use app\models\UserFavorite;
use frontend\models\forms\TaskSearchForm;
use frontend\models\forms\UserSearchForm;
use frontend\models\User;
use yii\web\NotFoundHttpException;

class UsersController extends SecuredController
{
    public function actionIndex()
    {
        $searchForm = new UserSearchForm();
        $searchForm->load(\Yii::$app->request->get());
        return $this->render('index', ['dataProvider' => $searchForm->getDataProvider(), 'model' => $searchForm]);
    }

    public function actionView($id)
    {
        $user = User::find()
            ->with('profile', 'categories', 'recalls')
            ->where('user.id =:id', ['id' => $id])
            ->one();

        if (!$user || !$user->categories) {
            throw new NotFoundHttpException("Исполнитель не найден!");
        }

        $searchForm = new TaskSearchForm();

        return $this->render('view', [
            'user' => $user,
            'model' => $searchForm,
        ]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSetCity()
    {
        if (\Yii::$app->request->get('city')) {
            \Yii::$app->session->set('city_id', \Yii::$app->request->get('city'));
        }
        $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionSwitchFavorite(int $chosenUserId)
    {
        if (\Yii::$app->user->identity->id !== $chosenUserId) {
            \Yii::$app->user->identity->switchUserFavorite($chosenUserId);
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}