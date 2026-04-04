<?php
/** @var yii\web\View $this */
/** @var array $errors */
/** @var array $data */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Registration');
?>
<div class="card shadow-sm mx-auto auth-card" style="width:100%;max-width:420px;">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-1"><?= Yii::t('app', 'Registration') ?></h4>
        <p class="text-center text-muted small mb-4"><?= Yii::t('app', 'Create an account and link Telegram') ?></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $e): ?>
                    <div><?= Html::encode($e) ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php yii\widgets\ActiveForm::begin(['id' => 'register-form']); ?>

        <div class="mb-3">
            <?= Html::label(Yii::t('app', 'Username'), 'username', ['class' => 'form-label']) ?>
            <?= Html::textInput('username', Html::encode($data['username'] ?? ''), [
                'id'          => 'username',
                'class'       => 'form-control',
                'placeholder' => 'my_login',
                'required'    => true,
                'autofocus'   => true,
            ]) ?>
            <div class="form-text"><?= Yii::t('app', '3–64 characters: letters, digits, _') ?></div>
        </div>

        <div class="mb-4">
            <?= Html::label(Yii::t('app', 'Phone number'), 'phone', ['class' => 'form-label']) ?>
            <?= Html::textInput('phone', Html::encode($data['phone'] ?? ''), [
                'id'          => 'phone',
                'class'       => 'form-control',
                'placeholder' => '+79001234567',
                'type'        => 'tel',
                'required'    => true,
            ]) ?>
            <div class="form-text"><?= Yii::t('app', 'Must match your Telegram phone number') ?></div>
        </div>

        <?= Html::submitButton(Yii::t('app', 'Register'), ['class' => 'btn btn-primary w-100']) ?>

        <?php yii\widgets\ActiveForm::end(); ?>

        <hr class="my-3">
        <p class="text-center mb-0 small">
            <?= Yii::t('app', 'Already have an account?') ?> <?= Html::a(Yii::t('app', 'Sign in'), ['auth/login']) ?>
        </p>
    </div>
</div>
