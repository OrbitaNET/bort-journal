<?php
/** @var yii\web\View $this */
/** @var string $phone */
/** @var string|null $botUsername */

use yii\helpers\Html;

$this->title = 'Привяжите Telegram';
$botLink = $botUsername
    ? 'https://t.me/' . $botUsername . '?start=' . urlencode(ltrim($phone, '+'))
    : null;
?>
<div class="card shadow-sm mx-auto" style="width:100%;max-width:440px;">
    <div class="card-body p-4 text-center">
        <div class="mb-3" style="font-size:2.5rem;">✅</div>
        <h4 class="mb-1">Аккаунт создан!</h4>
        <p class="text-muted small mb-4">Осталось привязать Telegram</p>

        <div class="alert alert-info text-start">
            <strong>Шаг 1.</strong> Откройте бота в Telegram<br>
            <strong>Шаг 2.</strong> Отправьте боту команду:<br>
            <code class="d-block mt-1 p-2 bg-white rounded">/start <?= Html::encode($phone) ?></code>
        </div>

        <?php if ($botLink): ?>
            <a href="<?= Html::encode($botLink) ?>" class="btn btn-primary w-100 mb-2" target="_blank">
                Открыть бота в Telegram
            </a>
        <?php endif ?>

        <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['auth/login'])) ?>"
           class="btn btn-outline-secondary w-100">
            Перейти ко входу
        </a>
    </div>
</div>
