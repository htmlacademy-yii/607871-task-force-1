<?php

use yii\db\Migration;

/**
 * Class m220301_191521_set_default_user_settings
 */
class m220301_191521_set_default_user_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user_settings', 'new_message', 'TINYINT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('user_settings', 'task_actions', 'TINYINT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('user_settings', 'new_recall', 'TINYINT(3) UNSIGNED NOT NULL DEFAULT 0');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220301_191521_set_default_user_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220301_191521_set_default_user_settings cannot be reverted.\n";

        return false;
    }
    */
}
