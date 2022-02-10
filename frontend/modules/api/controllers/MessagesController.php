<?php
namespace  frontend\modules\api\controllers;

use frontend\models\Correspondence;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class MessagesController extends ActiveController
{
    public $modelClass = Correspondence::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create']);

        return $actions;
    }

    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => \frontend\modules\api\models\Correspondence::find()->where(['task_id' => \Yii::$app->request->get('task_id')])
        ]);
    }

    public function actionCreate()
    {
        $data = Json::decode(\Yii::$app->request->getRawBody());

        $model = new Correspondence();
        $model->task_id = $data['task_id'];
        $model->message = $data['message'];
        $model->user_id = \Yii::$app->user->getId();
        if (!$model->save()) {
            throw new BadRequestHttpException();
        }

        \Yii::$app->response->statusCode = 201;
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'id' => $model->id,
            'message' => $model->message,
            'published_at' => $model->published_at,
            'is_mine' => true,
        ];
    }
}