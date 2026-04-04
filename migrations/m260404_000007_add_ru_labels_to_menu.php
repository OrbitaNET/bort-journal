<?php

use yii\db\Migration;

class m260404_000007_add_ru_labels_to_menu extends Migration
{
    public function up()
    {
        $this->addColumn('{{%menu_group}}', 'name_ru', $this->string(128)->null()->after('name'));
        $this->addColumn('{{%menu_item}}',  'label_ru', $this->string(128)->null()->after('label'));
    }

    public function down()
    {
        $this->dropColumn('{{%menu_group}}', 'name_ru');
        $this->dropColumn('{{%menu_item}}',  'label_ru');
    }
}
