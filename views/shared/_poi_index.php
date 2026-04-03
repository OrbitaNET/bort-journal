<?php
/**
 * @var yii\web\View   $this
 * @var yii\db\ActiveRecord[] $models
 * @var string $title
 * @var string $createRoute   e.g. 'fuel-station/create'
 * @var string $viewRoute     e.g. 'fuel-station/view'
 * @var string $updateRoute   e.g. 'fuel-station/update'
 * @var string $deleteRoute   e.g. 'fuel-station/delete'
 * @var array  $columns       [['attribute' => ..., 'label' => ...], ...]
 */

use yii\helpers\Html;

$this->title = $title;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= Html::encode($title) ?></h2>
    <?= Html::a('+ ' . Yii::t('app', 'Add'), [$createRoute], ['class' => 'btn btn-primary btn-sm']) ?>
</div>

<?php if (!$models): ?>
    <div class="text-center text-muted py-5"><?= Yii::t('app', 'No records yet.') ?></div>
<?php else: ?>
<div class="table-responsive">
<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <?php foreach ($columns as $col): ?>
            <th><?= Html::encode($col['label']) ?></th>
            <?php endforeach ?>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($models as $model): ?>
        <tr>
            <?php foreach ($columns as $col): ?>
            <td><?= Html::encode($model->{$col['attribute']}) ?></td>
            <?php endforeach ?>
            <td class="text-end text-nowrap">
                <?= Html::a(Yii::t('app', 'View'), [$viewRoute, 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                <?= Html::a(Yii::t('app', 'Edit'), [$updateRoute, 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                <?= Html::a(Yii::t('app', 'Delete'), [$deleteRoute, 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'data-confirm' => Yii::t('app', 'Are you sure?'),
                    'data-method' => 'post',
                ]) ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
</div>
<?php endif ?>
