<?php
/** @var yii\web\View $this */
/** @var app\models\MenuItem $item */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<li class="list-group-item d-flex justify-content-between align-items-center <?= $item->is_active ? '' : 'text-muted' ?>" data-id="<?= $item->id ?>">
    <div class="d-flex align-items-center gap-2">
        <span class="drag-handle text-muted" style="cursor:grab" title="<?= Yii::t('app', 'Drag') ?>">⠿</span>
        <span><?= Yii::t('app', $item->label) ?></span>
        <code class="small text-secondary"><?= Html::encode($item->controller . '/' . $item->action) ?></code>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small"><?= Yii::t('app', 'Order') ?>: <?= $item->sort_order ?></span>
        <button class="btn btn-sm <?= $item->is_active ? 'btn-success' : 'btn-outline-secondary' ?> toggle-btn"
                data-id="<?= $item->id ?>"
                data-url="<?= Url::to(['toggle-item', 'id' => $item->id]) ?>"
                data-label-on="<?= Yii::t('app', 'On') ?>"
                data-label-off="<?= Yii::t('app', 'Off') ?>">
            <?= $item->is_active ? Yii::t('app', 'On') : Yii::t('app', 'Off') ?>
        </button>
        <?= Html::a(Yii::t('app', 'Edit.'), ['update-item', 'id' => $item->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
        <?= Html::a(Yii::t('app', 'Del.'), ['delete-item', 'id' => $item->id], [
            'class' => 'btn btn-outline-danger btn-sm',
            'data-confirm' => Yii::t('app', 'Delete item "{name}"?', ['name' => $item->label]),
            'data-method' => 'post',
        ]) ?>
    </div>
</li>
