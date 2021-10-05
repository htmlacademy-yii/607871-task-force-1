<?php


namespace frontend\models\forms;


use frontend\models\User;

class UserSearchForm extends \yii\base\Model
{
    public $categories = [];
    public $vacant = 0;
    public $online = 0;
    public $recalls = 0;
    public $favorite = 0;
    public $name_search = '';

    public function attributeLabels()
    {
        return [
            'vacant' => 'Сейчас свободен',
            'online' => 'Сейчас онлайн',
            'recalls' => 'Есть отзывы',
            'favorite' => 'В избранном',
            'name_search' => 'Поиск по имени'
        ];
    }

    public function rules()
    {
        return [
            [['vacant', 'online', 'recalls', 'favorite', 'name_search', 'categories'], 'safe']
        ];
    }

    public function getDataProvider()
    {
        $conditionsMap = [
            'categories' => ['user_category.category_id' => $this->categories],
            'vacant' => ['not in', 'user.id', User::getBusyExecutorsId()],
            'online' => ['in', 'user.id', User::getOnlineExecutorsId()],
            'recalls' => ['in', 'user.id', User::getExecutorsWithRecallsId()],
            'favorite' => ['in', 'user.id', User::findOne(10)->favorites],
            'name_search' => ['or like', 'user.name', explode(' ', $this->name_search)],
        ];

        $query = User::find()
            ->joinWith(['profile', 'categories'], true, 'RIGHT JOIN');

        if ($this->name_search) {
            $this->categories = [];
            $this->vacant = 0;
            $this->online = 0;
            $this->recalls = 0;
            $this->favorite = 0;
        }

        foreach (get_object_vars($this) as $property => $value) {
            if ($this->$property) {
                $conditionsMap[$property] ? $query->andWhere($conditionsMap[$property]) : null;
            }
        }

        return $query->orderBy(['last_visit_date' => SORT_DESC])->all();
    }
}
