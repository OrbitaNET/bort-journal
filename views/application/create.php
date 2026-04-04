<?php
$this->title = Yii::t('app', 'New application');
?>
<div class="page-header">
    <h2><?= Yii::t('app', 'New application') ?></h2>
</div>

<?= $this->render('_form', ['model' => $model]) ?>
