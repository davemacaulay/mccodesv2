<?php
declare(strict_types=1);
global $db, $ir, $h;
require __DIR__ . '/sglobals.php';
check_access('manage_roles');

/**
 *
 */
class StaffRolesManagement
{
    private static ?self $inst = null;
    private string $viewPath = '';
    private ?database $mysql = null;
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
        $this->mysql = $db;
    }

    /**
     * @return void
     */
    private function setRoles(): void
    {
        $get_roles = $this->mysql->query(
            'SELECT * FROM staff_roles ORDER BY id'
        );
        while ($row = $this->mysql->fetch_row($get_roles)) {
            $this->roles[$row['id']] = $row;
        }
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
                $this->roleIndex();
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
                'params' => ['\'' . $this->mysql->escape($_POST['name']) . '\''],
            ],
            'update' => 'name = \'' . $this->mysql->escape($_POST['name']) . '\'',
        ];
        foreach ($permission_columns as $column) {
            $_POST[$column]             = array_key_exists($column, $_POST) ? strip_tags(trim($_POST[$column])) : null;
            $conf['insert']['cols'][]   = $column;
            $conf['insert']['params'][] = isset($_POST[$column]) ? 1 : 0;
            $conf['update']             .= $column . ' = ' . (isset($_POST[$column]) ? 1 : 0) . ',';
        }
        $get_dupe = $this->mysql->query(
            'SELECT COUNT(*) FROM staff_roles WHERE LOWER(name) = \'' . strtolower($_POST['name']) . '\'' . ($role_id ? ' AND id <> ' . $role_id : '')
        );
        if ($this->mysql->fetch_single($get_dupe)) {
            return [
                'type' => 'error',
                'message' => 'Another role with that name already exists',
            ];
        }
        if (empty($role_id)) {
            $names  = implode(', ', $conf['insert']['cols']);
            $values = implode(', ', $conf['insert']['params']);
            $this->mysql->query(
                'INSERT INTO staff_roles (' . $names . ') VALUES (' . $values . ')'
            );
        } else {
            $this->mysql->query(
                'UPDATE staff_roles SET ' . rtrim($conf['update'], ', ') . ' WHERE id = ' . $role_id
            );
        }
        stafflog_add(ucfirst($_GET['action']) . 'ed staff role: ' . $_POST['name']);
        $this->setRoles();
        return [
            'type' => 'success',
            'message' => $_POST['name'] . ' role successfully ' . $_GET['action'] . 'ed',
        ];
    }

    /**
     * @return array
     */
    private function getPermCols(): array
    {
        $get_cols = $this->mysql->query(
            'SHOW COLUMNS FROM staff_roles'
        );
        $cols     = [];
        while ($row = $this->mysql->fetch_row($get_cols)) {
            if (in_array($row['Field'], ['id', 'name'])) {
                continue;
            }
            $cols[] = $row['Field'];
        }
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
        $this->mysql->query(
            'DELETE FROM users_roles WHERE staff_role = ' . $role_id,
        );
        $this->mysql->query(
            'DELETE FROM staff_roles WHERE id = ' . $role_id,
        );
        stafflog_add('Deleted staff role: ' . $role['name']);
        $this->setRoles();
        return [
            'type' => 'success',
            'message' => 'The staff role "' . $role['name'] . '" has been deleted',
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
        foreach ($this->roles as $id => $role) {
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
     * @return string
     */
    private function renderRoleMenuOpts(): string
    {
        $ret = '';
        foreach ($this->roles as $id => $role) {
            $ret .= '<option value="' . $id . '">' . $role['name'] . '</a>';
        }
        return $ret;
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
                <div class="form-group">
                    <label for="' . $column . '" class="switch">
                        <input type="checkbox" name="' . $column . '" id="' . $column . '" value="1"' . ($role && $role[$column] ? ' checked' : '') . '>
                        <span class="slider round"></span>
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
