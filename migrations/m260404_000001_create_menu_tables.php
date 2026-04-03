<?php

use yii\db\Migration;

class m260404_000001_create_menu_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%menu_group}}', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string(128)->notNull(),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('{{%menu_item}}', [
            'id'         => $this->primaryKey(),
            'group_id'   => $this->integer()->null(),
            'label'      => $this->string(128)->notNull(),
            'controller' => $this->string(64)->notNull(),
            'action'     => $this->string(64)->notNull()->defaultValue('index'),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_active'  => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-menu_item-group_id',
            '{{%menu_item}}', 'group_id',
            '{{%menu_group}}', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-menu_item-group_id', '{{%menu_item}}');
        $this->dropTable('{{%menu_item}}');
        $this->dropTable('{{%menu_group}}');
    }
}
