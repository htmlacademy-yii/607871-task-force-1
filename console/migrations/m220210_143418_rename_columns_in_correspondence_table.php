<?php

use yii\db\Migration;

/**
 * Class m220210_143418_rename_columns_in_correspondence_table
 */
class m220210_143418_rename_columns_in_correspondence_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('correspondence', 'content', 'message');
        $this->renameColumn('correspondence', 'creation_date', 'published_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220210_143418_rename_columns_in_correspondence_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220210_143418_rename_columns_in_correspondence_table cannot be reverted.\n";

        return false;
    }
    */
}
