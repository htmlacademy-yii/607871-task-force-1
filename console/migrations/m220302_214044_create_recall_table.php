<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%recall}}`.
 */
class m220302_214044_create_recall_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%recall}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'description' => $this->text(),
            'rating' => $this->tinyInteger(1)->unsigned(),
            'creation_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'task_id-unique',
            'recall',
            'task_id',
            true
        );

        $this->addForeignKey(
            'fk-recall-task_id-task-id',
            'recall',
            'task_id',
            'task',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-recall-task_id-task-id',
            'recall'
        );
        $this->dropTable('{{%recall}}');
    }
}
