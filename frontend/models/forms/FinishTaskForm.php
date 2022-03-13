<?php


namespace frontend\models\forms;


use frontend\models\Recall;
use frontend\models\Task;
use frontend\models\UserMessage;
use frontend\service\NotificationService;
use yii\base\Model;

class FinishTaskForm extends Model
{
    public $status;
    public $description;
    public $rating;
    public $taskId;

    const COMPLETION_YES = 'yes';
    const COMPLETION_PROBLEMS = 'difficult';

    const COMPLETION = [
        self::COMPLETION_YES => 'да',
        self::COMPLETION_PROBLEMS => 'Возникли проблемы'
    ];

    const TASK_STATUS_MAP = [
        self::COMPLETION_YES => Task::STATUS_FINISHED,
        self::COMPLETION_PROBLEMS => Task::STATUS_FAILED
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'taskId', 'description', 'rating'], 'safe'],
            ['status', 'required', 'message' => 'Поле должно быть заполнено'],
            ['description', 'trim'],
            [['status', 'description'], 'string'],
            ['rating', 'integer', 'min' => 1, 'max' => 5, 'message' => "Поставьте оценку от 1 до 5"],
            [['taskId'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['taskId' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'description' => 'Комментарий',
            'rating' => 'Оценка',
            'creation_date' => 'Creation Date',
        ];
    }


    /**
     * В зависимости от того, с каким признаком клиент завершает задание ("Да" или "Возникли проблемы"),
     * данный метод выбирает статус, который будет присвоен заданию ("Выполнено" или "Провалено").
     * @return mixed
     */
    public function getTaskStatus()
    {
        return self::TASK_STATUS_MAP[$this->status];
    }

    /**
     * Метод "заливает" данные из свойств формы в свойства моделей Task и Recall и сохраняет их в базе данных.
     * @param Task $task
     * @return Task
     */
    public function saveFields(Task $task)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $task->status = $this->getTaskStatus();
            $task->save();

            if ($this->description || $this->rating) {

                $recall = new Recall();
                $recall->task_id = $this->taskId;
                $recall->description = $this->description ?: null;
                $recall->rating = $this->rating ?: null;
                $recall->save();

                $notification = new NotificationService($task, $task->executor);
                $notification->inform(UserMessage::TYPE_TASK_RECALL);
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }
        return $task;
    }

}