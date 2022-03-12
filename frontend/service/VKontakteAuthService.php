<?php


namespace frontend\service;


use frontend\models\Auth;
use frontend\models\City;
use frontend\models\Profile;
use frontend\models\User;
use Throwable;
use Yii;

class VKontakteAuthService
{
    public $client;
    public $attributes;

    public function __construct($client)
    {
        $this->client = $client;
        $this->attributes = $client->getUserAttributes();
    }

    /**
     * Метод, отвечающий за регистрацию пользователя на основании данных, полученных от VKontakte.
     * @throws \yii\base\Exception
     */
    public function register()
    {
        if (isset($this->attributes['email']) && !User::find()->where(['email' => $this->attributes['email']])->exists()) {
            $user = $this->loadUserVKAttributes();
            $profile = $this->loadProfileVKAttributes();

            if (!$user->errors && !$profile->errors) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $user->save();
                    $profile->user_id = $user->getPrimaryKey();
                    $profile->save();
                    $auth = new Auth([
                        'user_id' => $user->getPrimaryKey(),
                        'source' => $this->client->getId(),
                        'source_id' => (string)$this->attributes['id'],
                    ]);
                    $auth->save();
                    $transaction->commit();
                    Yii::$app->user->login($user);
                    Yii::$app->session->set('city_id', Yii::$app->user->identity->profile->city_id);

                } catch (Throwable $e) {
                    $transaction->rollBack();
                }
            }
        }
    }

    /**
     * Загрузка данных, полученных из ответа VKontakte, в модель пользовательского профиля.
     * @return Profile
     */
    private function loadProfileVKAttributes(): Profile
    {
        $profile = new Profile(['scenario' => Profile::SCENARIO_DEFAULT]);

        if (isset($this->attributes['city']['title'])) {
            $city = City::find()->where(['name' => $this->attributes['city']['title']])->one();
            if ($city) {
                $profile->city_id = $city->id;
            }
        }

        if (isset($this->attributes['photo'])) {
            $profile->avatar = $this->attributes['photo'];
        }

        if (isset($this->attributes['bdate'])) {
            $profile->birth_date = date('Y-m-d', strtotime($this->attributes['bdate']));
        }

        return $profile;
    }

    /**
     * Загрузка данных, полученных из ответа VKontakte, в модель пользователя.
     * @return User
     * @throws \yii\base\Exception
     */
    private function loadUserVKAttributes(): User
    {
        $password = Yii::$app->security->generateRandomString(15);
        $user = new User(['scenario' => User::SCENARIO_CREATE_USER]);
        $user->password_hash = Yii::$app->security->generatePasswordHash($password);

        if (isset($this->attributes['email'])) {
            $user->email = $this->attributes['email'];
        }

        if (isset($this->attributes['last_name'], $this->attributes['first_name'])) {
            $user->name = implode(' ', array($this->attributes['last_name'], $this->attributes['first_name']));
        }

        return $user;
    }


}