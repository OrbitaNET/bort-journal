<?php

use yii\db\Migration;

class m260402_000001_create_users_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id'                => $this->primaryKey(),
            'username'          => $this->string(64)->notNull()->unique(),
            'auth_key'          => $this->string(32)->notNull()->defaultValue(''),
            'access_token'      => $this->string(40)->unique(),
            'telegram_id'       => $this->bigInteger()->unique(),
            'telegram_username' => $this->string(64),
            'auth_code'         => $this->string(4),
            'auth_code_expires' => $this->integer(),
            'status'            => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at'        => $this->integer()->notNull(),
            'updated_at'        => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-user-telegram_id', '{{%user}}', 'telegram_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
