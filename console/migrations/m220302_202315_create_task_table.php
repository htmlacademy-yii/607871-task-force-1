<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 */
class m220302_202315_create_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(100)->notNull(),
            'description' => $this->text()->notNull(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'client_id' => $this->integer()->unsigned()->notNull(),
            'executor_id' => $this->integer()->unsigned(),
            'budget' => $this->integer()->unsigned(),
            'status' => $this->tinyInteger()->unsigned()->notNull(),
            'due_date' => $this->timestamp()->notNull(),
            'creation_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'city_id' => $this->integer()->unsigned(),
            'address' => $this->string(255),
            'latitude' => $this->decimal(10,7),
            'longitude' =>  $this->decimal(10,7),
        ]);

        $this->addForeignKey('fk-task-client_id-user-id',
            'task',
            'client_id',
            'user',
            'id',
        );

        $this->addForeignKey('fk-task-executor_id-user-id',
            'task',
            'executor_id',
            'user',
            'id',
        );

        $this->addForeignKey('fk-task-city_id-city-id',
            'task',
            'city_id',
            'city',
            'id',
        );

        $this->addForeignKey('fk-task-category_id-category-id',
            'task',
            'category_id',
            'category',
            'id',
            );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-task-client_id-user-id',
            'task'
        );
        $this->dropForeignKey(
            'fk-task-executor_id-user-id',
            'task'
        );
        $this->dropForeignKey(
            'fk-task-city_id-city-id',
            'task'
        );
        $this->dropForeignKey(
            'fk-task-category_id-category-id',
            'task'
        );
        $this->dropTable('{{%task}}');
    }
}
