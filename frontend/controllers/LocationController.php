<?php


namespace frontend\controllers;

use frontend\service\TaskService;
use frontend\service\YandexGeo;
use yii\web\Response;

class LocationController extends SecuredController
{
    /**
     * Метод, отвечающий за отправку запроса в геокодер API Яндекс.Карт и обработку ответа.
     * @return array|mixed
     */
    public function actionIndex()
    {
        $this->layout = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $addressQuery = \Yii::$app->request->get('search');
        $addressQueryModified = 'Yandex' . mb_substr(md5($addressQuery), 0, 26);
        $addressInCache = \Yii::$app->cache->get($addressQueryModified);

         if ($addressInCache) {
            return $addressInCache;
         }

        $responseData = YandexGeo::sendQuery(\Yii::$app->request->get('search'));


         if ($responseData) {
            $result = TaskService::createAutocompleteAddress($responseData);
            \Yii::$app->cache->set($addressQueryModified, $result, 86400);
            return $result;
        }
    }
}
