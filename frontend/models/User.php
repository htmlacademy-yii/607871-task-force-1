<?php

namespace frontend\models;

use frontend\models\behaviors\DateBehavior;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $reg_date
 * @property string $last_visit_date
 * @property string $password
 * @property array $portfolios
 *
 * @property Correspondence[] $correspondences
 * @property Profile $profile
 * @property Respond[] $responds
 * @property Task[] $clientTasks
 * @property Task[] $executorTasks
 * @property Category[] $categories
 * @property User[] $favorites
 * @property UserSettings[] $userSettings
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'last_visit_date', 'password'], 'required'],
            [['reg_date', 'last_visit_date'], 'safe'],
            [['name', 'email'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['email'], 'unique'],
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
            'email' => 'Email',
            'reg_date' => 'Reg Date',
            'last_visit_date' => 'Last Visit Date',
            'password' => 'Password',
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
     * Gets query for [[Correspondences]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondences()
    {
        return $this->hasMany(Correspondence::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Respond::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientTasks()
    {
        return $this->hasMany(Task::class, ['client_id' => 'id'])->inverseOf('user');
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorTasks()
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('user_category', ['user_id' => 'id'],
                function ($query) {
                    return $query->andWhere(['active' => 1]);
                });
    }

    /**
     * Gets query for [[Favorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(User::class, ['id' => 'chosen_id'])->viaTable('user_favorite', ['chooser_id' => 'id']);
    }

    /**
     * Gets query for [[Portfolios]].
     *
     * @return array
     */
    public function getPortfolios()
    {
        $query = new Query();
        $query->from('user_portfolio')->where('user_id =:user_id', [':user_id' => $this->id]);
        return $query->all();
    }

    /**
     * Gets query for [[UserSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasMany(UserSettings::class, ['user_id' => 'id']);
    }

    public static function getAllExecutors()
    {
        return User::find()->joinWith(['profile', 'categories'], true, 'RIGHT JOIN')->all();
    }



}
