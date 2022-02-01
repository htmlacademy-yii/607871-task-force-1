<?php

use yii\db\Migration;

/**
 * Class m220201_133214_insert_responds
 */
class m220201_133214_insert_responds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents('console/migrations/data/replies.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_133214_insert_responds cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_133214_insert_responds cannot be reverted.\n";

        return false;
    }
    */
}
