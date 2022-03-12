<?php

use yii\db\Migration;

/**
 * Class m220303_104907_alter_avatar_column_in_user_profile_table
 */
class m220303_104907_alter_avatar_column_in_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user_profile', 'avatar', $this->char(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('user_profile', 'avatar', $this->char(100));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_104907_alter_avatar_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
