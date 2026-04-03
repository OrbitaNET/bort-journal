<?php
/** @var yii\web\View $this */
/** @var app\models\FuelStation $model */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? Yii::t('app', 'Add fuel station') : Yii::t('app', 'Edit fuel station');
?>
<div class="row justify-content-center"><div class="col-lg-7">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= $this->title ?></h2>
    <?= Html::a('← ' . Yii::t('app', 'Back'), ['fuel-station/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<?php $form = ActiveForm::begin() ?>
<?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
<?= $this->render('//shared/_poi_map_picker', ['form' => $form, 'model' => $model]) ?>
<?= $form->field($model, 'address')->textInput() ?>
<?= $form->field($model, 'phone')->textInput() ?>
<?= $form->field($model, 'fuel_types')->textInput(['placeholder' => 'ДТ, АИ-92, АИ-95, Газ']) ?>
<?= $form->field($model, 'working_hours')->textInput(['placeholder' => '08:00–22:00']) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<div class="d-flex gap-2 mt-3">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['fuel-station/index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end() ?>
</div></div>
