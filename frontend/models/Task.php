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
 * @property int $status (0 - new, 1 - canceled, 2 - in progress, 3 - finished, 4 - failed)
 * @property string $due_date
 * @property string $creation_date
 * @property int|null $city_id
 * @property string|null $address
 * @property string|null $comments
 * @property float|null $latitude
 * @property float|null $longitude
 * @property array $taskFiles
 *
 * @property Category $category
 * @property City $city
 * @property User $client
 * @property Correspondence[] $correspondences
 * @property User $executor
 * @property Recall[] $recalls
 * @property Respond[] $responds

 */
class Task extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_CANCELED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_FINISHED = 3;
    const STATUS_FAILED = 4;

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
            [['title', 'description', 'category_id', 'budget', 'due_date', 'creation_date', 'latitude', 'longitude'], 'safe'],
            [['title', 'description', 'category_id', 'client_id', 'due_date'], 'required',
                'message' => 'Поле должно быть заполнено'],
            [['title', 'description', ],'trim'],
            ['due_date', 'date', 'format' => 'Y-m-d', 'message' => 'Введите дату в формате ГГГГ-ММ-ДД'],
            [['description'], 'string', 'min' => 15, 'max' => 1500,
                'tooShort' => "Не менее {min} символов", 'tooLong' => 'Не более {max} символов' ],
            [['category_id', 'client_id', 'executor_id', 'budget', 'status', 'city_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            ['title', 'string', 'min' => 5, 'max' => 100, 'tooShort' => "Не менее {min} символов", 'tooLong' => 'Не более {max} символов'],
            ['address', 'string', 'max' => 255, 'tooLong' => 'Не более {max} символов'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['client_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'address' => 'Локация',
            'comments' => 'Comments',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(User::class, ['id' => 'client_id'])->inverseOf('clientTasks');
    }

    /**
     * Gets query for [[Correspondences]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondences()
    {
        return $this->hasMany(Correspondence::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id'])->inverseOf('executorTasks');
    }

    /**
     * Gets query for [[Recalls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecalls()
    {
        return $this->hasMany(Recall::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Respond::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return array
     */
    public function getTaskFiles()
    {
        $query = new Query();
        $query->from('task_files')->where('task_id =:task_id', [':task_id'=> $this->id]);
        return $query->all();
    }
}
