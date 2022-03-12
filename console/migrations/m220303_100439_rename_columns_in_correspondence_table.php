<?php

use yii\db\Migration;

/**
 * Class m220303_100439_rename_columns_in_correspondence_table
 */
class m220303_100439_rename_columns_in_correspondence_table extends Migration
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
        $this->renameColumn('correspondence',  'message', 'content');
        $this->renameColumn('correspondence',  'published_at', 'creation_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_100439_rename_columns_in_correspondence_table cannot be reverted.\n";

        return false;
    }
    */
}
