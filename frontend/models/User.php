<?php

namespace frontend\models;

use Yii;
use yii\db\Expression;
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
 * @property string $password_hash
 * @property array $portfolios
 *
 * @property Correspondence[] $correspondences
 * @property Profile $profile
 * @property Respond[] $responds
 * @property Task[] $clientTasks
 * @property Task[] $executorTasks
 * @property Category[] $categories
 * @property User[] $favorites
 * @property UserSettings $userSettings
 * @property Recall[] $recalls
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREATE_USER = 'create_user';
    const SCENARIO_UPDATE_USER = 'update_user';

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
            [['email', 'name'], 'safe', 'on' => self::SCENARIO_CREATE_USER],
            [['email', 'name', 'password_hash'], 'trim'],
            [['email', 'name', 'password_hash'], 'required', 'on' => self::SCENARIO_CREATE_USER, 'message' => 'Поле должно быть заполнено'],
            ['name', 'required', 'on' => self::SCENARIO_CREATE_USER, 'message' => 'Введите ваше имя и фамилию'],
            [['name', 'email'], 'string', 'min' => 5, 'max' => 50, 'tooShort' => "Не меньше {min} символов", 'tooLong' => 'Не больше {max} символов'],
            [['name'], 'unique', 'message' => 'Пользователь с таким именем уже существует'],
            [['email'], 'unique', 'message' => 'Пользователь с таким email уже существует'],
            ['email', 'email', 'message' => 'Введите валидный адрес электронной почты'],
            [['email', 'name'], 'required', 'on' => self::SCENARIO_UPDATE_USER, 'message' => 'Поле должно быть заполнено'],
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
            'password_repeat' => 'Повтор пароля'
        ];
    }

    /**
     * Метод возвращает все сообщения пользователя из блока "Переписка" по всем задачам.
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondences()
    {
        return $this->hasMany(Correspondence::class, ['user_id' => 'id']);
    }

    /**
     * Метод возвращает соответствующий профиль пользователя.
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Метод возвращает все отклики пользователя по задачам.
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Respond::class, ['user_id' => 'id']);
    }

    /**
     * Метод возвращает список всех задач, в которых пользователь является заказчиком.
     * @return \yii\db\ActiveQuery
     */
    public function getClientTasks()
    {
        return $this->hasMany(Task::class, ['client_id' => 'id'])->inverseOf('client');
    }

    /**
     * Метод возвращает список всех задач, в которых пользователь является исполнителем.
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorTasks()
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Метод возвращает список идентификаторов всех потенциальных пользователей, у которых
     * есть отзывы от заказчиков.
     * @return array|null
     */
    public static function getExecutorsWithRecallsId(): ?array
    {
        $query = new Query();
        $query->select('executor_id')
            ->from('recall')->leftJoin('task', 'task.id=recall.task_id')
            ->where('task.executor_id IS NOT NULL')
            ->distinct();
        return ArrayHelper::getColumn($query->all(), 'executor_id');
    }

    /**
     * Метод возвращает список всех потенциальных исполнителей, которые были на
     * сайте в течение последних 30 минут.
     * @return array|null
     */
    public static function getOnlineExecutorsId(): ?array
    {
        return self::find()->select('id')
            ->where(['>=', 'last_visit_date', (new Expression("NOW() - INTERVAL 30 MINUTE"))])->column();
    }

    /**
     * Метод возвращает список идентификаторов всех потенциальных исполнителей,
     * которые добавлены в избранное у отдельно взятого пользователя.
     * @return array|null
     */
    public function getFavoriteExecutorsId(): ?array
    {
        return UserFavorite::find()
            ->select('chosen_id')
            ->where('chooser_id=:chooser_id', ['chooser_id' => $this->id])
            ->andWhere(['active' => UserFavorite::STATUS_ACTIVE])
            ->column();
    }

    /**
     * Метод возвращает список всех активных категорий-специализаций, которые есть у отдельно взятого пользователя.
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('user_category', ['user_id' => 'id'],
                function ($query) {
                    return $query->andWhere(['active' => UserCategory::STATUS_ACTIVE]);
                });
    }

    /**
     * Метод возвращает список всех потенциальных исполнителей, которых пользователь добавлен в избранное.
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFavorites()
    {
        return $this->hasMany(User::class, ['id' => 'chosen_id'])
            ->viaTable('user_favorite', ['chooser_id' => 'id'],
                function ($query) {
                    return $query->andWhere(['active' => UserFavorite::STATUS_ACTIVE]);
                });
    }

    /**
     * Метод возвращает список фотографий-примеров работ пользователя.
     * @return array
     */
    public function getPortfolios()
    {
        $query = new Query();
        $query->from('user_portfolio')->where('user_id =:user_id', [':user_id' => $this->id]);
        return $query->all();
    }

    /**
     * Метод возвращает список отзывов по работе пользователя-исполнителя.
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getRecalls()
    {
        return $this->hasMany(Recall::class, ['task_id' => 'id'])
            ->viaTable('task', ['executor_id' => 'id'])->with('task', 'reviewer');
    }

    /**
     * Метод возвращает перечень настроек пользователя, указанных им в профиле.
     * @return \yii\db\ActiveQuery
     */
    public function getUserSettings()
    {
        return $this->hasOne(UserSettings::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Метод возвращает рейтинг пользователя-исполнителя, рассчитанный на основании оценок заказчиков.
     * @return float
     * @throws \yii\base\InvalidConfigException
     */
    public function getRating(): ?float
    {
        return round($this->getRecalls()->average('rating'), 2);
    }

    /**
     * Метод возвращает все уведомления, сгенерированные для пользователя.
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessages()
    {
        return $this->hasMany(UserMessage::class, ['user_id' => 'id'])
            ->with('task')->orderBy('creation_date DESC');
    }

    /**
     * Метод возвращает количество всех задач, завершенных пользователем в качестве исполнителя.
     *
     * @return bool|int|string|null
     */
    public function getExecutorTasksFinished()
    {
        return $this->getExecutorTasks()
            ->where(['task.status' => Task::STATUS_FINISHED])->count();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }
}
