<?php


namespace frontend\models\forms;


use frontend\models\City;
use frontend\models\Profile;
use frontend\models\User;
use frontend\models\UserCategory;
use frontend\models\UserPortfolio;
use frontend\models\UserSettings;
use frontend\service\UserService;

class UserAccountForm extends BaseModelForm
{
    public $avatar;
    public $name;
    public $email;
    public $city_id;
    public $birth_date;
    public $description;
    public $new_categories_list;
    public $password;
    public $password_repeat;
    public $phone;
    public $skype;
    public $telegram;
    public $new_message;
    public $task_actions;
    public $new_recall;
    public $contacts_only_for_client;
    public $hide_profile;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'city_id', 'birth_date', 'description', 'new_categories_list', 'password',
                'password_repeat', 'phone', 'skype', 'telegram', 'new_message', 'task_actions', 'new_recall',
                'contacts_only_for_client', 'hide_profile'], 'safe'],
            [['email', 'name', 'city_id'], 'required', 'message' => 'Поле должно быть заполнено'],
            [['email', 'name', 'description', 'password', 'password_repeat', 'phone', 'skype', 'telegram'], 'trim'],
            [['new_message', 'task_actions', 'new_recall', 'hide_profile', 'contacts_only_for_client'], 'integer', 'min' => 0, 'max' => 1],
            [['name', 'email'], 'string', 'min' => 5, 'max' => 50, 'tooShort' => "Не меньше {min} символов",
                'tooLong' => 'Не больше {max} символов'],
            [['password', 'password_repeat'], 'string', 'min' => 8, 'max' => 64, 'tooShort' => "Длина пароля от {min} символов",
                'tooLong' => 'Длина пароля до {max} символов'],
            ['email', 'email', 'message' => 'Введите валидный адрес электронной почты'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat', 'message' => 'Пароли должны совпадать'],
            ['birth_date', 'datetime', 'format' => 'dd.MM.yyyy', 'max' => date('d.m.Y', strtotime('-14 years', time())),
                'strictDateFormat' => true, 'message' => 'Введите дату в формате ДД.ММ.ГГГГ'],
            ['city_id', 'integer'],
            [['description'], 'string'],
            [['avatar'], 'string', 'max' => 255],
            [['phone'], 'match', 'pattern' => '/^[\d]{11}/i', 'message' => 'Указан неверный номер телефона'],
            [['skype', 'telegram'], 'string', 'max' => 50],
            [['name', 'email'], 'isUserAttributeUnique'],
            [['phone', 'skype', 'telegram'], 'isProfileAttributeUnique'],
            [['city_id'], 'exist', 'skipOnError' => false, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id'], 'message' => 'Укажите город, чтобы находить подходящие задачи'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Электронная почта',
            'name' => 'Ваше имя',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'birth_date' => 'День рождения',
            'description' => 'Информация о себе',
            'city_id' => 'Город',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'skype' => 'Skype',
            'telegram' => 'Телеграм',
            'new_message' => 'Новое сообщение',
            'task_actions' => 'Действия по заданию',
            'new_recall' => 'Новый отзыв',
            'hide_profile' => 'Не показывать мой профиль',
            'contacts_only_for_client' => 'Показывать мои контакты только заказчику',
        ];
    }

    /**
     * Метод заливает данные из формы "Мой профиль" в соответствующие модели и сохраняет их в базе данных.
     * @param UploadFilesForm $uploadFilesModel
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveFields(UploadFilesForm $uploadFilesModel)
    {
        $user = $this->saveUserFields();
        $profile = $this->saveProfileFields($user, $uploadFilesModel);
        $userSettings = $this->saveUserSettingsFields($user);

        if (!$user->errors && !$profile->errors && !$userSettings->errors) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $user->save();
                $profile->save();
                $userSettings->save();
                $this->saveUserCategoriesFields($user);
                $this->saveUserPortfolioFields($user, $uploadFilesModel);
                $transaction->commit();
                \Yii::$app->session->set('city_id', $profile->city_id);
                return true;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                return false;
            }
        }
        return false;
    }

    /**
     * Метод заливает данные из формы "Мой профиль" в модель User и валидирует ее.
     * @return User|null
     * @throws \yii\base\Exception
     */
    private function saveUserFields()
    {
        $user = User::findOne(\Yii::$app->user->identity->id);
        $user->scenario = User::SCENARIO_UPDATE_USER;
        $user->name = $this->name;
        $user->email = $this->email;
        ($this->password) ? $user->password_hash = \Yii::$app->security->generatePasswordHash($this->password) : false;
        $user->validate();
        return $user;
    }

    /**
     * Метод заливает данные из формы "Мой профиль" в модель Profile и валидирует ее.
     * @param User $user
     * @param UploadFilesForm $uploadFilesModel
     * @return \frontend\models\Profile
     */
    private function saveProfileFields(User $user, UploadFilesForm $uploadFilesModel)
    {
        $profile = $user->profile;
        $profile->description = $this->description;
        $profile->birth_date = $this->birth_date ? date('Y-m-d', strtotime($this->birth_date)) : null;
        $profile->city_id = $this->city_id;
        $profile->phone = $this->phone ?: null;
        $profile->skype = $this->skype ?: null;
        $profile->telegram = $this->telegram ?: null;
        $profile->avatar = $this->saveUserAvatar($profile, $uploadFilesModel);
        $profile->validate();
        return $profile;
    }

    /**
     * Метод переносит файл с аватаром пользователя из временной папки и папку на сервере и переименовавывает его.
     * @param Profile $profile
     * @param UploadFilesForm $uploadFilesModel
     * @return string|null
     */
    private function saveUserAvatar(Profile $profile, UploadFilesForm $uploadFilesModel)
    {
        if ($uploadFilesModel->avatar) {
            $newAvatarFileName = UploadFilesForm::uploadFile($uploadFilesModel->avatar);
            return $newAvatarFileName ? (\Yii::$app->params['defaultUploadDirectory'] . $newAvatarFileName) : null;
        }
        return $profile->avatar;
    }

    /**
     * Метод заливает данные из формы "Мой профиль" в модель UserSettings и валидирует ее.
     * @param User $user
     * @return UserSettings
     */
    private function saveUserSettingsFields(User $user)
    {
        $userSettings = $user->userSettings;

        if (!isset($userSettings)) {
            $userSettings = new UserSettings();
            $userSettings->user_id = $user->id;
        }

        $userSettings->new_message = $this->new_message ?? UserSettings::SETTING_INACTIVE;
        $userSettings->task_actions = $this->task_actions ?? UserSettings::SETTING_INACTIVE;
        $userSettings->new_recall = $this->new_recall ?? UserSettings::SETTING_INACTIVE;
        $userSettings->contacts_only_for_client = $this->contacts_only_for_client ?? UserSettings::SETTING_INACTIVE;
        $userSettings->hide_profile = $this->hide_profile ?? UserSettings::SETTING_INACTIVE;
        $userSettings->validate();
        return $userSettings;
    }

    /**
     * Метод заливает данные из формы "Мой профиль" в модели UserCategory и сохраняет их.
     * @param User $user
     * @throws \yii\db\Exception
     */
    public function saveUserCategoriesFields(User $user)
    {
        UserService::deactivateAllUserCategories($user);

        if ($this->new_categories_list) {
            foreach ($this->new_categories_list as $key => $categoryId) {
                $userCategory = UserService::getUserCategory($user, $categoryId);
                if (!$userCategory) {
                    $userCategory = new UserCategory();
                    $userCategory->user_id = $user->id;
                    $userCategory->category_id = $categoryId;
                }
                $userCategory->active = UserCategory::STATUS_ACTIVE;
                $userCategory->save();
            }
        }
    }

    /**
     * Метод переносит файлы с портфолио пользователя (пртмерами работ) в папку на сервере,
     * заливает данные из формы "Мой профиль" в модели UserPortfolio и сохраняет их в базе данных.
     * @param UploadFilesForm $uploadFilesModel
     * @param User $user
     * @throws \yii\db\Exception
     */
    private function saveUserPortfolioFields(User $user, UploadFilesForm $uploadFilesModel): void
    {
        if ($uploadFilesModel->files) {
            UserService::deleteUserPortfolio($user);
        }

        foreach ($uploadFilesModel->files as $file) {
            $newFileName = UploadFilesForm::uploadFile($file); //загрузка файла из временной папки в uploads
            if ($newFileName) {
                $userPortfolio = new UserPortfolio();
                $userPortfolio->user_id = $user->id;
                $userPortfolio->file = \Yii::$app->params['defaultUploadDirectory'] . $newFileName;
                $userPortfolio->save();
            }
        }
    }
}