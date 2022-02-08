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
        $response_data = YandexGeo::sendQuery(\Yii::$app->request->get('search'));
        if ($response_data) {
            $GeoObjects = $response_data['response']['GeoObjectCollection']['featureMember'];
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
            return $result;
        }
    }
}
