<?php
use yii\helpers\Html;

$this->title = Yii::t('app', 'Search');
?>

<div class="mb-4">
    <h2 class="mb-3"><?= Yii::t('app', 'Search objects') ?></h2>
    <form action="" method="get" class="d-flex gap-2" style="max-width:500px">
        <input type="text" name="q" value="<?= Html::encode($q) ?>"
               class="form-control" placeholder="<?= Yii::t('app', 'Name or address...') ?>" autofocus>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app', 'Search') ?></button>
    </form>
</div>

<?php if ($q !== '' && !$results): ?>
    <p class="text-muted"><?= Yii::t('app', 'Nothing found for "{q}".', ['q' => Html::encode($q)]) ?></p>
<?php endif ?>

<?php foreach ($results as $group): ?>
<div class="mb-4">
    <h5 class="text-secondary border-bottom pb-1"><?= Html::encode($group['label']) ?></h5>
    <div class="list-group">
        <?php foreach ($group['models'] as $model): ?>
        <a href="<?= \yii\helpers\Url::to([$group['route'] . '/view', 'id' => $model->id]) ?>"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span><?= Html::encode($model->name) ?></span>
            <?php if (!empty($model->address)): ?>
            <span class="text-muted small ms-3"><?= Html::encode($model->address) ?></span>
            <?php endif ?>
        </a>
        <?php endforeach ?>
    </div>
</div>
<?php endforeach ?>
