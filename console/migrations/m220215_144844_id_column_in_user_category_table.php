<?php

use yii\db\Migration;

/**
 * Class m220215_144844_id_column_in_user_category_table
 */
class m220215_144844_id_column_in_user_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_category','id', 'integer not null AUTO_INCREMENT, add PRIMARY KEY (`id`)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220215_144844_id_column_in_user_category_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_144844_id_column_in_user_category_table cannot be reverted.\n";

        return false;
    }
    */
}
