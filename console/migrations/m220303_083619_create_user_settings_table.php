<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_settings}}`.
 */
class m220303_083619_create_user_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_settings}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'new_message' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'task_actions' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'new_recall' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'hide_profile' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'contacts_only_for_client' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk-user_settings-user_id-user-id',
            'user_settings',
            'user_id',
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
            'fk-user_settings-user_id-user-id',
            'user_settings'
        );
        $this->dropTable('{{%user_settings}}');
    }
}
