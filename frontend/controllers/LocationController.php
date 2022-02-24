<?php


namespace frontend\controllers;


use App\Exception\DataException;
use frontend\models\YandexGeo;
use yii\web\Response;

class LocationController extends SecuredController
{
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
            $GeoObjects = $responseData['response']['GeoObjectCollection']['featureMember'];
            $result = [];
            foreach ($GeoObjects as $value) {
                try {
                    $yandexGeo = new YandexGeo();
                    $yandexGeo->setParameters($value['GeoObject']);
                } catch (DataException $e) {
                    continue;
                }

                if (\Yii::$app->user->identity->city->name === $yandexGeo->city) {
                    $result [] = $yandexGeo->getAttributes();
                } else {
                    continue;
                }
            }
            \Yii::$app->cache->set($addressQueryModified, $result, 86400);
            return $result;
        }
    }
}
