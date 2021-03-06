<?php


namespace frontend\models\forms;


use frontend\models\User;
use frontend\models\UserSettings;
use yii\data\ActiveDataProvider;

class UserSearchForm extends \yii\base\Model
{
    public $categories = [];
    public $vacant;
    public $online;
    public $recalls;
    public $favorite;
    public $nameSearch;

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vacant' => 'Сейчас свободен',
            'online' => 'Сейчас онлайн',
            'recalls' => 'Есть отзывы',
            'favorite' => 'В избранном',
            'nameSearch' => 'Поиск по имени'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vacant', 'online', 'recalls', 'favorite', 'nameSearch', 'categories'], 'safe']
        ];
    }

    /**
     * Метод возвращает ActiveDataProvider для отображения данных на странице "Исполнители".
     * @return ActiveDataProvider
     */
    public function getDataProvider()
    {
        $query = User::find()
            ->joinWith(['profile', 'categories', 'userSettings'], true, 'RIGHT JOIN');

        if ($this->nameSearch) {
            $this->categories = [];
            $this->vacant = 0;
            $this->online = 0;
            $this->recalls = 0;
            $this->favorite = 0;

            $query->andWhere(['or like', 'user.name', explode(' ', $this->nameSearch)]);

        }

        if ($this->categories) {
            $query->andWhere(['user_category.category_id' => $this->categories]);
        }

        if ($this->vacant) {
            $query->leftJoin('task', 'task.executor_id=user.id AND task.status=2')
                ->andWhere('executor_id IS NULL');
        }

        if ($this->online) {
            $query->andWhere(['in', 'user.id', User::getOnlineExecutorsId()]);
        }

        if ($this->recalls) {
            $query->andWhere(['in', 'user.id', User::getExecutorsWithRecallsId()]);
        }

        if ($this->favorite) {
            $query->andWhere(['in', 'user.id', \Yii::$app->user->identity->getFavoriteExecutorsId()]);
        }

        $query->andWhere(['=', 'user_settings.hide_profile', UserSettings::SETTING_INACTIVE]);
        $query->distinct();

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5
            ],
            'sort' => [
                'defaultOrder' => [
                    'last_visit_date' => SORT_DESC,
                ]
            ],
        ]);
    }
}
