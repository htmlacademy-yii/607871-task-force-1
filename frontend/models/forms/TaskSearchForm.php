<?php

namespace frontend\models\forms;

use frontend\models\Task;
use yii\data\ActiveDataProvider;
use yii\db\Expression;


class TaskSearchForm extends \yii\base\Model
{
    const PERIOD_DAY = 1;
    const PERIOD_WEEK = 7;
    const PERIOD_MONTH = 30;
    const PERIOD_ALL = 0;
    const PERIOD_MAP = [
        self::PERIOD_DAY => 'За день',
        self::PERIOD_WEEK => 'За неделю',
        self::PERIOD_MONTH => 'За месяц',
        self::PERIOD_ALL => 'За все время',
    ];
    public $categories = [];
    public $noExecutor = true;
    public $remoteWork;
    public $titleSearch;
    public $period;

    public function attributeLabels()
    {
        return [
            'noExecutor' => 'Без исполнителя',
            'remoteWork' => 'Удаленная работа',
            'period' => 'Период',
            'titleSearch' => 'Поиск по названию',
        ];
    }

    public function rules()
    {
        return [
            [['noExecutor', 'remoteWork', 'titleSearch', 'period', 'categories'], 'safe']
        ];
    }

    public function getDataProvider()
    {

        $query = Task::find()->joinWith('category')->orderBy(['creation_date' => SORT_DESC]);

        if ($this->categories) {
            $query->andWhere(['category_id' => $this->categories]);
        }

        if ($this->noExecutor) {
            $query->andWhere(['executor_id' => null]);
        }

        if ($this->remoteWork) {
            $query->andWhere(['address' => null]);
        }

        if ($this->titleSearch) {
            $query->andWhere(['or like', 'title', explode(' ', $this->titleSearch)]);
        }

        if ($this->period) {
            $query->andWhere(['>=', 'creation_date', (new Expression("NOW() - INTERVAL {$this->period} DAY"))]);

        }
        
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5
            ],
        ]);
    }
}