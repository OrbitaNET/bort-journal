<?php
/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="card shadow-sm mx-auto mt-5" style="max-width:480px;">
    <div class="card-body p-4 text-center">
        <h2 class="text-danger mb-3"><?= Html::encode($name) ?></h2>
        <p class="text-muted"><?= nl2br(Html::encode($message)) ?></p>
        <a href="/" class="btn btn-outline-primary mt-2">На главную</a>
    </div>
</div>
