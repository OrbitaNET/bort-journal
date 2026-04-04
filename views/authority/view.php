<?php
/** @var app\models\Authority $model */
/** @var app\models\AuthorityOfficial[] $officials */
use yii\helpers\Html;

$this->title = $model->name;
?>
<div class="page-header">
    <h2><?= Html::encode($model->name) ?></h2>
    <div class="detail-actions">
        <?= Html::a(Yii::t('app', 'Edit'), ['authority/update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['authority/delete', 'id' => $model->id], ['class' => 'btn btn-outline-danger btn-sm', 'data-confirm' => Yii::t('app', 'Are you sure?'), 'data-method' => 'post']) ?>
        <?= Html::a('← ' . Yii::t('app', 'Back'), ['authority/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="card mb-4"><div class="card-body">
<?= $this->render('//shared/_poi_detail', ['model' => $model, 'rows' => [
    Yii::t('app', 'Type')        => $model->type,
    Yii::t('app', 'Latitude')    => $model->lat,
    Yii::t('app', 'Longitude')   => $model->lng,
    Yii::t('app', 'Address')     => $model->address,
    Yii::t('app', 'Phone')       => $model->phone,
    Yii::t('app', 'Website')     => $model->website,
    Yii::t('app', 'Description') => $model->description,
]]) ?>
</div></div>

<div class="page-header">
    <h4 class="mb-0"><?= Yii::t('app', 'Officials') ?></h4>
    <?= Html::a('+ ' . Yii::t('app', 'Add official'), ['authority/add-official', 'authorityId' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
</div>

<?php if ($officials): ?>

<div class="table-responsive-cards">
    <!-- Desktop table -->
    <div class="desktop-table table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><?= Yii::t('app', 'Full name') ?></th>
                    <th><?= Yii::t('app', 'Position') ?></th>
                    <th><?= Yii::t('app', 'Phone') ?></th>
                    <th><?= Yii::t('app', 'Email') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($officials as $official): ?>
            <tr>
                <td><?= Html::encode($official->full_name) ?></td>
                <td><?= Html::encode($official->position) ?></td>
                <td><?= Html::encode($official->phone) ?></td>
                <td><?= Html::encode($official->email) ?></td>
                <td class="text-end text-nowrap">
                    <?= Html::a(Yii::t('app', 'Edit'), ['authority/update-official', 'id' => $official->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Delete'), ['authority/delete-official', 'id' => $official->id], ['class' => 'btn btn-sm btn-outline-danger', 'data-confirm' => Yii::t('app', 'Are you sure?'), 'data-method' => 'post']) ?>
                </td>
            </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile card list -->
    <div class="mobile-card-list">
        <?php foreach ($officials as $official): ?>
        <div class="mobile-card-item">
            <div class="mci-title"><?= Html::encode($official->full_name) ?></div>
            <div class="mci-meta">
                <?php if ($official->position): ?><div><?= Html::encode($official->position) ?></div><?php endif ?>
                <?php if ($official->phone): ?><div><?= Html::encode($official->phone) ?></div><?php endif ?>
                <?php if ($official->email): ?><div><?= Html::encode($official->email) ?></div><?php endif ?>
            </div>
            <div class="mci-actions">
                <?= Html::a(Yii::t('app', 'Edit'), ['authority/update-official', 'id' => $official->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                <?= Html::a(Yii::t('app', 'Delete'), ['authority/delete-official', 'id' => $official->id], ['class' => 'btn btn-sm btn-outline-danger', 'data-confirm' => Yii::t('app', 'Are you sure?'), 'data-method' => 'post']) ?>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>

<?php else: ?>
<p class="text-muted"><?= Yii::t('app', 'No officials added yet.') ?></p>
<?php endif ?>
