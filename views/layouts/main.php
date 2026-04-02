<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> — Bort Journal</title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody() ?>

<?php if (!Yii::$app->user->isGuest): ?>
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand mb-0">Bort Journal</span>
    <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
        <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'm-0']) ?>
        <?= Html::submitButton('Выйти', ['class' => 'btn btn-sm btn-outline-light']) ?>
        <?= Html::endForm() ?>
    </div>
</nav>
<?php endif ?>

<main class="flex-grow-1 d-flex align-items-<?= Yii::$app->user->isGuest ? 'center' : 'start pt-4' ?> justify-content-center">
    <div class="<?= Yii::$app->user->isGuest ? 'w-100' : 'container' ?>">
        <?= $content ?>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
