<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\SidebarMenu;
use yii\bootstrap5\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'mobile-web-app-capable', 'content' => 'yes']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-capable', 'content' => 'yes']);
$this->registerMetaTag(['name' => 'theme-color', 'content' => '#212529']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$isGuest      = Yii::$app->user->isGuest;
$isSuperadmin = !$isGuest && Yii::$app->user->identity->isSuperadmin();
$currentLang  = Yii::$app->language;
$appTitle     = $currentLang === 'ru' ? 'БортЖурнал' : 'CaptainBook';

$isMapPage = Yii::$app->controller->id === 'map' && Yii::$app->controller->action->id === 'index';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> — <?= $appTitle ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column<?= $isMapPage ? ' overflow-hidden' : '' ?>">
<?php $this->beginBody() ?>

<?php if (!$isGuest): ?>
<nav class="navbar navbar-dark bg-dark px-3 gap-2 app-navbar" style="min-height:56px;">
    <?php if ($isSuperadmin): ?>
    <button class="btn btn-sm btn-outline-light d-lg-none me-1"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarOffcanvas"
            aria-controls="sidebarOffcanvas"
            title="<?= Yii::t('app', 'Navigation') ?>">
        ☰
    </button>
    <?php endif ?>

    <?= Html::a($appTitle, Yii::$app->homeUrl, ['class' => 'navbar-brand mb-0 flex-shrink-0 me-2']) ?>

    <!-- Desktop search -->
    <form action="<?= Url::to(['/search/index']) ?>" method="get"
          class="d-none d-md-flex flex-grow-1 navbar-search">
        <input type="text" name="q"
               value="<?= Html::encode(Yii::$app->request->get('q', '')) ?>"
               class="form-control form-control-sm"
               placeholder="<?= Yii::t('app', 'Search objects...') ?>">
    </form>

    <div class="d-flex align-items-center gap-2 ms-auto">
        <!-- Mobile search toggle -->
        <button class="btn btn-sm btn-outline-light d-md-none navbar-search-mobile"
                id="btn-mobile-search" type="button" style="display:none!important">
            🔍
        </button>

        <span class="text-white-50 small navbar-username d-none d-sm-inline">
            <?= Html::encode(Yii::$app->user->identity->username) ?>
        </span>

        <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'm-0']) ?>
        <?= Html::submitButton(
            '<span class="d-none d-sm-inline">' . Yii::t('app', 'Logout') . '</span><span class="d-inline d-sm-none">✕</span>',
            ['class' => 'btn btn-sm btn-outline-light', 'encode' => false]
        ) ?>
        <?= Html::endForm() ?>

        <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-light' : 'btn-outline-light')]) ?>
        <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-light' : 'btn-outline-light')]) ?>
    </div>
</nav>

<!-- Mobile search bar (shown/hidden via JS) -->
<div class="mobile-search-bar d-md-none" id="mobile-search-bar" style="display:none!important">
    <form action="<?= Url::to(['/search/index']) ?>" method="get" class="d-flex">
        <input type="text" name="q"
               value="<?= Html::encode(Yii::$app->request->get('q', '')) ?>"
               class="form-control form-control-sm"
               placeholder="<?= Yii::t('app', 'Search objects...') ?>"
               id="mobile-search-input"
               autofocus>
        <button class="btn btn-sm btn-outline-light ms-2" type="submit">→</button>
    </form>
</div>

<?php else: ?>
<div style="position:fixed;top:12px;right:16px;z-index:1030;">
    <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
    <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
</div>
<?php endif ?>

<?php if ($isSuperadmin): ?>
<!-- Offcanvas sidebar for mobile/tablet -->
<div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1"
     id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel"
     style="width:260px; max-width:80vw;">
    <div class="offcanvas-header bg-dark text-white py-2">
        <h6 class="offcanvas-title" id="sidebarOffcanvasLabel"><?= Yii::t('app', 'Navigation') ?></h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?= SidebarMenu::widget() ?>
    </div>
</div>
<?php endif ?>

<main class="flex-grow-1 d-flex align-items-<?= $isGuest ? 'center' : 'start' ?> justify-content-center<?= $isMapPage ? ' p-0' : '' ?>">
    <?php if ($isSuperadmin && !$isMapPage): ?>
    <div class="container-fluid">
        <div class="layout-with-sidebar">
            <div class="layout-content">
                <?= $content ?>
            </div>
            <div class="sidebar-menu-wrap d-none d-lg-block">
                <?= SidebarMenu::widget() ?>
            </div>
        </div>
    </div>
    <?php elseif ($isGuest): ?>
    <div class="w-100 px-2">
        <?= $content ?>
    </div>
    <?php elseif ($isMapPage): ?>
    <?= $content ?>
    <?php else: ?>
    <div class="container">
        <?= $content ?>
    </div>
    <?php endif ?>
</main>

<?php $this->registerJs(<<<JS
// Mobile search toggle
(function () {
    var btn = document.getElementById('btn-mobile-search');
    var bar = document.getElementById('mobile-search-bar');
    if (btn && bar) {
        btn.style.display = '';
        btn.addEventListener('click', function () {
            if (bar.style.display === 'none' || bar.style.display === '') {
                bar.style.display = 'block';
                bar.removeAttribute('style');
                document.getElementById('mobile-search-input').focus();
            } else {
                bar.style.display = 'none';
            }
        });
    }
})();
JS) ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
