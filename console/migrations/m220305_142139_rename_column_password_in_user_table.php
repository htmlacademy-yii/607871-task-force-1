<?php

use yii\db\Migration;

/**
 * Class m220305_142139_rename_column_password_in_user_table
 */
class m220305_142139_rename_column_password_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('user', 'password', 'password_hash');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('user',  'password_hash','password');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220305_142139_rename_column_password_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
