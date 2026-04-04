<?php

use yii\db\Migration;

class m260404_000006_create_application_tables extends Migration
{
    public function up()
    {
        $this->createTable('{{%application}}', [
            'id'            => $this->primaryKey(),
            'creator_id'    => $this->integer()->notNull(),
            'status'        => $this->string(20)->notNull()->defaultValue('draft'),
            'start_lat'     => $this->decimal(10, 7)->notNull(),
            'start_lng'     => $this->decimal(10, 7)->notNull(),
            'start_address' => $this->string(512)->null(),
            'end_lat'       => $this->decimal(10, 7)->notNull(),
            'end_lng'       => $this->decimal(10, 7)->notNull(),
            'end_address'   => $this->string(512)->null(),
            'notes'         => $this->text()->null(),
            'created_at'    => $this->integer()->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_application_creator',
            '{{%application}}', 'creator_id',
            '{{%user}}', 'id', 'CASCADE'
        );

        $this->createTable('{{%application_waypoint}}', [
            'id'             => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'poi_type'       => $this->string(50)->notNull(),
            'poi_id'         => $this->integer()->notNull(),
            'sort_order'     => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk_waypoint_application',
            '{{%application_waypoint}}', 'application_id',
            '{{%application}}', 'id', 'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_waypoint_application', '{{%application_waypoint}}');
        $this->dropTable('{{%application_waypoint}}');
        $this->dropForeignKey('fk_application_creator', '{{%application}}');
        $this->dropTable('{{%application}}');
    }
}
