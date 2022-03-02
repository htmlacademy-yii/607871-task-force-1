<?php


namespace frontend\widgets;


use frontend\models\Task;
use yii\base\Widget;

class TaskMapWidget extends Widget
{
    public $taskId;

    public function run()
    {
        $task = Task::findOne($this->taskId);
        return $this->render('task-map-widget', [
            'task' => $task,
        ]);
    }
}