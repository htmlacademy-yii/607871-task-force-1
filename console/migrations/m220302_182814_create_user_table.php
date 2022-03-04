<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m220302_182814_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(50)->notNull()->unique(),
            'email' => $this->string(50)->notNull()->unique(),
            'reg_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'last_visit_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'password' => $this->char(64)->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
