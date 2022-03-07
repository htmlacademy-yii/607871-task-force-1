<?php

use yii\db\Migration;

/**
 * Class m220307_163039_add_index_to_user_category_table
 */
class m220307_163039_add_index_to_user_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('unique_category_id_user_id','user_category', ['user_id', 'category_id'], true);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('unique_category_id_user_id','user_category');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220307_163039_add_index_in_user_category_table cannot be reverted.\n";

        return false;
    }
    */
}
