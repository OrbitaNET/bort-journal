<?php

use yii\db\Migration;

class m260404_000002_create_poi_tables extends Migration
{
    public function safeUp()
    {
        $opts = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        // Fuel stations
        $this->createTable('{{%fuel_station}}', [
            'id'           => $this->primaryKey(),
            'name'         => $this->string(255)->notNull(),
            'lat'          => $this->decimal(10, 7)->notNull(),
            'lng'          => $this->decimal(10, 7)->notNull(),
            'address'      => $this->string(500),
            'phone'        => $this->string(50),
            'fuel_types'   => $this->string(255)->comment('e.g. ДТ, АИ-92, АИ-95, Газ'),
            'working_hours'=> $this->string(100),
            'description'  => $this->text(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ], $opts);

        // Authorities
        $this->createTable('{{%authority}}', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(255)->notNull(),
            'lat'         => $this->decimal(10, 7)->notNull(),
            'lng'         => $this->decimal(10, 7)->notNull(),
            'address'     => $this->string(500),
            'phone'       => $this->string(50),
            'website'     => $this->string(255),
            'type'        => $this->string(100)->comment('администрация, министерство, управление...'),
            'description' => $this->text(),
            'created_at'  => $this->integer()->notNull(),
            'updated_at'  => $this->integer()->notNull(),
        ], $opts);

        // Authority officials
        $this->createTable('{{%authority_official}}', [
            'id'           => $this->primaryKey(),
            'authority_id' => $this->integer()->notNull(),
            'full_name'    => $this->string(255)->notNull(),
            'position'     => $this->string(255),
            'phone'        => $this->string(50),
            'email'        => $this->string(255),
        ], $opts);
        $this->addForeignKey('fk-official-authority', '{{%authority_official}}', 'authority_id', '{{%authority}}', 'id', 'CASCADE', 'CASCADE');

        // Medical points
        $this->createTable('{{%medical_point}}', [
            'id'           => $this->primaryKey(),
            'name'         => $this->string(255)->notNull(),
            'lat'          => $this->decimal(10, 7)->notNull(),
            'lng'          => $this->decimal(10, 7)->notNull(),
            'address'      => $this->string(500),
            'phone'        => $this->string(50),
            'type'         => $this->string(100)->comment('больница, поликлиника, аптека, медпункт'),
            'working_hours'=> $this->string(100),
            'is_24h'       => $this->smallInteger()->notNull()->defaultValue(0),
            'description'  => $this->text(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ], $opts);

        // Marinas (yacht stations)
        $this->createTable('{{%marina}}', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(255)->notNull(),
            'lat'           => $this->decimal(10, 7)->notNull(),
            'lng'           => $this->decimal(10, 7)->notNull(),
            'address'       => $this->string(500),
            'phone'         => $this->string(50),
            'berths_count'  => $this->smallInteger()->unsigned(),
            'max_draft'     => $this->decimal(4, 2)->comment('max vessel draft in meters'),
            'services'      => $this->text()->comment('available services'),
            'working_hours' => $this->string(100),
            'description'   => $this->text(),
            'created_at'    => $this->integer()->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ], $opts);

        // Service stations
        $this->createTable('{{%service_station}}', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(255)->notNull(),
            'lat'           => $this->decimal(10, 7)->notNull(),
            'lng'           => $this->decimal(10, 7)->notNull(),
            'address'       => $this->string(500),
            'phone'         => $this->string(50),
            'service_types' => $this->text()->comment('types of services provided'),
            'working_hours' => $this->string(100),
            'description'   => $this->text(),
            'created_at'    => $this->integer()->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ], $opts);

        // Emergency medical stations
        $this->createTable('{{%emergency_medical}}', [
            'id'                  => $this->primaryKey(),
            'name'                => $this->string(255)->notNull(),
            'lat'                 => $this->decimal(10, 7)->notNull(),
            'lng'                 => $this->decimal(10, 7)->notNull(),
            'address'             => $this->string(500),
            'phone'               => $this->string(50),
            'is_24h'              => $this->smallInteger()->notNull()->defaultValue(0),
            'ambulance_available' => $this->smallInteger()->notNull()->defaultValue(0),
            'description'         => $this->text(),
            'created_at'          => $this->integer()->notNull(),
            'updated_at'          => $this->integer()->notNull(),
        ], $opts);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-official-authority', '{{%authority_official}}');
        $this->dropTable('{{%authority_official}}');
        $this->dropTable('{{%authority}}');
        $this->dropTable('{{%fuel_station}}');
        $this->dropTable('{{%medical_point}}');
        $this->dropTable('{{%marina}}');
        $this->dropTable('{{%service_station}}');
        $this->dropTable('{{%emergency_medical}}');
    }
}
