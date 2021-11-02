<?php

namespace frontend\models;

use frontend\models\behaviors\DateBehavior;
use Yii;
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
 * @property int $status (0 - new, 1 - canceled, 2 - in progress, 3 - finished, 5 - failed)
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
class Task extends \yii\db\ActiveRecord
{
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
            [['title', 'description', 'category_id', 'client_id', 'budget', 'status', 'due_date'], 'required'],
            [['description'], 'string'],
            [['category_id', 'client_id', 'executor_id', 'budget', 'status', 'city_id'], 'integer'],
            [['due_date', 'creation_date'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['title'], 'string', 'max' => 100],
            [['address', 'comments'], 'string', 'max' => 255],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'client_id' => 'Client ID',
            'executor_id' => 'Executor ID',
            'budget' => 'Budget',
            'status' => 'Status',
            'due_date' => 'Due Date',
            'creation_date' => 'Creation Date',
            'city_id' => 'City ID',
            'address' => 'Address',
            'comments' => 'Comments',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }
    /**
     * @return array|string[]
     */

    public function behaviors()
    {
        return [
            DateBehavior::class
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(User::className(), ['id' => 'client_id']);
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
        return $this->hasOne(User::class, ['id' => 'executor_id']);
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
