<?php


namespace frontend\widgets;


use frontend\models\forms\FinishTaskForm;
use frontend\models\Respond;
use frontend\models\Task;
use yii\base\Widget;

class TaskActionWidget extends Widget
{
    public $taskId;

    /**
     * Виджет отображает формы, отвечающие за взаимодействие с заданием: отмена, отклик, отказ, завершение.
     * @return string
     */
    public function run()
    {
        $respond = new Respond();
        $finishTaskForm = new FinishTaskForm();
        $taskModel = new Task();
        return $this->render('task-action-widget', [
            'respond' => $respond,
            'finishTaskForm' => $finishTaskForm,
            'taskId' => $this->taskId,
            'taskModel' => $taskModel,
        ]);
    }
}