<?php
/** @var yii\web\View $this */
/** @var app\models\MenuGroup[] $groups */
/** @var app\models\MenuItem[] $ungrouped */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Menu settings');

$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js', ['position' => \yii\web\View::POS_END]);

$sortGroupsUrl = Url::to(['sort-groups']);
$sortItemsUrl  = Url::to(['sort-items']);

$this->registerJs(<<<JS
// Toggle active
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.toggle-btn');
    if (!btn) return;
    fetch(btn.dataset.url, {method: 'POST', headers: {'X-CSRF-Token': yii.getCsrfToken()}})
        .then(r => r.json())
        .then(data => {
            btn.textContent = data.is_active ? btn.dataset.labelOn : btn.dataset.labelOff;
            btn.className = 'btn btn-sm ' + (data.is_active ? 'btn-success' : 'btn-outline-secondary') + ' toggle-btn';
            btn.closest('li').classList.toggle('text-muted', !data.is_active);
        });
});

function saveOrder(url, ids) {
    var body = new URLSearchParams();
    ids.forEach(function(id, i) { body.append('ids[' + i + ']', id); });
    fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-Token': yii.getCsrfToken()},
        body: body
    });
}

// Sort groups
var groupWrap = document.getElementById('groups-wrap');
if (groupWrap) {
    Sortable.create(groupWrap, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function() {
            var ids = Array.from(groupWrap.querySelectorAll('.group-card')).map(el => el.dataset.id);
            saveOrder('{$sortGroupsUrl}', ids);
        }
    });
}

// Sort items within each group
document.querySelectorAll('.items-list').forEach(function(list) {
    Sortable.create(list, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function() {
            var ids = Array.from(list.querySelectorAll('li[data-id]')).map(el => el.dataset.id);
            saveOrder('{$sortItemsUrl}', ids);
        }
    });
});
JS);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= Yii::t('app', 'Menu settings') ?></h2>
    <div class="d-flex gap-2">
        <?= Html::a(Yii::t('app', '+ Group'), ['create-group'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
        <?= Html::a(Yii::t('app', '+ Menu item'), ['create-item'], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
</div>

<div id="groups-wrap">
<?php foreach ($groups as $group): ?>
<div class="card mb-3 group-card" data-id="<?= $group->id ?>">
    <div class="card-header d-flex justify-content-between align-items-center gap-2">
        <span class="drag-handle text-muted me-1" style="cursor:grab" title="<?= Yii::t('app', 'Drag') ?>">⠿</span>
        <strong class="flex-grow-1"><?= Yii::t('app', $group->name) ?></strong>
        <div class="d-flex gap-2">
            <?= Html::a(Yii::t('app', 'Edit'), ['update-group', 'id' => $group->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete-group', 'id' => $group->id], [
                'class' => 'btn btn-outline-danger btn-sm',
                'data-confirm' => Yii::t('app', 'Delete group "{name}"? Items will become ungrouped.', ['name' => $group->name]),
                'data-method' => 'post',
            ]) ?>
        </div>
    </div>
    <?php if ($group->items): ?>
    <ul class="list-group list-group-flush items-list">
        <?php foreach ($group->items as $item): ?>
        <?= $this->render('_item-row', ['item' => $item]) ?>
        <?php endforeach ?>
    </ul>
    <?php else: ?>
    <ul class="list-group list-group-flush items-list">
        <li class="list-group-item text-muted small"><?= Yii::t('app', 'No menu items') ?></li>
    </ul>
    <?php endif ?>
</div>
<?php endforeach ?>
</div>

<?php if ($ungrouped): ?>
<div class="card mb-3">
    <div class="card-header text-muted"><?= Yii::t('app', 'Ungrouped') ?></div>
    <ul class="list-group list-group-flush items-list">
        <?php foreach ($ungrouped as $item): ?>
        <?= $this->render('_item-row', ['item' => $item]) ?>
        <?php endforeach ?>
    </ul>
</div>
<?php endif ?>

<?php if (!$groups && !$ungrouped): ?>
<div class="text-center text-muted py-5">
    <p><?= Yii::t('app', 'Menu is empty.') ?></p>
    <p><?= Html::a(Yii::t('app', 'Add first item'), ['create-item'], ['class' => 'btn btn-primary']) ?></p>
</div>
<?php endif ?>
