<?php /** @noinspection ALL */

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "respond".
 *
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property string $description
 * @property int $rate
 * @property int $status
 * @property string $creation_date
 *
 * @property Task $tasks
 * @property User $volunteer
 */
class Respond extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REFUSED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'respond';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'rate'], 'safe'],
            [['user_id', 'task_id', 'description', 'rate'], 'required', 'message' => 'Поле должно быть заполнено'],
            [['user_id', 'task_id', 'rate', 'status'], 'integer', 'message' => 'Значение должно содержать только цифры'],
            ['description', 'trim'],
            ['description', 'string', 'min' => 2, 'tooShort' => "Минимальное количество символов - {min}"],
            [['creation_date', 'status'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'task_id' => 'Task ID',
            'description' => 'Комментарий',
            'rate' => 'Ваша цена',
            'status' => 'Status',
            'creation_date' => 'Creation Date',
        ];
    }

    /**
     * Метод возвращает задание, к которому относится конкретный отклик.
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id'])->inverseOf('responds');
    }

    /**
     * Метод возвращает пользователя, оставившего конкретный отклик к заданию.
     * @return \yii\db\ActiveQuery
     */
    public function getVolunteer()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('responds');
    }
}
