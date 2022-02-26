<?php

use yii\db\Migration;

/**
 * Class m220131_123459_init
 */
class m220131_123459_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE DATABASE taskmaster");
        $sql = file_get_contents('console/migrations/data/taskmaster_bd.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220131_123459_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220131_123459_init cannot be reverted.\n";

        return false;
    }
    */
}
