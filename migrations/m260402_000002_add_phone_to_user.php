<?php

use yii\db\Migration;

class m260402_000002_add_phone_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'phone', $this->string(20)->after('username'));
        $this->createIndex('idx-user-phone', '{{%user}}', 'phone', true);
    }

    public function safeDown()
    {
        $this->dropIndex('idx-user-phone', '{{%user}}');
        $this->dropColumn('{{%user}}', 'phone');
    }
}
