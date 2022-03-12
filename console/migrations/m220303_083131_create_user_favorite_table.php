<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_favorite}}`.
 */
class m220303_083131_create_user_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_favorite}}', [
            'id' => $this->primaryKey(),
            'chooser_id' => $this->integer()->unsigned()->notNull(),
            'chosen_id' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->createIndex(
            'unique_chooser_chosen',
            'user_favorite',
            ['chooser_id', 'chosen_id'],
            true
        );

        $this->addForeignKey(
            'fk-user_favorite-chooser_id-user-id',
            'user_favorite',
            'chooser_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_favorite-chosen_id-user-id',
            'user_favorite',
            'chosen_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user_favorite-chooser_id-user-id',
            'user_favorite'
        );
        $this->dropForeignKey(
            'fk-user_favorite-chosen_id-user-id',
            'user_favorite'
        );
        $this->dropIndex(
            'unique_chooser_chosen',
            'user_favorite'
        );
        $this->dropTable('{{%user_favorite}}');
    }
}
