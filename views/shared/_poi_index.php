<?php
/**
 * @var yii\web\View   $this
 * @var yii\db\ActiveRecord[] $models
 * @var string $title
 * @var string $createRoute
 * @var string $viewRoute
 * @var string $updateRoute
 * @var string $deleteRoute
 * @var array  $columns       [['attribute' => ..., 'label' => ...], ...]
 */

use yii\helpers\Html;

$this->title = $title;
?>

<div class="page-header">
    <h2><?= Html::encode($title) ?></h2>
    <?= Html::a('+ ' . Yii::t('app', 'Add'), [$createRoute], ['class' => 'btn btn-primary btn-sm']) ?>
</div>

<?php if (!$models): ?>
    <div class="text-center text-muted py-5"><?= Yii::t('app', 'No records yet.') ?></div>
<?php else: ?>

<div class="table-responsive-cards">
    <!-- Desktop table -->
    <div class="desktop-table table-responsive">
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
                            'class'        => 'btn btn-sm btn-outline-danger',
                            'data-confirm' => Yii::t('app', 'Are you sure?'),
                            'data-method'  => 'post',
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile card list -->
    <div class="mobile-card-list">
        <?php foreach ($models as $model): ?>
        <div class="mobile-card-item">
            <div class="mci-title"><?= Html::encode($model->{$columns[0]['attribute']}) ?></div>
            <?php if (count($columns) > 1): ?>
            <div class="mci-meta">
                <?php foreach (array_slice($columns, 1) as $col): ?>
                    <?php $val = $model->{$col['attribute']}; if ($val !== null && $val !== ''): ?>
                    <span class="me-2"><span class="text-secondary"><?= Html::encode($col['label']) ?>:</span> <?= Html::encode($val) ?></span>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
            <?php endif ?>
            <div class="mci-actions">
                <?= Html::a(Yii::t('app', 'View'), [$viewRoute, 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                <?= Html::a(Yii::t('app', 'Edit'), [$updateRoute, 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                <?= Html::a(Yii::t('app', 'Delete'), [$deleteRoute, 'id' => $model->id], [
                    'class'        => 'btn btn-sm btn-outline-danger',
                    'data-confirm' => Yii::t('app', 'Are you sure?'),
                    'data-method'  => 'post',
                ]) ?>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>

<?php endif ?>
