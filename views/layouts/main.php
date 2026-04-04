<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\SidebarMenu;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$isGuest     = Yii::$app->user->isGuest;
$isSuperadmin = !$isGuest && Yii::$app->user->identity->isSuperadmin();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> — <?= $appTitle ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody() ?>

<?php
$currentLang = Yii::$app->language;
$appTitle = $currentLang === 'ru' ? 'БортЖурнал' : 'CaptainBook';
?>
<?php if (!$isGuest): ?>
<nav class="navbar navbar-dark bg-dark px-3">
    <?= Html::a($appTitle, Yii::$app->homeUrl, ['class' => 'navbar-brand mb-0']) ?>
    <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
        <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'm-0']) ?>
        <?= Html::submitButton(Yii::t('app', 'Logout'), ['class' => 'btn btn-sm btn-outline-light']) ?>
        <?= Html::endForm() ?>
        <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-light' : 'btn-outline-light')]) ?>
        <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-light' : 'btn-outline-light')]) ?>
    </div>
</nav>
<?php else: ?>
<div style="position:fixed;top:12px;right:16px;z-index:1000;">
    <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
    <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
</div>
<?php endif ?>

<main class="flex-grow-1 d-flex align-items-<?= $isGuest ? 'center' : 'start pt-4' ?> justify-content-center">
    <?php if ($isSuperadmin): ?>
    <div class="container-fluid">
        <div class="d-flex gap-4">
            <div class="flex-grow-1 min-w-0">
                <?= $content ?>
            </div>
            <div class="sidebar-menu-wrap flex-shrink-0">
                <?= SidebarMenu::widget() ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="<?= $isGuest ? 'w-100' : 'container' ?>">
        <?= $content ?>
    </div>
    <?php endif ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
