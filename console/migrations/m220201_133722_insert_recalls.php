<?php

use yii\db\Migration;

/**
 * Class m220201_133722_insert_recalls
 */
class m220201_133722_insert_recalls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents('console/migrations/data/opinions.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_133722_insert_recalls cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_133722_insert_recalls cannot be reverted.\n";

        return false;
    }
    */
}
