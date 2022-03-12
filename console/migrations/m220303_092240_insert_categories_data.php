<?php

use yii\db\Migration;

/**
 * Class m220303_092240_insert_categories_data
 */
class m220303_092240_insert_categories_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents(__DIR__ . '/data/categories.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('category');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_092240_insert_categories_data cannot be reverted.\n";

        return false;
    }
    */
}
