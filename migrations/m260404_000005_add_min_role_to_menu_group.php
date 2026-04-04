<?php

use yii\db\Migration;

class m260404_000005_add_min_role_to_menu_group extends Migration
{
    public function up()
    {
        $this->addColumn('{{%menu_group}}', 'min_role', $this->string(20)->notNull()->defaultValue('user')->after('sort_order'));
    }

    public function down()
    {
        $this->dropColumn('{{%menu_group}}', 'min_role');
    }
}
