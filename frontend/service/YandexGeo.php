<?php


namespace frontend\service;


use App\Exception\DataException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use Yii;
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

    /**
     * Метод разбирает геообект, полученный из геокодера API Яндекс.Карт, и заливает нужные из него значения в
     * свойства ассоциативного массива для дальнейшей передачи данных на фронтенд.
     * @param array $geoObject - геообъект, полученный из ответа геокодера API Яндекс.Карт.
     * @throws DataException
     */
    public function setParameters(array $geoObject)
    {
        $this->setComponents($geoObject);
        $this->city = $this->searchCityName();
        $this->latitude = $this->searchPoint($geoObject)['latitude'];
        $this->longitude = $this->searchPoint($geoObject)['longitude'];
        $this->full_address = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['formatted'] ?? null;
        $this->short_address = $this->searchShortAddress();
    }

    /**
     * Метод отвечает за поиск блока 'Components' в структуре объекта, полученного от геокодера API Яндекс.Карт в ответ на запрос.
     * Данные из этого блока сохраняются для дальнейшей обработки.
     * Блок 'Components' содержит отдельные фрагменты полного адреса.
     * @param array $geoObject - геообъект, полученный из ответа геокодера API Яндекс.Карт.
     * @throws DataException
     */
    public function setComponents(array $geoObject)
    {
        if (!isset($this->components)) {
            $components = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
            if (isset($components)) {
                $this->components = $components;
            } else {
                throw new DataException("Геоданные не определены");
            }
        }
    }

    /**
     * Метод отправляет запрос в геокодер API Яндекс.Карт и возвращает
     * @param string $geoCode - может быть задан в виде строки с адресом (город+улица+дом), либо в виде координат(долгота,широта)
     * @param null $kind - задает требуемую степень точности адреса. Задавать значение имеет смысл только в том случае, если в $geoCode
     * были переданы координаты.
     * @return array|mixed
     */
    public static function sendQuery(string $geoCode, $kind = null)
    {

        $apiKey = Yii::$app->params['yandexAPIKey'];
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
            $request = new Request('GET', '');
            $response = $client->send($request, [
                'query' => $query
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new BadResponseException("Ошибка ответа: " . $response->getReasonPhrase(), $request, $response);
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ServerException("Неверный или некорректный JSON", $request, $response);
            }

            $content = $response->getBody()->getContents();
            return json_decode($content, true);

        } catch (RequestException $e) {
            $e->getMessage();
            return [];
        } catch (GuzzleException $e) {
            $e->getMessage();
            return [];
        }
    }

    /**
     * Метод отвечает за поиск названия района города в геообъекте, полученном от геокодера API Яндекс.Карт.
     * @param array $geoObject - геообъект, полученный из ответа геокодера API Яндекс.Карт.
     * @return string|null
     * @throws DataException
     */
    public function searchDistrict(array $geoObject): ?string
    {
        $this->setComponents($geoObject);
        return array_values(array_filter($this->components, function ($array) {
                    return $array['kind'] === 'district';
                })
            )[0]['name'] ?? null;
    }

    /**
     * Метод отвечает за поиск названия города в компонентах полного адреса.
     * @return string|null
     */
    private function searchCityName(): ?string
    {
        return array_values(array_filter($this->components, function ($array) {
                    return $array['kind'] === 'locality';
                })
            )[0]['name'] ?? null;
    }

    /**
     * Метод отвечает за поиск точных координат задания для дальнейшей их подстановки на карту в виде маркера.
     * @param array $geoObject - геообъект, полученный из ответа геокодера API Яндекс.Карт.
     * @return array|null
     */
    private function searchPoint(array $geoObject): ?array
    {
        if (isset($geoObject['Point']['pos'])) {
            $point = explode(' ', $geoObject['Point']['pos']);
            return count($point) == 2 ? ['latitude' => $point[1], 'longitude' => $point[0]] : null;
        }
        return null;
    }

    /**
     * Метод отвечает за составление короткого адреса (улица+номер дома) из отдельных компонентов полного адреса.
     * @return string|null
     */
    private function searchShortAddress(): ?string
    {
        $street = (array_values(array_filter($this->components, function ($array) {
                    return $array['kind'] === 'street';
                })
            )[0]['name']) ?? null;

        $house = (array_values(array_filter($this->components, function ($array) {
                    return $array['kind'] === 'house';
                })
            )[0]['name']) ?? null;

        return isset($street, $house) ? "{$street}, {$house}" : null;
    }
}