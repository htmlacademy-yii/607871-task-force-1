<?php

namespace frontend\models;


use yii\db\ActiveRecord;
use yii\db\Query;


/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $category_id
 * @property int $client_id
 * @property int|null $executor_id
 * @property int $budget
 * @property int $status (1 - new, 2 - canceled, 3 - in progress, 4 - finished, 5 - failed)
 * @property string $due_date
 * @property string $creation_date
 * @property int|null $city_id
 * @property string|null $address
 * @property string|null $comments
 * @property float|null $latitude
 * @property float|null $longitude
 * @property array $taskFiles
 * @property string $businessStatus
 * @property string $district
 *
 * @property Category $category
 * @property City $city
 * @property User $client
 * @property Correspondence[] $correspondences
 * @property User $executor
 * @property Recall $recall
 * @property Respond[] $responses
 */
class Task extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_CANCELED = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_FINISHED = 4;
    const STATUS_FAILED = 5;

    const SCENARIO_CREATE_TASK = 'create_task';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'category_id', 'budget', 'due_date', 'creation_date', 'latitude', 'longitude', 'address', 'district'], 'safe'],
            [['title', 'description', 'category_id', 'client_id', 'due_date'], 'required',
                'message' => 'Поле должно быть заполнено'],
            [['title', 'description', 'address', 'district'], 'trim'],
            ['due_date', 'datetime', 'format' => 'yyyy-MM-dd', 'min' => date('Y-m-d', strtotime('+1 days', time())), 'strictDateFormat' => true,
                'message' => 'Введите дату в формате ГГГГ-ММ-ДД', 'on' => self::SCENARIO_CREATE_TASK],
            ['description', 'string', 'min' => 15, 'max' => 1500,
                'tooShort' => "Не менее {min} символов", 'tooLong' => 'Не более {max} символов'],
            [['category_id', 'client_id', 'executor_id', 'budget', 'status', 'city_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            ['title', 'string', 'min' => 5, 'max' => 100, 'tooShort' => "Не менее {min} символов", 'tooLong' => 'Не более {max} символов'],
            ['address', 'string', 'max' => 255, 'tooLong' => 'Не более {max} символов'],
            ['district', 'string', 'max' => 150, 'tooLong' => 'Не более {max} символов'],
            ['client_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['client_id' => 'id']],
            ['executor_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['executor_id' => 'id']],
            ['city_id', 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            ['category_id', 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'client_id' => 'Client ID',
            'executor_id' => 'Executor ID',
            'budget' => 'Бюджет',
            'status' => 'Status',
            'due_date' => 'Сроки исполнения',
            'creation_date' => 'Creation Date',
            'city_id' => 'City ID',
            'full_address' => 'Локация',
            'address' => 'Адрес',
            'comments' => 'Comments',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Метод возвращает категорию, к которой относится конкретное задание.
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Метод возвращает город, к которому относится конкретное задание.
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Метод возвращает клинета, создавшего конкретное задание.
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(User::class, ['id' => 'client_id']);
    }

    /**
     * Метод возвращает список всех сообщений в блоке "Переписка", оставленных по конкретному заданию.
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondences()
    {
        return $this->hasMany(Correspondence::class, ['task_id' => 'id']);
    }

    /**
     * Метод возвращает пользователя, указанного в конкретном задании в качестве исполнителя.
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id'])->inverseOf('executorTasks');
    }

    /**
     * Метод возвращает отзыв, оставленный клинетом по конкретному заданию.
     * @return \yii\db\ActiveQuery
     */
    public function getRecall()
    {
        return $this->hasOne(Recall::class, ['task_id' => 'id']);
    }

    /**
     * Метод возвращает список всех откликов, относящихся к конкретному заданию.
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Respond::class, ['task_id' => 'id']);
    }

    /**
     * Метод возвращает список всех файлов, прикрепленных к конкретному заданию.
     * @return array
     */
    public function getTaskFiles()
    {
        $query = new Query();
        $query->from('task_files')->where('task_id =:task_id', [':task_id' => $this->id]);
        return $query->all();
    }
}
