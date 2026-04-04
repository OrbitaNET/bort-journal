<?php

use yii\db\Migration;

class m260404_000008_rename_label_name_to_en extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%menu_item}}',  'label', 'label_en');
        $this->renameColumn('{{%menu_group}}', 'name',  'name_en');
    }

    public function down()
    {
        $this->renameColumn('{{%menu_item}}',  'label_en', 'label');
        $this->renameColumn('{{%menu_group}}', 'name_en',  'name');
    }
}
