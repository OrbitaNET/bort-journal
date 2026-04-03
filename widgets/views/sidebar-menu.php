<?php
/** @var app\models\MenuGroup[] $groups */
/** @var app\models\MenuItem[] $ungrouped */

use yii\helpers\Html;
use yii\helpers\Url;

$currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
?>
<nav class="sidebar-menu">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <span class="sidebar-title">Навигация</span>
        <?= Html::a('⚙', ['/menu/index'], ['class' => 'sidebar-settings-btn', 'title' => 'Настроить меню']) ?>
    </div>

    <?php foreach ($groups as $group): ?>
        <?php if ($group->items): ?>
        <div class="sidebar-group">
            <div class="sidebar-group-title"><?= Html::encode($group->name) ?></div>
            <?php foreach ($group->items as $item): ?>
                <?php $url = Url::to($item->getUrl()); $active = $currentRoute === $item->controller . '/' . $item->action; ?>
                <a href="<?= $url ?>" class="sidebar-link <?= $active ? 'active' : '' ?>">
                    <?= Html::encode($item->label) ?>
                </a>
            <?php endforeach ?>
        </div>
        <?php endif ?>
    <?php endforeach ?>

    <?php foreach ($ungrouped as $item): ?>
        <?php $url = Url::to($item->getUrl()); $active = $currentRoute === $item->controller . '/' . $item->action; ?>
        <a href="<?= $url ?>" class="sidebar-link <?= $active ? 'active' : '' ?>">
            <?= Html::encode($item->label) ?>
        </a>
    <?php endforeach ?>
</nav>
