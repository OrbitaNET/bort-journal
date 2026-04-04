<?php
/** @var yii\web\View $this */
/** @var app\models\MenuItem $model */
/** @var app\models\MenuGroup[] $groups */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? Yii::t('app', 'New menu item') : Yii::t('app', 'Edit menu item');
?>

<div class="row justify-content-center">
<div class="col-md-5">

<h2 class="mb-4"><?= $this->title ?></h2>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'label_ru')->textInput(['placeholder' => 'Название (RU)']) ?>
<?= $form->field($model, 'label')->textInput(['placeholder' => 'Label (EN)']) ?>

<?= $form->field($model, 'controller')->textInput(['placeholder' => Yii::t('app', 'e.g.: hello-world')]) ?>

<?= $form->field($model, 'action')->textInput(['placeholder' => Yii::t('app', 'e.g.: index')]) ?>

<?= $form->field($model, 'group_id')->dropDownList(
    ArrayHelper::map($groups, 'id', fn($g) => $g->localizedName),
    ['prompt' => Yii::t('app', '— No group —')]
) ?>

<?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0]) ?>

<?= $form->field($model, 'is_active')->checkbox() ?>

<div class="d-flex gap-2 mt-3">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end() ?>

</div>
</div>
