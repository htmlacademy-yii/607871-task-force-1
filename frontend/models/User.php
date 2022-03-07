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
    public $password;
    public $password_repeat;
    public $new_categories_list = [];

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
            [['email', 'name', 'password'], 'safe', 'on' => self::SCENARIO_CREATE_USER],
            [['email', 'name', 'password', 'password_repeat'], 'trim'],
            [['email', 'password', 'name'], 'required', 'on' => self::SCENARIO_CREATE_USER, 'message' => 'Поле должно быть заполнено'],
            ['name', 'required', 'on' => self::SCENARIO_CREATE_USER, 'message' => 'Введите ваше имя и фамилию'],
            [['name', 'email'], 'string', 'min' => 5, 'max' => 50, 'tooShort' => "Не меньше {min} символов", 'tooLong' => 'Не больше {max} символов'],
            [['password', 'password_repeat'], 'string', 'min' => 8, 'max' => 64, 'tooShort' => "Длина пароля от {min} символов", 'tooLong' => 'Длина пароля до {max} символов'],
            [['name'], 'unique', 'message' => 'Пользователь с таким именем уже существует'],
            [['email'], 'unique', 'message' => 'Пользователь с таким email уже существует'],
            ['email', 'email', 'message' => 'Введите валидный адрес электронной почты'],
            [['email', 'password', 'password_repeat', 'new_categories_list'], 'safe', 'on' => self::SCENARIO_UPDATE_USER],
            [['email', 'name'], 'required', 'on' => self::SCENARIO_UPDATE_USER, 'message' => 'Поле должно быть заполнено'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat', 'on' => self::SCENARIO_UPDATE_USER, 'message' => 'Пароли должны совпадать']
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
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondences()
    {
        return $this->hasMany(Correspondence::class, ['user_id' => 'id']);
    }

    /**
     * Метод возвращает соответствующий профиль пользователя.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Метод возвращает все отклики пользователя по задачам.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Respond::class, ['user_id' => 'id']);
    }

    /**
     * Метод возвращает список всех задач, в которых пользователь является заказчиком.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientTasks()
    {
        return $this->hasMany(Task::class, ['client_id' => 'id'])->inverseOf('client');
    }

    /**
     * Метод возвращает список всех задач, в которых пользователь является исполнителем.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorTasks()
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Метод возвращает список идентификаторов всех потенциальных пользователей, у которых
     * есть отзывы от заказчиков.
     *
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
            ->andWhere('active=1')
            ->column();
    }

    /**
     * Метод возвращает список всех активных категорий-специализаций, которые есть у отдельно взятого пользователя.
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
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
     * Метод возвращает список всех потенциальных исполнителей, которых пользователь добавлен в избранное.
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFavorites()
    {
        return $this->hasMany(User::class, ['id' => 'chosen_id'])
            ->viaTable('user_favorite', ['chooser_id' => 'id'],
                function ($query) {
                    return $query->andWhere(['active' => 1]);
                });
    }

    /**
     * Метод возвращает список фотографий-примеров работ пользователя.
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
     * Метод возвращает список отзывов по работе пользователя-исполнителя.
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
     * Метод возвращает перечень настроек пользователя, указанных им в профиле.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSettings()
    {
        return $this->hasOne(UserSettings::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Метод возвращает рейтинг пользователя-исполнителя, рассчитанный на основании оценок заказчиков.
     *
     * @return float
     * @throws \yii\base\InvalidConfigException
     */
    public function getRating(): ?float
    {
        return round($this->getRecalls()->average('rating'), 2);
    }

    /**
     * Метод возвращает адрес файла с аватаром пользователя, либо, при его отсутствии, дефолтный аватар.
     *
     * @return mixed|string|null
     */
    public function getAvatar()
    {
        $defaultAvatar = Yii::$app->params['defaultAvatarPath'] ?? '';
        return ($this->profile->avatar) ?: $defaultAvatar;
    }

    /**
     * Метод возвращает все уведомления, сгенерированные для пользователя.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessage()
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
     * Метод проверяет, установлена ли у пользователя конкретная настройка.
     *
     * @param string $setting
     * @return bool
     */
    public function checkUserSetting(string $setting)
    {
        return ($this->userSettings && $this->userSettings->$setting);
    }

    /**
     * Метод проверяет, специализируется ли пользователь на конкретной категории в качестве исполнителя.
     *
     * @param int $categoryId
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkUserCategory(int $categoryId)
    {
        return $this->getCategories()->where(['category.id' => $categoryId])->exists();
    }

    /**
     * Метод проверяет, установлена ли данная категория в настройках пользователя.
     * Если установлена, метод ее возвращает, если нет - ничего не возвращает.
     * @param int $categoryId
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getUserCategory(int $categoryId)
    {
        return UserCategory::find()->where(['user_id' => $this->id, 'category_id' => $categoryId])->one();
    }

    public function setPasswordHash($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Метод проверяет, добавлен ли пользователь с указанным id в перечень избранных у конкретного пользователя.
     *
     * @param int $chosenUserId
     * @return bool
     */
    public function checkIsUserFavorite(int $chosenUserId): bool
    {
        return UserFavorite::find()
            ->where('chooser_id =:chooser_id', [':chooser_id' => Yii::$app->user->identity->id])
            ->andWhere('chosen_id =:chosen_id', [':chosen_id' => $chosenUserId])
            ->andWhere('active=1')
            ->exists();
    }

    /**
     * Метод добавляет и активирует пользователя в список избранных, если его там нет, либо если он деактивирован.
     * Если же он уже есть в списке избранных и актвирован, то метод его деактивирует.
     *
     * @param int $chosenUserId
     * @return bool
     */
    public function switchUserFavorite(int $chosenUserId)
    {
        $userFavorite = UserFavorite::find()
            ->where('chooser_id =:chooser_id', [':chooser_id' => $this->id])
            ->andWhere('chosen_id =:chosen_id', [':chosen_id' => $chosenUserId])->one();

        if ($userFavorite) {
            $userFavorite->active = $userFavorite->active === 1 ? 0 : 1;
        } else {
            $userFavorite = new UserFavorite([
                'chooser_id' => $this->id,
                'chosen_id' => $chosenUserId,
                'active' => 1
            ]);
        }

        return $userFavorite->save();
    }

    /**
     * Метод деактивирует все категории пользователя, в которых он специализируется как исполнитель.
     *
     * @throws \yii\db\Exception
     */
    public function deactivateAllUserCategories()
    {
        Yii::$app->db
            ->createCommand('UPDATE user_category SET active = 0 WHERE user_id=:user_id', ['user_id' => $this->id])
            ->execute();
    }

    /**
     * Метод удаляет все фотографии пользователя, добавленные его в портфолио в профиле (примеры работ).
     *
     * @throws \yii\db\Exception
     */
    public function deleteUserPortfolio()
    {
        Yii::$app->db
            ->createCommand('DELETE FROM user_portfolio WHERE user_id=:user_id', ['user_id' => $this->id])
            ->execute();
    }

    /**
     * Метод создает уведомление о произошедшем событии для пользователя на сайте и отправляет уведомление на электронную почту.
     *
     * @param string $messageType
     * @param Task $task
     */
    public function inform(string $messageType, Task $task)
    {
        if ($this->userSettings) {

            if ($this->userSettings->task_actions && $messageType === UserMessage::TYPE_TASK_CONFIRMED) {
                $this->createUserMessage($messageType, $task);
                $this->sendEmail('taskConfirmed-html', $messageType, $task);
            }

            if ($this->userSettings->task_actions && $messageType === UserMessage::TYPE_TASK_FAILED) {
                $this->createUserMessage($messageType, $task);
                $this->sendEmail('taskFailed-html', $messageType, $task);
            }

            if ($this->userSettings->task_actions && $messageType === UserMessage::TYPE_TASK_CLOSED) {
                $this->createUserMessage($messageType, $task);
                $this->sendEmail('taskClosed-html', $messageType, $task);
            }

            if ($this->userSettings->new_message && $messageType === UserMessage::TYPE_NEW_MESSAGE) {
                $this->createUserMessage($messageType, $task);
                $this->sendEmail('taskCorrespondence-html', $messageType, $task);
            }

            if ($this->userSettings->new_recall && $messageType === UserMessage::TYPE_TASK_RECALLED) {
                $this->createUserMessage($messageType, $task);
                $this->sendEmail('taskRecalled-html', $messageType, $task);
            }
        }
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

    /**
     * Метод валидирует пароль пользователя, сравнивая его с хэшем в базе данных.
     *
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Метод создает сообщение конкретного типа для пользователя на сайте.
     *
     * @param string $messageType
     * @param Task $task
     * @return bool
     */
    protected function createUserMessage(string $messageType, Task $task): bool
    {
        $message = new UserMessage([
            'user_id' => $this->id,
            'task_id' => $task->id,
            'type' => $messageType,
        ]);
        return $message->save();
    }

    /**
     * Метод отправляет сообщение определенного типа пользователю на электронную почту.
     *
     * @param string $template
     * @param int $typeMessage
     * @param Task $task
     * @return bool
     */
    protected function sendEmail(string $template, int $typeMessage, Task $task): bool
    {
        $message = Yii::$app->mailer->compose($template, [
            'user' => $this,
            'task' => $task,
        ]);
        $message->setTo($this->email)->setFrom('yii-taskforce@mail.ru')
            ->setSubject(UserMessage::TYPE_MESSAGE_MAP[$typeMessage] . ' "' . $task->title . '"');
        return $message->send();
    }
}
