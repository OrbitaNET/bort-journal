<?php
use yii\helpers\Html;
use app\models\User;

$this->title = Yii::t('app', 'Users');

$roleLabels = [
    User::ROLE_SUPERADMIN => '<span class="badge bg-danger">Superadmin</span>',
    User::ROLE_ADMIN      => '<span class="badge bg-warning text-dark">Admin</span>',
    User::ROLE_USER       => '<span class="badge bg-secondary">User</span>',
];
?>
<div class="page-header">
    <h2><?= $this->title ?></h2>
</div>

<div class="table-responsive-cards">
    <!-- Desktop table -->
    <div class="desktop-table table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th><?= Yii::t('app', 'Username') ?></th>
                    <th><?= Yii::t('app', 'Phone') ?></th>
                    <th><?= Yii::t('app', 'Telegram') ?></th>
                    <th><?= Yii::t('app', 'Role') ?></th>
                    <th><?= Yii::t('app', 'Registered') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= Html::encode($user->username) ?></td>
                    <td><?= Html::encode($user->phone) ?></td>
                    <td><?= $user->telegram_username ? '@' . Html::encode($user->telegram_username) : '—' ?></td>
                    <td><?= $roleLabels[$user->role] ?? Html::encode($user->role) ?></td>
                    <td><?= $user->created_at ? date('d.m.Y', $user->created_at) : '—' ?></td>
                    <td><?= Html::a(Yii::t('app', 'View'), ['user/view', 'id' => $user->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile card list -->
    <div class="mobile-card-list">
        <?php foreach ($users as $user): ?>
        <div class="mobile-card-item">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="mci-title mb-0"><?= Html::encode($user->username) ?></div>
                <div class="mci-badge"><?= $roleLabels[$user->role] ?? Html::encode($user->role) ?></div>
            </div>
            <div class="mci-meta">
                <?= Html::encode($user->phone) ?>
                <?php if ($user->telegram_username): ?>
                · @<?= Html::encode($user->telegram_username) ?>
                <?php endif ?>
                · <?= $user->created_at ? date('d.m.Y', $user->created_at) : '—' ?>
            </div>
            <div class="mci-actions">
                <?= Html::a(Yii::t('app', 'View'), ['user/view', 'id' => $user->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>
