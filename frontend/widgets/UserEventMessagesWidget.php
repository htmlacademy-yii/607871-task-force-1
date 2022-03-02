<?php


namespace frontend\widgets;


use frontend\models\Task;
use yii\base\Widget;

class UserEventMessagesWidget extends Widget
{
    public function run()
    {
        $user = \Yii::$app->user->identity;
        $userMessages = $user->userMessage;
        return $this->render('user-event-messages-widget', [
            'userMessages' => $userMessages,
        ]);
    }

}