<?php

use yii\db\Migration;

/**
 * Class m220303_100759_rename_column_other_in_user_profile_table
 */
class m220303_100759_rename_column_other_in_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('user_profile', 'other', 'telegram');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('user_profile', 'telegram', 'other');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_100759_rename_column_other_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
