<?php
/** @var yii\web\View $this */
/** @var array $errors */
/** @var array $data */

use yii\helpers\Html;

$this->title = 'Регистрация';
?>
<div class="card shadow-sm mx-auto" style="width:100%;max-width:420px;">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-1">Регистрация</h4>
        <p class="text-center text-muted small mb-4">Создайте аккаунт и привяжите Telegram</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $e): ?>
                    <div><?= Html::encode($e) ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php yii\widgets\ActiveForm::begin(['id' => 'register-form']); ?>

        <div class="mb-3">
            <?= Html::label('Имя пользователя', 'username', ['class' => 'form-label']) ?>
            <?= Html::textInput('username', Html::encode($data['username'] ?? ''), [
                'id'          => 'username',
                'class'       => 'form-control',
                'placeholder' => 'my_login',
                'required'    => true,
                'autofocus'   => true,
            ]) ?>
            <div class="form-text">3–64 символа: буквы, цифры, _</div>
        </div>

        <div class="mb-4">
            <?= Html::label('Номер телефона', 'phone', ['class' => 'form-label']) ?>
            <?= Html::textInput('phone', Html::encode($data['phone'] ?? ''), [
                'id'          => 'phone',
                'class'       => 'form-control',
                'placeholder' => '+79001234567',
                'type'        => 'tel',
                'required'    => true,
            ]) ?>
            <div class="form-text">Должен совпадать с номером в Telegram</div>
        </div>

        <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary w-100']) ?>

        <?php yii\widgets\ActiveForm::end(); ?>

        <hr class="my-3">
        <p class="text-center mb-0 small">
            Уже есть аккаунт? <?= Html::a('Войти', ['auth/login']) ?>
        </p>
    </div>
</div>
