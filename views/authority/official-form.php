<?php
/** @var app\models\AuthorityOfficial $model */
/** @var app\models\Authority $authority */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? Yii::t('app', 'Add official') : Yii::t('app', 'Edit official');
?>
<div class="row justify-content-center"><div class="col-lg-6">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= $this->title ?></h2>
    <?= Html::a('← ' . Html::encode($authority->name), ['authority/view', 'id' => $authority->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>
<?php $form = ActiveForm::begin() ?>
<?= $form->field($model, 'full_name')->textInput(['autofocus' => true]) ?>
<?= $form->field($model, 'position')->textInput() ?>
<?= $form->field($model, 'phone')->textInput() ?>
<?= $form->field($model, 'email')->textInput() ?>
<div class="d-flex gap-2 mt-3">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['authority/view', 'id' => $authority->id], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end() ?>
</div></div>
