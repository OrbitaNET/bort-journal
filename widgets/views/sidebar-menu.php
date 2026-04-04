<?php
/** @var app\models\MenuGroup[] $groups */
/** @var app\models\MenuItem[] $ungrouped */

use yii\helpers\Html;
use yii\helpers\Url;

$currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
?>
<nav class="sidebar-menu">
    <div class="sidebar-header">
        <span class="sidebar-title"><?= Yii::t('app', 'Navigation') ?></span>
        <button type="button"
                class="btn-close btn-close-white sidebar-close-btn d-md-none"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
    </div>

    <?php foreach ($groups as $group): ?>
        <?php if ($group->items): ?>
        <div class="sidebar-group">
            <div class="sidebar-group-title"><?= Yii::t('app', $group->name) ?></div>
            <?php foreach ($group->items as $item): ?>
                <?php $url = Url::to($item->getUrl()); $active = $currentRoute === $item->controller . '/' . $item->action; ?>
                <a href="<?= $url ?>" class="sidebar-link <?= $active ? 'active' : '' ?>">
                    <?= Yii::t('app', $item->label) ?>
                </a>
            <?php endforeach ?>
        </div>
        <?php endif ?>
    <?php endforeach ?>

    <?php foreach ($ungrouped as $item): ?>
        <?php $url = Url::to($item->getUrl()); $active = $currentRoute === $item->controller . '/' . $item->action; ?>
        <a href="<?= $url ?>" class="sidebar-link <?= $active ? 'active' : '' ?>">
            <?= Yii::t('app', $item->label) ?>
        </a>
    <?php endforeach ?>
</nav>
