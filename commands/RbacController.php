<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\rbac\Permission;
use app\models\User;

/**
 * Manages RBAC roles and permissions.
 *
 * Usage:
 *   php yii rbac/init              — create roles and seed default permissions
 *   php yii rbac/assign <userId> <role>  — assign role to user
 *   php yii rbac/add-permission <role> <controller> <action>  — grant permission
 *   php yii rbac/list              — show all permissions
 */
class RbacController extends Controller
{
    /**
     * Create roles and seed default permissions.
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // --- Roles ---
        $admin = $auth->createRole(User::ROLE_ADMIN);
        $admin->description = 'Administrator';
        $auth->add($admin);

        $user = $auth->createRole(User::ROLE_USER);
        $user->description = 'Regular user';
        $auth->add($user);

        // --- Default permissions for "user" role ---
        $userPermissions = [
            'hello-world/index',
            'auth/logout',
            'map/index',
            'map/data',
            'map/polygons',
        ];

        foreach ($userPermissions as $name) {
            $perm = $auth->createPermission($name);
            $auth->add($perm);
            $auth->addChild($user, $perm);
        }

        // --- Default permissions for "admin" role (inherits user permissions) ---
        $auth->addChild($admin, $user);

        $adminPermissions = [
            'map/save-polygon',
            'map/delete-polygon',
        ];

        foreach ($adminPermissions as $name) {
            $perm = $auth->createPermission($name);
            $auth->add($perm);
            $auth->addChild($admin, $perm);
        }

        // --- Sync RBAC assignments for existing users ---
        foreach (User::find()->where(['!=', 'role', User::ROLE_SUPERADMIN])->all() as $u) {
            $auth->revokeAll($u->id);
            $role = $auth->getRole($u->role);
            if ($role) {
                $auth->assign($role, $u->id);
            }
        }

        $this->stdout("RBAC initialized successfully.\n");
        return ExitCode::OK;
    }

    /**
     * Assign a role to a user.
     *
     * @param int    $userId
     * @param string $role  (admin|user)
     */
    public function actionAssign($userId, $role)
    {
        $auth = Yii::$app->authManager;

        $rbacRole = $auth->getRole($role);
        if (!$rbacRole) {
            $this->stderr("Role '{$role}' not found. Run rbac/init first.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user = User::findOne($userId);
        if (!$user) {
            $this->stderr("User #{$userId} not found.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth->revokeAll($userId);
        $auth->assign($rbacRole, $userId);

        $user->role = $role;
        $user->save(false);

        $this->stdout("Role '{$role}' assigned to user #{$userId} ({$user->username}).\n");
        return ExitCode::OK;
    }

    /**
     * Grant a controller/action permission to a role.
     *
     * @param string $role        (admin|user)
     * @param string $controller  e.g. hello-world
     * @param string $action      e.g. index
     */
    public function actionAddPermission($role, $controller, $action)
    {
        $auth = Yii::$app->authManager;
        $name = "{$controller}/{$action}";

        $perm = $auth->getPermission($name);
        if (!$perm) {
            $perm = $auth->createPermission($name);
            $auth->add($perm);
        }

        $rbacRole = $auth->getRole($role);
        if (!$rbacRole) {
            $this->stderr("Role '{$role}' not found.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($auth->hasChild($rbacRole, $perm)) {
            $this->stdout("Permission '{$name}' already granted to role '{$role}'.\n");
            return ExitCode::OK;
        }

        $auth->addChild($rbacRole, $perm);
        $this->stdout("Permission '{$name}' granted to role '{$role}'.\n");
        return ExitCode::OK;
    }

    /**
     * List all permissions grouped by role.
     */
    public function actionList()
    {
        $auth = Yii::$app->authManager;

        foreach ([User::ROLE_ADMIN, User::ROLE_USER] as $roleName) {
            $role = $auth->getRole($roleName);
            if (!$role) {
                continue;
            }
            $this->stdout("\n[{$roleName}]\n");
            $permissions = $auth->getPermissionsByRole($roleName);
            foreach ($permissions as $perm) {
                $this->stdout("  - {$perm->name}\n");
            }
        }

        $this->stdout("\n");
        return ExitCode::OK;
    }
}
