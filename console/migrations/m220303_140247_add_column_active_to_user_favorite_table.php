<?php

use yii\db\Migration;

/**
 * Class m220303_140247_add_column_active_to_user_favorite_table
 */
class m220303_140247_add_column_active_to_user_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_favorite', 'active', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 1');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_favorite', 'active');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_140247_add_column_active_to_user_favorite_table cannot be reverted.\n";

        return false;
    }
    */
}
