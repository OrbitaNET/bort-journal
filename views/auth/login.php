<?php
/** @var yii\web\View $this */
/** @var array $errors */

use yii\helpers\Html;

$this->title = 'Вход';
?>
<div class="card shadow-sm mx-auto" style="width:100%;max-width:400px;">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-1">Bort Journal</h4>
        <p class="text-center text-muted small mb-4">Введите номер телефона для входа</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $e): ?>
                    <div><?= Html::encode($e) ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php yii\widgets\ActiveForm::begin(['id' => 'login-form']); ?>

        <div class="mb-3">
            <?= Html::label('Номер телефона', 'phone', ['class' => 'form-label']) ?>
            <?= Html::textInput('phone', '', [
                'id'          => 'phone',
                'class'       => 'form-control form-control-lg',
                'placeholder' => '+79001234567',
                'type'        => 'tel',
                'required'    => true,
                'autofocus'   => true,
            ]) ?>
            <div class="form-text">Номер телефона, привязанный к Telegram</div>
        </div>

        <?= Html::submitButton('Получить код', ['class' => 'btn btn-primary w-100']) ?>

        <?php yii\widgets\ActiveForm::end(); ?>

        <hr class="my-3">
        <p class="text-center mb-0 small">
            Нет аккаунта? <?= Html::a('Зарегистрироваться', ['auth/register']) ?>
        </p>
    </div>
</div>
