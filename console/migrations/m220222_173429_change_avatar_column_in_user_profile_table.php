<?php

use yii\db\Migration;

/**
 * Class m220222_173429_change_avatar_column_in_user_profile_table
 */
class m220222_173429_change_avatar_column_in_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user_profile', 'avatar', 'string');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220222_173429_change_avatar_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220222_173429_change_avatar_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
