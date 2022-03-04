<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_portfolio}}`.
 */
class m220302_204048_create_user_portfolio_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_portfolio}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'file' => $this->string(255)->notNull(),
        ]);

        $this->addForeignKey('fk-user_portfolio-user_id-user-id',
            'user_portfolio',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user_portfolio-user_id-user-id',
            'user_portfolio'
        );
        $this->dropTable('{{%user_portfolio}}');
    }
}
