<?php
/** @var yii\web\View $this */
/** @var array $errors */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Sign In');
?>
<div class="card shadow-sm mx-auto" style="width:100%;max-width:400px;">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-1">Bort Journal</h4>
        <p class="text-center text-muted small mb-4"><?= Yii::t('app', 'Enter your phone number to sign in') ?></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $e): ?>
                    <div><?= Html::encode($e) ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php yii\widgets\ActiveForm::begin(['id' => 'login-form']); ?>

        <div class="mb-3">
            <?= Html::label(Yii::t('app', 'Phone number'), 'phone', ['class' => 'form-label']) ?>
            <?= Html::textInput('phone', '', [
                'id'          => 'phone',
                'class'       => 'form-control form-control-lg',
                'placeholder' => '+79001234567',
                'type'        => 'tel',
                'required'    => true,
                'autofocus'   => true,
            ]) ?>
            <div class="form-text"><?= Yii::t('app', 'Phone number linked to Telegram') ?></div>
        </div>

        <?= Html::submitButton(Yii::t('app', 'Get code'), ['class' => 'btn btn-primary w-100']) ?>

        <?php yii\widgets\ActiveForm::end(); ?>

        <hr class="my-3">
        <p class="text-center mb-0 small">
            <?= Yii::t('app', 'No account?') ?> <?= Html::a(Yii::t('app', 'Register'), ['auth/register']) ?>
        </p>
    </div>
</div>
