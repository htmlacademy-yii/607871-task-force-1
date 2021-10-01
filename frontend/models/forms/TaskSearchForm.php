<?php

namespace frontend\models\forms;

use frontend\models\Task;


class TaskSearchForm extends \yii\base\Model
{
    public $categories = [];
    public $no_executor = true;
    public $no_address;
    public $search;
    public $period;

    public function attributeLabels()
    {
        return [
            'no_executor' => 'Без исполнителя',
            'no_address' => 'Удаленная работа',
            'period' => 'Период',
            'search' => 'Поиск по названию',
        ];
    }

    public function rules()
    {
        return [
            [['no_executor', 'no_address', 'search', 'period', 'categories'], 'safe']
        ];
    }

    public function getPeriodsList()
    {
        return [1 => 'За день', 2 => 'За неделю', 3 => 'За месяц'];
    }

    public function getDataProvider()
    {
        $dayInSeconds = 86400;
        $format = 'Y-m-d H:i:s';
        $periods = [
            1 => time() - $dayInSeconds,
            2 => time() - 7 * $dayInSeconds,
            3 => time() - 30 * $dayInSeconds,
        ];

        $conditionsMap = [
            'categories' => ['category_id' => $this->categories],
            'no_executor' => ['executor_id' => null],
            'no_address' => ['address' => null],
            'period' => ['between', 'creation_date', date($format, $periods[$this->period]), date($format, time())],
            'search' => ['or like', 'title', explode(' ', $this->search)],
        ];

        $query = Task::find();

        foreach (get_object_vars($this) as $property => $value) {
            if ($this->$property) {
                $conditionsMap[$property] ? $query->andWhere($conditionsMap[$property]) : null;
            }
        }

        return $query->joinWith('category')->orderBy(['creation_date' => SORT_DESC])->all();
    }
}