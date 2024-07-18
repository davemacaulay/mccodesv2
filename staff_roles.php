<?php
declare(strict_types=1);
global $db, $ir, $h;
require __DIR__ . '/sglobals.php';
if (!check_access('manage_roles')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}

/**
 *
 */
class StaffRolesManagement
{
    private static ?self $inst = null;
    private string $viewPath = '';
    private ?database $db = null;
    private array $roles = [];

    /**
     * @param database $db
     */
    public function __construct(database $db)
    {
        $this->setViewPath();
        $this->setDb($db);
        $this->setRoles();
        $this->processAction();
    }

    /**
     * @return void
     */
    private function setViewPath(): void
    {
        $this->viewPath = __DIR__ . '/views';
    }

    /**
     * @param database $db
     * @return void
     */
    private function setDb(database $db): void
    {
        $this->db = $db;
    }

    /**
     * @return void
     */
    private function setRoles(): void
    {
        $this->roles = [];
        $get_roles = $this->db->query(
            'SELECT * FROM staff_roles ORDER BY id'
        );
        while ($row = $this->db->fetch_row($get_roles)) {
            $this->roles[$row['id']] = $row;
        }
        $this->db->free_result($get_roles);
    }

    /**
     * @return void
     */
    private function processAction(): void
    {
        $_GET['id'] = array_key_exists('id', $_GET) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
        if (array_key_exists('submit', $_POST)) {
            $response = match ($_GET['action'] ?? '') {
                'add' => $this->doUpsertRole(),
                'edit' => $this->doUpsertRole($_GET['id']),
                'remove' => $this->doRemoveRole($_GET['id']),
                'grant' => $this->doGrantRole(),
                'revoke' => $this->doRevokeRole(),
                default => null,
            };
            if (!empty($response)) {
                echo '<div class="alert alert-' . $response['type'] . '">' . $response['message'] . '</div>';
                match ($_GET['action'] ?? '') {
                    'grant' => $this->viewGrantRole(),
                    'revoke' => $this->viewRevokeRole(),
                    default => $this->roleIndex(),
                };
                return;
            }
        }
        match ($_GET['action'] ?? '') {
            'add' => $this->viewUpsertRole(),
            'edit' => $this->viewUpsertRole($_GET['id']),
            'remove' => $this->viewRemoveRole($_GET['id']),
            'grant' => $this->viewGrantRole(),
            'revoke' => $this->viewRevokeRole(),
            default => $this->roleIndex(),
        };
    }

    /**
     * @param int|null $role_id
     * @return string[]
     */
    private function doUpsertRole(?int $role_id = null): array
    {
        $permission_columns = $this->getPermCols();
        $_POST['name']      = array_key_exists('name', $_POST) ? strip_tags(trim($_POST['name'])) : null;
        if (empty($_POST['name'])) {
            return [
                'type' => 'error',
                'message' => 'You didn\'t enter a valid name',
            ];
        }
        $conf = [
            'insert' => [
                'cols' => ['name'],
                'params' => ['\'' . $this->db->escape($_POST['name']) . '\''],
            ],
            'update' => 'name = \'' . $this->db->escape($_POST['name']) . '\'',
        ];
        foreach ($permission_columns as $column) {
            $_POST[$column]             = array_key_exists($column, $_POST) ? strip_tags(trim($_POST[$column])) : null;
            $conf['insert']['cols'][]   = $column;
            $conf['insert']['params'][] = isset($_POST[$column]) ? 1 : 0;
            $conf['update']             .= $column . ' = ' . (isset($_POST[$column]) ? 1 : 0) . ',';
        }
        $get_dupe = $this->db->query(
            'SELECT COUNT(*) FROM staff_roles WHERE LOWER(name) = \'' . strtolower($_POST['name']) . '\'' . ($role_id ? ' AND id <> ' . $role_id : '')
        );
        if ($this->db->fetch_single($get_dupe)) {
            $this->db->free_result($get_dupe);
            return [
                'type' => 'error',
                'message' => 'Another role with that name already exists',
            ];
        }
        $this->db->free_result($get_dupe);
        if (empty($role_id)) {
            $names  = implode(', ', $conf['insert']['cols']);
            $values = implode(', ', $conf['insert']['params']);
            $this->db->query(
                'INSERT INTO staff_roles (' . $names . ') VALUES (' . $values . ')'
            );
        } else {
            $this->db->query(
                'UPDATE staff_roles SET ' . rtrim($conf['update'], ', ') . ' WHERE id = ' . $role_id
            );
        }
        $log = $_GET['action'] . 'ed the staff role: ' . $_POST['name'];
        stafflog_add(ucfirst($log));
        $this->setRoles();
        return [
            'type' => 'success',
            'message' => $log,
        ];
    }

    /**
     * @return array
     */
    private function getPermCols(): array
    {
        $get_cols = $this->db->query(
            'SHOW COLUMNS FROM staff_roles'
        );
        $cols     = [];
        while ($row = $this->db->fetch_row($get_cols)) {
            if (in_array($row['Field'], ['id', 'name'])) {
                continue;
            }
            $cols[] = $row['Field'];
        }
        $this->db->free_result($get_cols);
        return $cols;
    }

    /**
     * @param int|null $role_id
     * @return string[]
     */
    private function doRemoveRole(?int $role_id): array
    {
        $role = $this->getRole($role_id);
        if (empty($role)) {
            return [
                'type' => 'error',
                'message' => 'The role you selected doesn\'t exist',
            ];
        }
        if (!array_key_exists('confirm', $_POST) || !$_POST['confirm']) {
            return [
                'type' => 'error',
                'message' => 'You must confirm the desire to remove the staff role: ' . $role['name'],
            ];
        }
        $this->db->query(
            'DELETE FROM users_roles WHERE staff_role = ' . $role_id,
        );
        $this->db->query(
            'DELETE FROM staff_roles WHERE id = ' . $role_id,
        );
        $log = 'deleted the staff role: ' . $role['name'];
        stafflog_add(ucfirst($log));
        $this->setRoles();
        return [
            'type' => 'success',
            'message' => 'You\'ve ' . $log,
        ];
    }

    /**
     * @param int $role_id
     * @return array|null
     */
    private function getRole(int $role_id): ?array
    {
        return $this->roles[$role_id] ?? null;
    }

    /**
     * @return string[]
     */
    private function doGrantRole(): array
    {
        $data = $this->processGrantRevokePostData();
        if ($data['type'] !== 'success') {
            return $data;
        }
        $get_has_role = $this->db->query(
            'SELECT COUNT(*) FROM users_roles WHERE userid = ' . $data['user']['userid'] . ' AND staff_role = ' . $data['role']['id'],
        );
        if ($this->db->fetch_single($get_has_role)) {
            return [
                'type' => 'error',
                'message' => $data['user']['username'] . ' already has the ' . $data['role']['name'] . ' role',
            ];
        }
        $this->db->query(
            'INSERT INTO users_roles (userid, staff_role) VALUES (' . $data['user']['userid'] . ', ' . $data['role']['id'] . ')',
        );
        $log = 'granted staff role ' . $data['role']['name'] . ' to ' . $data['user']['username'] . ' [' . $data['user']['userid'] . ']';
        stafflog_add(ucfirst($log));
        return [
            'type' => 'success',
            'message' => 'You\'ve ' . $log,
        ];
    }

    /**
     * @return array|string[]
     */
    private function processGrantRevokePostData(): array
    {
        $nums = ['user', 'role'];
        foreach ($nums as $num) {
            $_POST[$num] = array_key_exists($num, $_POST) && is_numeric($_POST[$num]) ? (int)$_POST[$num] : null;
        }
        if (empty($_POST['user'])) {
            return [
                'type' => 'error',
                'message' => 'Invalid user given',
            ];
        }
        if (empty($_POST['role'])) {
            return [
                'type' => 'error',
                'message' => 'Invalid role given',
            ];
        }
        $get_user = $this->db->query(
            'SELECT userid, username FROM users WHERE userid = ' . $_POST['user'],
        );
        $user     = $this->db->fetch_row($get_user);
        $this->db->free_result($get_user);
        if (empty($user)) {
            return [
                'type' => 'error',
                'message' => 'User not found',
            ];
        }
        $get_role = $this->db->query(
            'SELECT id, name FROM staff_roles WHERE id = ' . $_POST['role'],
        );
        $role     = $this->db->fetch_row($get_role);
        $this->db->free_result($get_role);
        if (empty($role)) {
            return [
                'type' => 'error',
                'message' => 'Role not found',
            ];
        }
        return [
            'type' => 'success',
            'user' => $user,
            'role' => $role,
        ];
    }

    /**
     * @return array|string[]
     */
    private function doRevokeRole(): array
    {
        $data = $this->processGrantRevokePostData();
        if ($data['type'] !== 'success') {
            return $data;
        }
        $get_has_role = $this->db->query(
            'SELECT COUNT(*) FROM users_roles WHERE userid = ' . $data['user']['userid'] . ' AND staff_role = ' . $data['role']['id'],
        );
        if (!$this->db->fetch_single($get_has_role)) {
            return [
                'type' => 'error',
                'message' => $data['user']['username'] . ' doesn\'t have the ' . $data['role']['name'] . ' role',
            ];
        }
        $this->db->query(
            'DELETE FROM users_roles WHERE userid = ' . $data['user']['userid'] . ' AND staff_role = ' . $data['role']['id'],
        );
        $log = 'revoked staff role ' . $data['role']['name'] . ' from ' . $data['user']['username'] . ' [' . $data['user']['userid'] . ']';
        stafflog_add(ucfirst($log));
        return [
            'type' => 'success',
            'message' => 'You\'ve ' . $log,
        ];
    }

    /**
     * @return void
     */
    private function viewGrantRole(): void
    {
        $template = file_get_contents($this->viewPath . '/staff-roles/role-grant.html');
        echo strtr($template, [
            '{{USER-MENU}}' => $this->renderUserMenuOpts(),
            '{{ROLE-MENU}}' => $this->renderRoleMenuOpts(),
        ]);
    }

    /**
     * @return string
     */
    private function renderUserMenuOpts(): string
    {
        $ret       = '';
        $get_users = $this->db->query(
            'SELECT userid, username FROM users ORDER BY username'
        );
        while ($row = $this->db->fetch_row($get_users)) {
            $ret .= '<option value="' . $row['userid'] . '">' . $row['username'] . '</option>';
        }
        $this->db->free_result($get_users);
        return $ret;
    }

    /**
     * @return string
     */
    private function renderRoleMenuOpts(): string
    {
        $ret   = '';
        $roles = $this->getRoles();
        foreach ($roles as $id => $role) {
            $ret .= '<option value="' . $id . '">' . $role['name'] . '</a>';
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return void
     */
    private function viewRevokeRole(): void
    {
        $template = file_get_contents($this->viewPath . '/staff-roles/role-revoke.html');
        echo strtr($template, [
            '{{USER-MENU}}' => $this->renderRoledUserMenuOpts(),
            '{{ROLE-MENU}}' => $this->renderRoleMenuOpts(),
        ]);
    }

    /**
     * @return string
     */
    private function renderRoledUserMenuOpts(): string
    {
        $ret       = '';
        $get_users = $this->db->query(
            'SELECT userid, username FROM users WHERE userid IN (SELECT userid FROM users_roles WHERE staff_role > 0) ORDER BY username'
        );
        while ($row = $this->db->fetch_row($get_users)) {
            $ret .= '<option value="' . $row['userid'] . '">' . $row['username'] . '</option>';
        }
        $this->db->free_result($get_users);
        return $ret;
    }

    /**
     * @return void
     */
    private function roleIndex(): void
    {
        $template = file_get_contents($this->viewPath . '/staff-roles/role-index.html');
        $roles    = $this->renderRoles();
        echo strtr($template, [
            '{{STAFF-ROLES}}' => $roles,
        ]);
    }

    /**
     * @return string
     */
    private function renderRoles(): string
    {
        $ret      = '';
        $template = file_get_contents($this->viewPath . '/staff-roles/role-index-entry.html');
        $roles    = $this->getRoles();
        foreach ($roles as $id => $role) {
            $ret .= strtr($template, [
                '{{ROLE-ID}}' => $id,
                '{{ROLE-NAME}}' => $role['name'],
                '{{ROLE-PERMISSIONS}}' => $this->expandPermissions($role),
            ]);
        }
        return $ret;
    }

    /**
     * @param array $role
     * @return string
     */
    private function expandPermissions(array $role): string
    {
        $has = [];
        foreach ($role as $key => $value) {
            if (in_array($key, ['id', 'name'])) {
                continue;
            }
            if ($key === 'administrator' && $value) {
                $has[] = '<em style="color:#008800;">all</em>';
                break;
            }
            if ($value) {
                $has[] = ucwords(str_replace('_', ' ', $key));
            }
        }
        return $has ? implode(', ', $has) : '<em style="color: #ff0000;">none</em>';
    }

    /**
     * @param int|null $role_id
     * @return void
     */
    private function viewUpsertRole(?int $role_id = null): void
    {
        if ($_GET['action'] === 'edit' && !$role_id) {
            $this->displayRoleSelectionMenu();
            return;
        }
        $role     = $role_id ? $this->getRole($role_id) : null;
        $template = file_get_contents($this->viewPath . '/staff-roles/role-upsert.html');
        echo strtr($template, [
            '{{ROLE-ID}}' => $role['id'] ?? '',
            '{{ROLE-NAME}}' => $role['name'] ?? '',
            '{{ROLE-PERMISSIONS}}' => $this->upsertRolePermissionsForm($role),
            '{{BTN-ACTION}}' => $role_id ? 'Edit' : 'Add',
            '{{FORM-ACTION}}' => $role_id ? 'staff_roles.php?action=edit&id=' . $role_id : 'staff_roles.php?action=add',
        ]);
    }

    /**
     * @return void
     */
    private function displayRoleSelectionMenu(): void
    {
        $template = file_get_contents($this->viewPath . '/staff-roles/role-selection-menu.html');
        echo strtr($template, [
            '{{FORM-LOCATION}}' => 'staff_roles.php',
            '{{FORM-ACTION}}' => $_GET['action'],
            '{{ROLES}}' => $this->renderRoleMenuOpts(),
        ]);
    }

    /**
     * @param array|null $role
     * @return string
     */
    private function upsertRolePermissionsForm(?array $role): string
    {
        $permission_columns = $this->getPermCols();
        $ret                = '';
        foreach ($permission_columns as $column) {
            $ret .= '
                <div style="display: inline-block; width: 33%;">
                    <label for="' . $column . '">
                        <input type="checkbox" name="' . $column . '" id="' . $column . '" value="1"' . ($role && $role[$column] ? ' checked' : '') . '>
                        ' . ucwords(str_replace('_', ' ', $column)) . '
                    </label>
                </div>
            ';
        }
        return $ret;
    }

    /**
     * @param int|null $role_id
     * @return void
     */
    private function viewRemoveRole(?int $role_id = null): void
    {
        if (!$role_id) {
            $this->displayRoleSelectionMenu();
            return;
        }
        $role     = $this->getRole($role_id);
        $template = file_get_contents($this->viewPath . '/staff-roles/role-remove.html');
        echo strtr($template, [
            '{{ROLE-NAME}}' => $role['name'],
            '{{FORM-ACTION}}' => 'staff_roles.php?action=remove&id=' . $role['id'],
        ]);
    }

    /**
     * @param database $db
     * @return self|null
     */
    public static function getInstance(database $db): ?self
    {
        if (self::$inst === null) {
            self::$inst = new self($db);
        }
        return self::$inst;
    }
}

$module = StaffRolesManagement::getInstance($db);
$h->endpage();
