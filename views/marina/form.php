<?php
/** @var app\models\Marina $model */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? Yii::t('app', 'Add marina') : Yii::t('app', 'Edit marina');
?>
<div class="row justify-content-center"><div class="col-lg-7">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= $this->title ?></h2>
    <?= Html::a('← ' . Yii::t('app', 'Back'), ['marina/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>
<?php $form = ActiveForm::begin() ?>
<?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
<?= $this->render('//shared/_poi_map_picker', ['form' => $form, 'model' => $model]) ?>
<?= $form->field($model, 'address')->textInput() ?>
<?= $form->field($model, 'phone')->textInput() ?>
<div class="row">
    <div class="col-md-6"><?= $form->field($model, 'berths_count')->textInput(['type' => 'number', 'min' => 0]) ?></div>
    <div class="col-md-6"><?= $form->field($model, 'max_draft')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0, 'placeholder' => '2.50']) ?></div>
</div>
<?= $form->field($model, 'working_hours')->textInput(['placeholder' => '08:00–20:00']) ?>
<?= $form->field($model, 'services')->textarea(['rows' => 3, 'placeholder' => 'Заправка, стоянка, ремонт...']) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<div class="d-flex gap-2 mt-3">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['marina/index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end() ?>
</div></div>
