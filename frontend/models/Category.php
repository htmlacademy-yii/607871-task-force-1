<?php

namespace frontend\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 *
 * @property Task[] $tasks
 * @property User[] $users
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'icon'], 'required'],
            [['name', 'icon'], 'string', 'max' => 100],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'icon' => 'Icon',
        ];
    }

    /**
     * Метод возвращает список всех задач, относящихся к определенной категории.
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['category_id' => 'id']);
    }

    /**
     * Метод возвращает список всех пользователей, у которых активирована определенная категория.
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_category', ['category_id' => 'id']);
    }

    /**
     * Метод возвращает список всех категорий в виде двумерного массива, каждый элемент которого состоит из id и
     * названия категории.
     * @return array
     */
    public static function getCategoryMap(): array
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
