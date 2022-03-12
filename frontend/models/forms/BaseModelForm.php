<?php


namespace frontend\models\forms;


use frontend\models\City;
use frontend\models\Profile;
use frontend\models\User;
use yii\base\Model;

class BaseModelForm extends Model
{
    /**
     * Метод валидирует атрибут формы, относящийся к пользователю, на предмет уникальности.
     * @param $attribute
     */
    public function isUserAttributeUnique($attribute)
    {
        $user = User::find()->where([$attribute => $this->$attribute])->one();
        if ($user && $user->id !== \Yii::$app->user->identity->id) {
            $this->addError($attribute, "Пользователь со значением {$this->$attribute} уже существует");
        }
    }

    /**
     * Метод валидирует атрибут формы, относящийся к профилю пользователя, на предмет уникальности.
     * @param $attribute
     */
    public function isProfileAttributeUnique($attribute)
    {
        $profile = Profile::find()->where([$attribute => $this->$attribute])->one();
        if ($profile && $profile->user_id !== \Yii::$app->user->identity->id) {
            $this->addError($attribute, "Пользователь с такими данными уже существует");
        }
    }

    /**
     * Метод валидации города, выбранного в задании.
     * @param $attribute
     */
    public function isCityNameInCatalog($attribute)
    {
        $taskCity = City::find()->where(['name' => $this->$attribute])->one();
        if (!$taskCity) {
            $this->addError('fullAddress', "Город {$this->$attribute} отсутствует в справочнике");
        }
    }
}