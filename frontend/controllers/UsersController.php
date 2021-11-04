<?php


namespace frontend\controllers;


use frontend\models\forms\TaskSearchForm;
use frontend\models\forms\UserSearchForm;
use frontend\models\Recall;
use frontend\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
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
            ->joinWith('profile', 'categories')
            ->where('user.id =:id', ['id' => $id])
            ->one();

        if (!$user || !$user->categories) {
            throw new NotFoundHttpException("Исполнитель с ID {$id} не найден!");
        }

        $recalls = Recall::find()
            ->joinWith(['task'], true, 'RIGHT JOIN')
            ->where(['task.executor_id' => $user->id])->all();

        $searchForm = new TaskSearchForm();

        return $this->render('view', [
            'user' => $user,
            'model' => $searchForm,
            'recalls' => $recalls,
        ]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goHome();
    }

}