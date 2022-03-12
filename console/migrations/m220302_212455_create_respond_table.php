<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%respond}}`.
 */
class m220302_212455_create_respond_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%respond}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'description' => $this->text()->notNull(),
            'rate' => $this->integer()->unsigned()->notNull(),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1),
            'creation_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')

        ]);

        $this->addForeignKey(
            'fk-respond-user_id-user-id',
            'respond',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-respond-task_id-task-id',
            'respond',
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
            'fk-respond-user_id-user-id',
            'respond'
        );
        $this->dropForeignKey(
            'fk-respond-task_id-task-id',
            'respond'
        );
        $this->dropTable('{{%respond}}');
    }
}
