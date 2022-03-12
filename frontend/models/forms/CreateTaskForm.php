<?php


namespace frontend\models\forms;

use App\Exception\DataException;
use frontend\models\Category;
use frontend\models\City;
use frontend\models\Task;
use frontend\models\TaskFiles;
use frontend\service\YandexGeo;

class CreateTaskForm extends BaseModelForm
{
    public $title;
    public $description;
    public $category_id;
    public $files;
    public $full_address;
    public $budget;
    public $due_date;
    public $address;
    public $latitude;
    public $longitude;
    public $city_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'category_id', 'budget', 'due_date', 'latitude', 'longitude', 'address'], 'safe'],
            [['title', 'description', 'category_id', 'due_date'], 'required', 'message' => 'Поле должно быть заполнено'],
            [['title', 'description', 'address'], 'trim'],
            ['due_date', 'datetime', 'format' => 'yyyy-MM-dd', 'min' => date('Y-m-d', strtotime('+1 day', time())),
                'strictDateFormat' => true, 'skipOnError' => false, 'message' => 'Введите дату в формате ГГГГ-ММ-ДД'],
            ['description', 'string', 'min' => 15, 'max' => 1500, 'tooShort' => "Не менее {min} символов", 'tooLong' => 'Не более {max} символов'],
            [['category_id', 'budget'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            ['title', 'string', 'min' => 5, 'max' => 100, 'tooShort' => "Не менее {min} символов",],
            ['address', 'string', 'max' => 255, 'tooLong' => 'Не более {max} символов'],
            ['category_id', 'exist', 'skipOnError' => false, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            ['city_name', 'string', 'max' => 100, 'tooLong' => 'Не более {max} символов'],
            ['city_name', 'isCityNameInCatalog'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Мне нужно',
            'description' => 'Подробности задания',
            'category_id' => 'Категория',
            'budget' => 'Бюджет',
            'status' => 'Status',
            'due_date' => 'Сроки исполнения',
            'full_address' => 'Локация',
            'address' => 'Адрес',
            'comments' => 'Comments',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Метод заполнения данных из формы в поля модели и сохранения в базе данных
     * @param UploadFilesForm $uploadFilesModel
     * @return Task|null
     */
    public function saveFields(UploadFilesForm $uploadFilesModel): ?Task
    {
        $task = new Task();
        $task->title = $this->title;
        $task->status = Task::STATUS_NEW;
        $task->description = $this->description;
        $task->category_id = $this->category_id;
        $task->budget = $this->budget;
        $task->due_date = $this->due_date;
        $task->client_id = \Yii::$app->user->id;
        $task->address = $this->address;
        $this->setTaskLocation($task);

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $task->save();
            $this->saveTaskFiles($task->id, $uploadFilesModel);
            $transaction->commit();
            return $task;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return null;
        }
    }

    /**
     * Метод загружает файлы задания в папку upload и сохраняет данные в базу данных.
     * @param int $taskId
     * @param UploadFilesForm $uploadFilesModel
     */
    private function saveTaskFiles(int $taskId, UploadFilesForm $uploadFilesModel)
    {
        foreach ($uploadFilesModel->files as $file) {
            $newFileName = UploadFilesForm::uploadFile($file); //загрузка файла из временной папки в uploads
            if ($newFileName) {
                $taskFile = new TaskFiles();
                $taskFile->task_id = $taskId;
                $taskFile->name = $file->name;
                $taskFile->url = \Yii::$app->params['defaultUploadDirectory'] . $newFileName;
                $taskFile->save();
            }
        }
    }

    /**
     * Если при создании задания был указан город, данный метод заполнит
     * @param Task $task
     */
    public function setTaskLocation(Task $task)
    {
        if ($this->city_name) {
            $taskCity = City::find()->where(['name' => $this->city_name])->one();
            if ($taskCity) {
                $task->city_id = $taskCity->id;
                $task->district = $this->searchDistrict();
                $task->latitude = $this->latitude;
                $task->longitude = $this->longitude;
            }
        }
    }

    /**
     * Метод отправляет запрос в Геокодер API Яндекс.Карт на поиск района города по координатам долготы и широты,
     * переданным в конкретном задании. Если район найден, возвращается его название.
     * @return mixed|null
     */
    private function searchDistrict()
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $geoCode = "{$this->longitude},{$this->latitude}";
        $responseData = YandexGeo::sendQuery($geoCode, 'district');

        if (!$responseData) {
            return null;
        }

        $districts = [];
        $geoObjects = $responseData['response']['GeoObjectCollection']['featureMember'];
        foreach ($geoObjects as $value) {
            try {
                $yandexGeo = new YandexGeo();
                $districts[] = $yandexGeo->searchDistrict($value['GeoObject']);
            } catch (DataException $e) {
                continue;
            }
        }

        if (!empty($districts)) {
            return array_shift($districts);
        }
    }
}