<?php

use yii\db\Migration;

/**
 * Class m220201_130816_insert_profiles
 */
class m220201_130816_insert_profiles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents('console/migrations/data/profiles.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_130816_insert_profiles cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_130816_insert_profiles cannot be reverted.\n";

        return false;
    }
    */
}
