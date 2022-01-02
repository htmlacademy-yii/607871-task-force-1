<?php


namespace frontend\widgets;


use frontend\models\forms\TaskFinishForm;
use frontend\models\Respond;
use yii\base\Widget;

class TaskActionWidget extends Widget
{
    public $id;

    public function run()
    {
        $respond = new Respond();
        $finishForm = new TaskFinishForm();
        return $this->render('task-action-widget', [
            'respond' => $respond,
            'finishForm' => $finishForm,
        ]);
    }
}