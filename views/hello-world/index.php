<?php
/** @var yii\web\View $this */
/** @var string $username */

use yii\helpers\Html;

$this->title = 'Hello World';
?>
<div class="text-center">
    <h1 class="display-5 mb-2">Hello, <?= Html::encode($username) ?>!</h1>
    <p class="text-muted">Вы успешно авторизованы через Telegram.</p>
</div>
