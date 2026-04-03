<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\MenuGroup;
use app\models\MenuItem;

class SidebarMenu extends Widget
{
    public function run()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isSuperadmin()) {
            return '';
        }

        $groups = MenuGroup::find()
            ->with(['items' => function ($q) {
                $q->where(['is_active' => 1])->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC]);
            }])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        $ungrouped = MenuItem::find()
            ->where(['group_id' => null, 'is_active' => 1])
            ->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC])
            ->all();

        return $this->render('sidebar-menu', compact('groups', 'ungrouped'));
    }
}
