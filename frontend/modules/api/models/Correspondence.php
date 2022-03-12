<?php

namespace frontend\modules\api\models;

use \frontend\models\Correspondence as BaseCorrespondence;

class Correspondence extends BaseCorrespondence
{
    /**
     * Определение перечня полей для возврата на фронтэнд
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'message',
            'published_at',
            'is_mine' => function (BaseCorrespondence $model) {
                return (int) $model->user_id === (int) \Yii::$app->user->id;
            },
        ];
    }
}
