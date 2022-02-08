<?php

use yii\db\Migration;

/**
 * Class m220207_143831_change_birth_date_column_in_profile_table
 */
class m220207_143831_change_birth_date_column_in_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user_profile','birth_date', 'date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_143831_change_birth_date_column_in_profile_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_143831_change_birth_date_column_in_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
