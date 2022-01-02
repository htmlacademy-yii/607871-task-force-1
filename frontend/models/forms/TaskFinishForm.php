<?php


namespace frontend\models\forms;


use frontend\models\Task;
use yii\base\Model;

class TaskFinishForm extends Model
{
    public $description;
    public $rating;
    public $status;
    const COMPLETION_YES = 'yes';
    const COMPLETION_PROBLEMS = 'difficult';

    const COMPLETION = [
        self::COMPLETION_YES => 'да',
        self::COMPLETION_PROBLEMS => 'Возникли проблемы'
    ];

    public function rules()
    {
        return [
            [['description', 'rating'], 'safe'],
            [['description', 'rating'], 'required', 'message' => 'Поле должно быть заполнено'],
            ['description', 'trim'],
            ['rating', 'integer', 'min' => 1, 'max' => 5, 'message' => "Поставьте оценку от 1 до 5"],
            //[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],

        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Комментарий',
            'rating' => 'Оценка',
        ];
    }

}