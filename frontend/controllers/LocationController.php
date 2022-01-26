<?php


namespace frontend\controllers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class LocationController extends SecuredController
{
    public function actionIndex()
    {
        $this->layout = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $apiKey = \Yii::$app->params['yandexAPIKey'];
        $geoCode = \Yii::$app->request->get('search');
        $client = new Client(['base_uri' => 'https://geocode-maps.yandex.ru/1.x']);
        try {
            $response = $client->request('GET', '', [
                'query' => [
                    'apikey' => $apiKey,
                    'geocode' => $geoCode,
                    'format' => 'json',
                    'lang' => 'ru_RU',
                    'sco' => 'longlat',
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new BadResponseException("Ошибка ответа: " . $response->getReasonPhrase());
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ServerException("Неверный или некорректный JSON");
            }

            $content = $response->getBody()->getContents();
            $response_data = json_decode($content, true);
            $addresses = $response_data['response']['GeoObjectCollection']['featureMember'];
            $result = [];
            $address = [];
            foreach ($addresses as $value) {
                $point = explode(' ', $value['GeoObject']['Point']['pos']);
                $address['latitude'] = $point[1];
                $address['longitude'] = $point[0];
                $address['full_address'] = $value['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['formatted'];
                $address['city'] = array_values(
                    array_filter($value['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['Components'], function ($array) {
                        return $array['kind'] === 'locality';
                    })
                )[0]['name'];
                $address['address'] = $value['GeoObject']['name'];
                $result [] = $address;
            }
            return $result;
        } catch (RequestException $e) {
            return [];
        }

    }
}