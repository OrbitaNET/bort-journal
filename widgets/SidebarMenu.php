<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\MenuGroup;
use app\models\MenuItem;
use app\models\User;

class SidebarMenu extends Widget
{
    // Actions considered read-only — shown to all roles including 'user'
    const READONLY_ACTIONS = ['index', 'view'];

    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return '';
        }

        $identity = Yii::$app->user->identity;
        $isSuperadmin = $identity->isSuperadmin();
        $isAdmin      = $identity->isAdmin();
        $isUser       = !$isSuperadmin && !$isAdmin;

        // Which group min_roles are accessible to this user
        if ($isSuperadmin) {
            $allowedRoles = [MenuGroup::MIN_ROLE_USER, MenuGroup::MIN_ROLE_ADMIN, MenuGroup::MIN_ROLE_SUPERADMIN];
        } elseif ($isAdmin) {
            $allowedRoles = [MenuGroup::MIN_ROLE_USER, MenuGroup::MIN_ROLE_ADMIN];
        } else {
            $allowedRoles = [MenuGroup::MIN_ROLE_USER];
        }

        $groups = MenuGroup::find()
            ->where(['min_role' => $allowedRoles])
            ->with(['items' => function ($q) use ($isUser) {
                $q->where(['is_active' => 1]);
                if ($isUser) {
                    $q->andWhere(['action' => self::READONLY_ACTIONS]);
                }
                $q->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC]);
            }])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        $ungroupedQuery = MenuItem::find()->where(['group_id' => null, 'is_active' => 1]);
        if ($isUser) {
            $ungroupedQuery->andWhere(['action' => self::READONLY_ACTIONS]);
        }
        $ungrouped = $ungroupedQuery->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC])->all();

        return $this->render('sidebar-menu', compact('groups', 'ungrouped'));
    }
}
