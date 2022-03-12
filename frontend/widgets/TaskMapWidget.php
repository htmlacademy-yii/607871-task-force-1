<?php


namespace frontend\widgets;


use frontend\models\Task;
use yii\base\Widget;

class TaskMapWidget extends Widget
{
    public $taskId;

    /**
     * Виджет отображает карту, сгенерированную по координатам задания.
     * @return string
     */
    public function run()
    {
        $task = Task::findOne($this->taskId);
        return $this->render('task-map-widget', ['task' => $task]);
    }
}