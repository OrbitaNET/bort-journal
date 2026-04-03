<?php
/** @var yii\web\View $this */
/** @var app\models\MenuGroup $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? Yii::t('app', 'New group') : Yii::t('app', 'Edit group');
?>

<div class="row justify-content-center">
<div class="col-md-5">

<h2 class="mb-4"><?= $this->title ?></h2>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', 'Group name')]) ?>
<?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0]) ?>

<div class="d-flex gap-2 mt-3">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end() ?>

</div>
</div>
