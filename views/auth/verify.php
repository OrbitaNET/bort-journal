<?php
/** @var yii\web\View $this */
/** @var array $errors */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Enter code');
?>
<div class="card shadow-sm mx-auto" style="width:100%;max-width:360px;">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-1"><?= Yii::t('app', 'Confirmation') ?></h4>
        <p class="text-center text-muted small mb-4"><?= Yii::t('app', 'Enter the 4-digit code from Telegram') ?></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $e): ?>
                    <div><?= Html::encode($e) ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if (Yii::$app->session->hasFlash('info')): ?>
            <div class="alert alert-info py-2"><?= Html::encode(Yii::$app->session->getFlash('info')) ?></div>
        <?php endif ?>

        <?php yii\widgets\ActiveForm::begin(['id' => 'verify-form']); ?>

        <div class="mb-4 text-center">
            <?= Html::textInput('code', '', [
                'id'          => 'code',
                'class'       => 'form-control form-control-lg text-center',
                'placeholder' => '0000',
                'maxlength'   => 4,
                'pattern'     => '\d{4}',
                'required'    => true,
                'autofocus'   => true,
                'style'       => 'letter-spacing:.6em; font-size:1.8em; max-width:160px; margin:0 auto;',
                'inputmode'   => 'numeric',
            ]) ?>
            <div class="form-text mt-1"><?= Yii::t('app', 'Code is valid for 5 minutes') ?></div>
        </div>

        <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-success w-100 mb-2']) ?>
        <?= Html::a(Yii::t('app', '← Back'), ['auth/login'], ['class' => 'btn btn-outline-secondary w-100']) ?>

        <?php yii\widgets\ActiveForm::end(); ?>
    </div>
</div>
