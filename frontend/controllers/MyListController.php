<?php


namespace frontend\controllers;


use frontend\models\Task;
use frontend\service\MyListService;
use yii\db\Expression;

class MyListController extends SecuredController
{
    /** Метод, отвечающий за отображение данных на странице "Мой список"
     * @return string
     */
    public function actionIndex()
    {
        $currentUserId = \Yii::$app->user->id;
        $status = \Yii::$app->request->get('status');
        $dataProvider = MyListService::dataProvider($currentUserId, $status);

        return $this->render('index', ['status' => $status, 'dataProvider' => $dataProvider]);
    }
}