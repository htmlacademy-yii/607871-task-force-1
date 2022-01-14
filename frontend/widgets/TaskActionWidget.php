<?php


namespace frontend\widgets;


use frontend\models\Recall;
use frontend\models\Respond;
use frontend\models\Task;
use yii\base\Widget;

class TaskActionWidget extends Widget
{
    public $taskId;

    public function run()
    {
        $respond = new Respond();
        $recall = new Recall();
        $taskModel = new Task();
        return $this->render('task-action-widget', [
            'respond' => $respond,
            'recall' => $recall,
            'taskId' => $this->taskId,
            'taskModel'=> $taskModel,
        ]);
    }
}