<?php

use yii\db\Migration;

/**
 * Class m220303_100704_delete_column_address_in_user_profile_table
 */
class m220303_100704_delete_column_address_in_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user_profile', 'address');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('user_profile', 'address', $this->string(255));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_100704_delete_column_address_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
