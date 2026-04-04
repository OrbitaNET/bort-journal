<?php
use yii\helpers\Html;
use app\models\User;

$this->title = $model->username;

$roleLabels = [
    User::ROLE_SUPERADMIN => Yii::t('app', 'Superadmin'),
    User::ROLE_ADMIN      => Yii::t('app', 'Administrator'),
    User::ROLE_USER       => Yii::t('app', 'User'),
];
?>
<div class="row justify-content-center"><div class="col-lg-6">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= Html::encode($model->username) ?></h2>
    <?= Html::a('← ' . Yii::t('app', 'Back'), ['user/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
<?php endif ?>

<dl class="row mb-4">
    <dt class="col-sm-4"><?= Yii::t('app', 'Phone') ?></dt>
    <dd class="col-sm-8"><?= Html::encode($model->phone) ?></dd>

    <dt class="col-sm-4"><?= Yii::t('app', 'Telegram') ?></dt>
    <dd class="col-sm-8"><?= $model->telegram_username ? '@' . Html::encode($model->telegram_username) : '—' ?></dd>

    <dt class="col-sm-4"><?= Yii::t('app', 'Role') ?></dt>
    <dd class="col-sm-8"><?= Html::encode($roleLabels[$model->role] ?? $model->role) ?></dd>

    <dt class="col-sm-4"><?= Yii::t('app', 'Registered') ?></dt>
    <dd class="col-sm-8"><?= $model->created_at ? date('d.m.Y H:i', $model->created_at) : '—' ?></dd>
</dl>

<?php if (!$model->isSuperadmin()): ?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title"><?= Yii::t('app', 'Change role') ?></h5>
        <?php $form = \yii\widgets\ActiveForm::begin(['action' => ['user/set-role', 'id' => $model->id]]) ?>
        <div class="d-flex gap-3 align-items-center">
            <select name="role" class="form-select w-auto">
                <option value="<?= User::ROLE_USER ?>"<?= $model->role === User::ROLE_USER ? ' selected' : '' ?>><?= Yii::t('app', 'User') ?></option>
                <option value="<?= User::ROLE_ADMIN ?>"<?= $model->role === User::ROLE_ADMIN ? ' selected' : '' ?>><?= Yii::t('app', 'Administrator') ?></option>
            </select>
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php \yii\widgets\ActiveForm::end() ?>
    </div>
</div>
<?php endif ?>

</div></div>
