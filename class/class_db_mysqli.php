<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

if (!defined('MONO_ON')) {
    exit;
}

if (!function_exists('error_critical')) {
    // Umm...
    die('<h1>Error</h1>' . 'Error handler not present');
}

if (!extension_loaded('mysqli')) {
    // dl doesn't work anymore, crash
    error_critical('MySQLi extension not present but required', 'N/A',
        debug_backtrace());
}

/**
 *
 */
class database
{
    public string $host;
    public string $user;
    public string $pass;
    public string $database;
    public string $last_query;
    public mysqli_result|bool $result;
    public mysqli|int $connection_id;
    public int $num_queries = 0;
    public array $queries = [];

    /**
     * @param $host
     * @param $user
     * @param $pass
     * @param $database
     * @return int
     */
    public function configure($host, $user, $pass, $database): int
    {
        $this->host     = $host;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->database = $database;
        return 1; //Success.
    }

    /**
     * @return false|mysqli
     */
    public function connect(): false|mysqli
    {
        if (!$this->host) {
            $this->host = 'localhost';
        }
        if (!$this->user) {
            $this->user = 'root';
        }
        $conn =
            mysqli_connect($this->host, $this->user, $this->pass,
                $this->database);
        if (mysqli_connect_error()) {
            error_critical(mysqli_connect_errno() . ': ' . mysqli_connect_error(),
                'Attempted to connect to database on ' . $this->host,
                debug_backtrace());
        }
        // @overridecharset mysqli
        $this->connection_id = $conn;
        return $this->connection_id;
    }

    /**
     * @return int
     */
    public function disconnect(): int
    {
        if ($this->connection_id) {
            mysqli_close($this->connection_id);
            $this->connection_id = 0;
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param $database
     * @return void
     */
    public function change_db($database): void
    {
        if (!mysqli_select_db($this->connection_id, $database)) {
            error_critical(mysqli_errno($this->connection_id) . ': '
                . mysqli_error($this->connection_id),
                'Attempted to select database: ' . $database,
                debug_backtrace());
        }
        $this->database = $database;
    }

    /**
     * @param $query
     * @return mysqli_result|bool
     */
    public function query($query): mysqli_result|bool
    {
        $this->last_query = $query;
        $this->queries[]  = $query;
        $this->num_queries++;
        $this->result =
            mysqli_query($this->connection_id, $this->last_query);
        if ($this->result === false) {
            error_critical(mysqli_errno($this->connection_id) . ': '
                . mysqli_error($this->connection_id),
                'Attempted to execute query: ' . nl2br($this->last_query),
                debug_backtrace());
        }
        return $this->result;
    }

    /**
     * @param mysqli_result|int $result
     * @return false|array|null
     */
    public function fetch_row(mysqli_result|int $result = 0): false|array|null
    {
        if (!$result) {
            $result = $this->result;
        }
        return mysqli_fetch_assoc($result);
    }

    /**
     * @param mysqli_result|int $result
     * @return int|string
     */
    public function num_rows(mysqli_result|int $result = 0): int|string
    {
        if (!$result) {
            $result = $this->result;
        }
        return mysqli_num_rows($result);
    }

    /**
     * @return int|string
     */
    public function insert_id(): int|string
    {
        return mysqli_insert_id($this->connection_id);
    }

    /**
     * @param mysqli_result|int $result
     * @return mixed
     */
    public function fetch_single(mysqli_result|int $result = 0): mixed
    {
        if (!$result) {
            $result = $this->result;
        }
        //Ugly hack here
        mysqli_data_seek($result, 0);
        $temp = mysqli_fetch_array($result);
        return $temp[0];
    }

    /**
     * @param $table
     * @param $data
     * @return mysqli_result|bool
     */
    public function easy_insert($table, $data): mysqli_result|bool
    {
        $query = "INSERT INTO `$table` (";
        $i     = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $query .= ', ';
            }
            $query .= $k;
        }
        $query .= ') VALUES(';
        $i     = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $query .= ', ';
            }
            $query .= "'" . $this->escape($v) . "'";
        }
        $query .= ')';
        return $this->query($query);
    }

    /**
     * @param $text
     * @return string
     */
    public function escape($text): string
    {
        return mysqli_real_escape_string($this->connection_id, $text);
    }

    /**
     * @return int|string
     */
    public function affected_rows(): int|string
    {
        return mysqli_affected_rows($this->connection_id);
    }

    /**
     * @param mysqli_result|int $result
     * @return void
     */
    public function free_result(mysqli_result|int $result): void
    {
        mysqli_free_result($result);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        call_user_func_array([$this->connection_id, $name], $arguments);
    }

}
