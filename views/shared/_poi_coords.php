<?php
/** @var yii\widgets\ActiveForm $form */
/** @var yii\db\ActiveRecord $model */
?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'lat')->textInput(['type' => 'number', 'step' => 'any', 'placeholder' => '55.7558']) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'lng')->textInput(['type' => 'number', 'step' => 'any', 'placeholder' => '37.6173']) ?>
    </div>
</div>
