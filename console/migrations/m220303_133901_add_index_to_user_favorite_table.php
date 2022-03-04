<?php

use yii\db\Migration;

/**
 * Class m220303_133901_add_index_to_user_favorite_table
 */
class m220303_133901_add_index_to_user_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('unique_chooser_chosen', 'user_favorite', ['chooser_id', 'chosen_id'], true );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('unique_chooser_chosen', 'user_favorite');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_133901_add_index_to_user_favorite_table cannot be reverted.\n";

        return false;
    }
    */
}
