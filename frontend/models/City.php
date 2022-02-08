<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property float|null $latitude
 * @property float|null $longitude
 *
 * @property Profile[] $profiles
 * @property Task[] $tasks
 */
class City extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE_CITY = 'create_city';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'latitude', 'longitude'], 'required', 'on' => self::SCENARIO_CREATE_CITY],
            [['name', 'latitude', 'longitude'],'safe'],
            ['name', 'trim'],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Город',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::class, ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['city_id' => 'id']);
    }

    public static function getCityMap()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
