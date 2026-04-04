<?php
use yii\helpers\Html;

$this->title = Yii::t('app', 'Edit application') . ' #' . $model->id;
?>
<div class="page-header">
    <h2><?= Html::encode($this->title) ?></h2>
</div>

<?= $this->render('_form', ['model' => $model]) ?>
