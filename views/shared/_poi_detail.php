<?php
/**
 * @var array $rows  ['Label' => 'value', ...]
 */
use yii\helpers\Html;
?>
<dl class="row mb-0">
<?php foreach ($rows as $label => $value): ?>
    <?php if ($value !== null && $value !== ''): ?>
    <dt class="col-sm-4 text-muted fw-normal"><?= Html::encode($label) ?></dt>
    <dd class="col-sm-8"><?= nl2br(Html::encode((string)$value)) ?></dd>
    <?php endif ?>
<?php endforeach ?>
</dl>
