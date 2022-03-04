<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile}}`.
 */
class m220302_185858_create_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'birth_date' => $this->timestamp(),
            'description' => $this->text(),
            'avatar' => $this->string(100),
            'city_id' => $this->integer()->unsigned(),
            'address' => $this->string(255),
            'phone' => $this->string(12)->unique(),
            'skype' => $this->string(50)->unique(),
            'other' => $this->string(50)->unique(),
        ]);

        $this->addForeignKey(
            'fk-profile-user_id-user-id',
            'user_profile',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE');

        $this->addForeignKey(
            'fk-profile-city_id-city-id',
            'user_profile',
            'city_id',
            'city',
            'id',
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-profile-user_id-user-id',
            'user_profile'
        );
        $this->dropForeignKey(
            'fk-profile-city_id-city-id',
            'user_profile'
        );
        $this->dropTable('{{%user_profile}}');
    }
}
