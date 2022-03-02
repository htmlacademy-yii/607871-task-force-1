<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%task}}`.
 */
class m220203_085915_add_district_column_to_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task', 'district', $this->char(150));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220203_085915_add_district_column_to_task_table cannot be reverted.\n";

        return false;
    }
}
