<?php

use yii\db\Migration;

class m260404_000004_create_map_polygon extends Migration
{
    public function up()
    {
        $this->createTable('{{%map_polygon}}', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(255)->notNull(),
            'coordinates' => $this->text()->notNull(),
            'color'       => $this->string(20)->notNull()->defaultValue('#3388ff'),
            'created_at'  => $this->integer(),
            'updated_at'  => $this->integer(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%map_polygon}}');
    }
}
