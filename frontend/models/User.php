<?php

namespace frontend\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

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
 * @property Recall[] $recalls
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
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
            [['email', 'name', 'password'], 'safe'],
            [['email', 'name', 'password'], 'trim'],
            [['email', 'password'], 'required', 'message' => 'Поле должно быть заполнено'],
            [ 'name', 'required', 'message' => 'Введите ваше имя и фамилию'],
            [['name', 'email'], 'string', 'max' => 50, 'message' => 'Не больше 50 символов'],
            [['password'], 'string', 'min' => 8, 'max' => 64, 'tooShort' => "Длина пароля от {min} символов", 'tooLong' => 'Длина пароля до {max} символов'],
            [['name'], 'unique', 'message' => 'Пользователь с таким именем уже существует'],
            [['email'], 'unique', 'message' => 'Пользователь с таким email уже существует'],
            ['email', 'email', 'message' => 'Введите валидный адрес электронной почты'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Электронная почта',
            'name' => 'Ваше имя',
            'password' => 'Пароль',
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
        return $this->hasMany(Task::class, ['client_id' => 'id'])->inverseOf('client');
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

    public static function getExecutorsWithRecallsId()
    {
        $query = new Query();
        $query->select('executor_id')
            ->from('recall')->leftJoin('task', 'task.id=recall.task_id')
            ->where('task.executor_id IS NOT NULL')
            ->distinct();
        return ArrayHelper::getColumn($query->all(), 'executor_id');
    }

    public static function getOnlineExecutorsId()
    {
        return [2, 13];
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
        return $this->hasMany(User::class, ['id' => 'chosen_id'])
            ->viaTable('user_favorite', ['chooser_id' => 'id']);
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
     * Gets query for [[Recalls]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */

    public function getRecalls()
    {
     return $this->hasMany(Recall::class, ['task_id' => 'id'])
         ->viaTable('task', ['executor_id' => 'id'])->with('task', 'reviewer');
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

    public function getRating()
    {
        return round($this->getRecalls()->average('rating'), 2);
    }

    public function getAvatar()
    {
        return ($this->profile->avatar) ?: Yii::$app->params['defaultAvatarPath'];
    }


    public function getExecutorTasksFinished()
    {
        return $this->getExecutorTasks()
            ->where(['task.status' => Task::STATUS_FINISHED])->count();
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
