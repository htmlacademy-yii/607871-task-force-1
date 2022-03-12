<?php


namespace frontend\widgets;

use frontend\models\User;
use yii\base\Widget;

class UserEventMessagesWidget extends Widget
{
    /**
     * Виджет отображает список уведомлений для пользователя.
     * @return string
     */
    public function run()
    {
        $user = User::findOne(\Yii::$app->user->id);
        $userMessages = $user->userMessages;
        return $this->render('user-event-messages-widget', ['userMessages' => $userMessages]);
    }
}