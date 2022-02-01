<?php

use yii\db\Migration;

/**
 * Class m220201_132148_insert_tasks
 */
class m220201_132148_insert_tasks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents('console/migrations/data/tasks.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_132148_insert_tasks cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_132148_insert_tasks cannot be reverted.\n";

        return false;
    }
    */
}
