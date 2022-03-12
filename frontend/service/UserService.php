<?php


namespace frontend\service;


use frontend\models\User;
use frontend\models\UserCategory;
use frontend\models\UserFavorite;

class UserService
{
    /**
     * Метод удаляет из базы данных портфолио пользователя (примеры работ).
     * @param User $user
     * @return bool
     */
    public static function deleteUserPortfolio(User $user)
    {
        try {
            \Yii::$app->db
                ->createCommand('DELETE FROM user_portfolio WHERE user_id=:user_id', ['user_id' => $user->id])
                ->execute();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }


    /**
     * Метод добавляет и активирует пользователя в список избранных, если его там нет, либо если он деактивирован.
     * Если же он уже есть в списке избранных и актвирован, то метод его деактивирует.
     * @param int $chosenUserId
     * @return bool
     */
    public static function switchUserFavorite(int $chosenUserId)
    {
        $currentUserId = \Yii::$app->user->id;
        $userFavorite = UserFavorite::find()
            ->where('chooser_id =:chooser_id', [':chooser_id' => $currentUserId])
            ->andWhere('chosen_id =:chosen_id', [':chosen_id' => $chosenUserId])->one();

        if (!$userFavorite) {
            $userFavorite = new UserFavorite([
                'chooser_id' => $currentUserId,
                'chosen_id' => $chosenUserId,
            ]);
        }

        if ($userFavorite && $userFavorite->active === UserFavorite::STATUS_ACTIVE) {
            $userFavorite->active = UserFavorite::STATUS_INACTIVE;
        } else {
            $userFavorite->active = UserFavorite::STATUS_ACTIVE;
        }

        return $userFavorite->save();
    }

    /**
     * Метод деактивирует все категории пользователя в качестве потенциального исполнителя.
     * @param User $user
     * @throws \yii\db\Exception
     */
    public static function deactivateAllUserCategories(User $user)
    {
        \Yii::$app->db
            ->createCommand('UPDATE user_category SET active =:active WHERE user_id=:user_id', [
                'active' => UserCategory::STATUS_INACTIVE,
                'user_id' => $user->id,
            ])->execute();
    }

    /**
     * Метод проверяет, добавлен ли пользователь с указанным id в список избранных у конкретного пользователя.
     * @param int $chosenUserId
     * @return bool
     */
    public static function checkIsUserFavorite(int $chosenUserId): bool
    {
        $currentUserId = \Yii::$app->user->identity->id;
        return UserFavorite::find()
            ->where('chooser_id =:chooser_id', [':chooser_id' => $currentUserId])
            ->andWhere('chosen_id =:chosen_id', [':chosen_id' => $chosenUserId])
            ->andWhere('active=1')
            ->exists();
    }


    /**
     * Метод проверяет, специализируется ли в настоящее время пользователь на конкретной категории в качестве
     * потенциального исполнителя.
     * @param User $user
     * @param int $categoryId
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function checkUserCategory(User $user, int $categoryId)
    {
        return $user->getCategories()->where(['category.id' => $categoryId])->exists();
    }

    /**
     * Метод проверяет, была ли уже установлена связь между пользователем и категорией
     * (не важно активна эта связь сейчас или нет). Если такая связь существует, возвращается модель этой связи.
     * @param User $user
     * @param int $categoryId
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getUserCategory(User $user, int $categoryId)
    {
        return UserCategory::find()->where(['user_id' => $user->id, 'category_id' => $categoryId])->one();
    }

    /**
     * Метод, отвечающий за валидацию пароля пользователя.
     * @param User $user
     * @param $password
     * @return bool
     */
    public static function validatePassword(User $user, $password)
    {
        return \Yii::$app->security->validatePassword($password, $user->password_hash);
    }

    /**
     * Метод проверят, установлена ли конкретная настройка у пользователя.
     * @param User $user
     * @param string $setting
     * @return bool
     */
    public static function checkUserSetting(User $user, string $setting)
    {
        if ($user->userSettings) {
            return (bool)$user->userSettings->$setting;
        }
        return false;
    }

    /**
     * Метод возвращает адрес файла с аватаром пользователя, либо, при его отсутствии, дефолтный аватар.
     * @param User $user
     * @return mixed|string
     */
    public static function getAvatar(User $user)
    {
        $defaultAvatar = \Yii::$app->params['defaultAvatarPath'] ?? '';
        return ($user->profile->avatar) ?: $defaultAvatar;
    }
}