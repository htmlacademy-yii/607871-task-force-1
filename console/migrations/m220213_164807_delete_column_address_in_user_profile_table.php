<?php

use yii\db\Migration;

/**
 * Class m220213_164807_delete_column_address_in_user_profile_table
 */
class m220213_164807_delete_column_address_in_user_profile_table extends Migration
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
        echo "m220213_164807_delete_column_address_in_user_profile_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220213_164807_delete_column_address_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
