<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_category}}`.
 */
class m220302_205201_create_user_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_category}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'active' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk-user_category-category_id-category-id',
            'user_category',
            'category_id',
            'category',
            'id',
        );

        $this->addForeignKey(
            'fk-user_category-user_id-user-id',
            'user_category',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user_category-category_id-category-id',
            'user_category'
        );
        $this->dropForeignKey(
            'fk-user_category-user_id-user-id',
            'user_category'
        );
        $this->dropTable('{{%user_category}}');
    }
}
