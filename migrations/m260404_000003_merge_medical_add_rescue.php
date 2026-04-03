<?php

use yii\db\Migration;

class m260404_000003_merge_medical_add_rescue extends Migration
{
    public function up()
    {
        // Add ambulance_available to medical_point (merge from emergency_medical)
        $this->addColumn('{{%medical_point}}', 'ambulance_available', $this->tinyInteger(1)->notNull()->defaultValue(0));

        // Drop emergency_medical table
        $this->dropTable('{{%emergency_medical}}');

        // Create rescue_service table (МЧС / спасатели)
        $this->createTable('{{%rescue_service}}', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(255)->notNull(),
            'type'        => $this->string(50)->notNull()->defaultValue('mchs'),
            'lat'         => $this->decimal(10, 7),
            'lng'         => $this->decimal(10, 7),
            'address'     => $this->string(255),
            'phone'       => $this->string(255),
            'is_24h'      => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'description' => $this->text(),
            'created_at'  => $this->integer(),
            'updated_at'  => $this->integer(),
        ]);
    }

    public function down()
    {
        $this->dropColumn('{{%medical_point}}', 'ambulance_available');

        $this->createTable('{{%emergency_medical}}', [
            'id'                 => $this->primaryKey(),
            'name'               => $this->string(255)->notNull(),
            'lat'                => $this->decimal(10, 7),
            'lng'                => $this->decimal(10, 7),
            'address'            => $this->string(255),
            'phone'              => $this->string(255),
            'is_24h'             => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'ambulance_available'=> $this->tinyInteger(1)->notNull()->defaultValue(0),
            'description'        => $this->text(),
            'created_at'         => $this->integer(),
            'updated_at'         => $this->integer(),
        ]);

        $this->dropTable('{{%rescue_service}}');
    }
}
