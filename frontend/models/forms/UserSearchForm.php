<?php


namespace frontend\models\forms;


use frontend\models\User;

class UserSearchForm extends \yii\base\Model
{
    public $categories = [];
    public $vacant;
    public $online;
    public $recalls;
    public $favorite;
    public $search;

    public function attributeLabels()
    {
        return [
            'vacant' => 'Сейчас свободен',
            'online' => 'Сейчас онлайн',
            'recalls' => 'Есть отзывы',
            'favorite' => 'В избранном',
            'search' => 'Поиск по имени'
        ];
    }

    public function rules()
    {
        return [
            [['vacant', 'online', 'recalls', 'favorite','search', 'categories'], 'safe']
        ];
    }
    public function getDataProvider()
    {
        $conditionsMap = [
            'categories' => ['user_category.category_id' => $this->categories],
            'vacant' => ['or', 'task.status!=2', ['task.status'=> null]],
            //'online' => [],
            /*SELECT DISTINCT `user`.*
            FROM `user`
            RIGHT JOIN `user_profile` ON `user`.`id` = `user_profile`.`user_id`
            RIGHT JOIN `user_category` ON `user`.`id` = `user_category`.`user_id`
            RIGHT JOIN `category` ON `user_category`.`category_id` = `category`.`id`
            LEFT JOIN `task` ON `user`.`id` = `task`.`executor_id`
            WHERE (`user_category.category_id` IN ('1', '2', '3', '4', '5', '6', '7', '8'))
        AND (`task`.`status` != 2) AND (`active`=1) ORDER BY `last_visit_date` DESC*/
            //'recalls' => ['address' => null],
            //'favorite' => [],
        ];

        $query = User::find()
            ->joinWith(['profile', 'categories'], true, 'RIGHT JOIN')
            ->joinWith('executorTasks');
        ;

        foreach (get_object_vars($this) as $property => $value) {
            if ($this->$property) {
                $conditionsMap[$property] ? $query->andWhere($conditionsMap[$property]) : null;
            }
        }

        return $query->orderBy(['last_visit_date' => SORT_DESC])->all();
    }




}
