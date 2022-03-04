<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task_files}}`.
 */
class m220302_204653_create_task_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task_files}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'url' => $this->string(255)->notNull(),
            'name' => $this->string(100)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-task_files-task_id-task-id',
            'task_files',
            'task_id',
            'task',
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
            'fk-task_files-task_id-task-id',
            'task_files'
        );
        $this->dropTable('{{%task_files}}');
    }
}
