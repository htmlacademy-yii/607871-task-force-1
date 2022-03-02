<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_message}}`.
 */
class m220228_131620_create_user_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_message}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer(10)->notNull()->unsigned(),
            'user_id' => $this->integer(10)->notNull()->unsigned(),
            'type' => $this->tinyInteger(3)->notNull()->unsigned(),
            'creation_date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->addForeignKey('message_ibfk_1', 'user_message', 'task_id', 'task', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('message_ibfk_2', 'user_message', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_message}}');
    }
}
