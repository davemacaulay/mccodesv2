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
    private ?headers $headers = null;
    private array $roles = [];

    /**
     * @param database $db
     * @param headers $headers
     */
    public function __construct(database $db, headers $headers)
    {
        $this->setViewPath();
        $this->setDb($db);
        $this->setHeaders($headers);
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
     * @param headers $headers
     * @return void
     */
    private function setHeaders(headers $headers): void
    {
        $this->headers = $headers;
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
     * @return void
     */
    private function roleIndex(): void
    {
        $template = file_get_contents($this->viewPath . '/staff-roles/role-index.php');
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
        $template = file_get_contents($this->viewPath . '/staff-roles/role-index-entry.php');
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

    }

    /**
     * @param database $db
     * @param headers $headers
     * @return self|null
     */
    public static function getInstance(database $db, headers $headers): ?self
    {
        if (self::$inst === null) {
            self::$inst = new self($db, $headers);
        }
        return self::$inst;
    }
}

$module = StaffRolesManagement::getInstance($db, $h);
