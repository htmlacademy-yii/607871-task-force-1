<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%correspondence}}`.
 */
class m220303_082045_create_correspondence_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%correspondence}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'content' => $this->text()->notNull(),
            'creation_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-correspondence-task_id-task-id',
            'correspondence',
            'task_id',
            'task',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-correspondence-user_id-user-id',
            'correspondence',
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
            'fk-correspondence-task_id-task-id',
            'correspondence'
        );
        $this->dropForeignKey(
            'fk-correspondence-user_id-user-id',
            'correspondence'
        );
        $this->dropTable('{{%correspondence}}');
    }
}
