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
$hasSidebar   = !$isGuest; // all logged-in users get sidebar
$currentLang  = Yii::$app->language;
$appTitle     = $currentLang === 'ru' ? 'БортЖурнал' : 'CaptainBook';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> — <?= $appTitle ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<?php if (!$isGuest): ?>
<nav class="navbar navbar-dark bg-dark px-3" style="gap:12px">

    <?php /* Mobile only: hamburger for all logged-in users */ ?>
    <button class="btn btn-sm btn-outline-light d-md-none me-1"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarOffcanvas"
            title="<?= Yii::t('app', 'Navigation') ?>">☰</button>

    <?= Html::a($appTitle, Yii::$app->homeUrl, ['class' => 'navbar-brand mb-0 flex-shrink-0']) ?>

    <?php /* Desktop: search always visible */ ?>
    <form action="<?= Url::to(['/search/index']) ?>" method="get"
          class="d-none d-md-flex flex-grow-1" style="max-width:360px">
        <input type="text" name="q"
               value="<?= Html::encode(Yii::$app->request->get('q', '')) ?>"
               class="form-control form-control-sm"
               placeholder="<?= Yii::t('app', 'Search objects...') ?>">
    </form>

    <div class="d-flex align-items-center gap-3 ms-auto">
        <?php /* Mobile only: search icon */ ?>
        <button class="btn btn-sm btn-outline-light d-md-none"
                id="btn-mobile-search" type="button">🔍</button>

        <span class="text-white-50 small d-none d-md-inline">
            <?= Html::encode(Yii::$app->user->identity->username) ?>
        </span>

        <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'm-0']) ?>
        <?= Html::submitButton(
            '<span class="d-none d-md-inline">' . Yii::t('app', 'Logout') . '</span>'
            . '<span class="d-inline d-md-none">✕</span>',
            ['class' => 'btn btn-sm btn-outline-light', 'encode' => false]
        ) ?>
        <?= Html::endForm() ?>

        <span class="d-none d-md-inline">
        <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-light' : 'btn-outline-light')]) ?>
        <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-light' : 'btn-outline-light')]) ?>
        </span>
    </div>
</nav>

<?php /* Mobile only: search bar below navbar */ ?>
<div id="mobile-search-bar" class="mobile-search-bar" style="display:none">
    <form action="<?= Url::to(['/search/index']) ?>" method="get" class="d-flex">
        <input type="text" name="q"
               value="<?= Html::encode(Yii::$app->request->get('q', '')) ?>"
               class="form-control form-control-sm"
               placeholder="<?= Yii::t('app', 'Search objects...') ?>"
               id="mobile-search-input">
        <button class="btn btn-sm btn-outline-light ms-2" type="submit">→</button>
    </form>
</div>

<?php else: ?>
<div style="position:fixed;top:12px;right:16px;z-index:1030;">
    <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
    <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
</div>
<?php endif ?>

<?php /* Mobile only: offcanvas sidebar for all logged-in users */ ?>
<div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1"
     id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel"
     style="width:260px;max-width:80vw;">
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div class="flex-grow-1">
            <?= SidebarMenu::widget() ?>
        </div>
        <div class="p-3 border-top d-flex gap-2">
            <?= Html::a('RU', ['/language/ru'], ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
            <?= Html::a('EN', ['/language/en'], ['class' => 'btn btn-sm ' . ($currentLang === 'en' ? 'btn-secondary' : 'btn-outline-secondary')]) ?>
        </div>
    </div>
</div>

<main class="flex-grow-1 d-flex align-items-<?= $isGuest ? 'center' : 'start pt-4' ?> justify-content-center">
    <?php if ($hasSidebar): ?>
    <div class="container-fluid">
        <div class="d-flex gap-4">
            <div class="flex-grow-1 min-w-0">
                <?= $content ?>
            </div>
            <?php /* Desktop: sidebar in layout; Mobile: hidden (offcanvas used) */ ?>
            <div class="sidebar-menu-wrap flex-shrink-0 d-none d-md-block">
                <?= SidebarMenu::widget() ?>
            </div>
        </div>
    </div>
    <?php elseif ($isGuest): ?>
    <div class="w-100 px-2">
        <?= $content ?>
    </div>
    <?php endif ?>
</main>

<?php $this->registerJs(<<<JS
(function () {
    var btn = document.getElementById('btn-mobile-search');
    var bar = document.getElementById('mobile-search-bar');
    if (!btn || !bar) return;
    btn.addEventListener('click', function () {
        var open = bar.style.display !== 'none';
        bar.style.display = open ? 'none' : 'block';
        if (!open) document.getElementById('mobile-search-input').focus();
    });
})();
JS) ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
