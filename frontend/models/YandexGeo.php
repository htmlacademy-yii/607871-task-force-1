<?php


namespace frontend\models;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use yii\base\Model;


class YandexGeo extends Model
{
    public $city;
    public $full_address;
    public $short_address;
    public $latitude;
    public $longitude;
    private $components;

    public function attributeLabels()
    {
        return [
            'city' => 'Город',
            'full_address' => 'Полный адрес',
            'short_address' => 'Короткий адрес',
            'latitude' => 'Широта',
            'longitude' => 'Долгота',
        ];
    }

    public function setParameters(array $geoObject)
    {
        $this->setComponents($geoObject);
        $this->city = $this->searchCityName();
        $this->latitude = $this->searchPoint($geoObject)['latitude'];
        $this->longitude = $this->searchPoint($geoObject)['longitude'];
        $this->full_address = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['formatted'];
        $this->short_address = $this->searchShortAddress();
    }

    public function setComponents(array $geoObject)
    {
        if (!isset($this->components)) {
            $this->components = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
        }
    }

    private function searchCityName(): ?string
    {
        return array_values(array_filter($this->components, function ($array) {
                return $array['kind'] === 'locality';
            })
        )[0]['name'];
    }

    private function searchPoint(array $geoObject): array
    {
        $point = explode(' ', $geoObject['Point']['pos']);
        return ['latitude' => $point[1], 'longitude' => $point[0]];
    }

    private function searchShortAddress()
    {
        $street = array_values(array_filter($this->components, function ($array) {
                return $array['kind'] === 'street';
            })
        )[0]['name'];

        $house = array_values(array_filter($this->components, function ($array) {
                return $array['kind'] === 'house';
            })
        )[0]['name'];

        return "{$street}, {$house}";
    }

    public function searchDistrict(array $geoObject)
    {
        $this->setComponents($geoObject);
        return array_values(array_filter($this->components, function ($array) {
                return $array['kind'] === 'district';
            })
        )[0]['name'];
    }

    public static function sendQuery(string $geoCode, $kind = null)
    {
        $apiKey = \Yii::$app->params['yandexAPIKey'];
        $client = new Client(['base_uri' => 'https://geocode-maps.yandex.ru/1.x']);
        $query = [
            'apikey' => $apiKey,
            'geocode' => $geoCode,
            'format' => 'json',
            'lang' => 'ru_RU',
            'sco' => 'longlat',
        ];

        isset($kind) ? $query['kind'] = $kind : false;

        try {
            $response = $client->request('GET', '', [
                'query' => $query
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new BadResponseException("Ошибка ответа: " . $response->getReasonPhrase());
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ServerException("Неверный или некорректный JSON");
            }

            $content = $response->getBody()->getContents();
            return json_decode($content, true);

        } catch (RequestException $e) {
            return [];
        } catch (GuzzleException $e) {
            return [];
        }
    }
}