<?php
declare(strict_types=1);
/**
 * MCCodes Version 2.0.5b
 * Copyright (C) 2005-2012 Dabomstew
 * All rights reserved.
 *
 * Redistribution of this code in any form is prohibited, except in
 * the specific cases set out in the MCCodes Customer License.
 *
 * This code license may be used to run one (1) game.
 * A game is defined as the set of users and other game database data,
 * so you are permitted to create alternative clients for your game.
 *
 * If you did not obtain this code from MCCodes.com, you are in all likelihood
 * using it illegally. Please contact MCCodes to discuss licensing options
 * in this case.
 *
 * File: class/class_db_mysqli.php
 * Signature: 0bd885c66484350e8b0130c39e932e20
 * Date: Fri, 20 Apr 12 08:50:30 +0000
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
        debug_backtrace(false));
}

class database
{
    public string $host;
    public string $user;
    public string $pass;
    public string $database;
    public string $last_query;
    public mysqli_result|bool $result;
    public mysqli $connection_id;
    public int $num_queries = 0;
    public array $queries = [];

    public function configure($host, $user, $pass, $database): int
    {
        $this->host     = $host;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->database = $database;
        return 1; //Success.
    }

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
                debug_backtrace(false));
        }
        // @overridecharset mysqli
        $this->connection_id = $conn;
        return $this->connection_id;
    }

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

    public function change_db($database): void
    {
        if (!mysqli_select_db($this->connection_id, $database)) {
            error_critical(mysqli_errno($this->connection_id) . ': '
                . mysqli_error($this->connection_id),
                'Attempted to select database: ' . $database,
                debug_backtrace(false));
        }
        $this->database = $database;
    }

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
                debug_backtrace(false));
        }
        return $this->result;
    }

    public function fetch_row($result = 0): false|array|null
    {
        if (!$result) {
            $result = $this->result;
        }
        return mysqli_fetch_assoc($result);
    }

    public function num_rows($result = 0): int|string
    {
        if (!$result) {
            $result = $this->result;
        }
        return mysqli_num_rows($result);
    }

    public function insert_id(): int|string
    {
        return mysqli_insert_id($this->connection_id);
    }

    public function fetch_single($result = 0)
    {
        if (!$result) {
            $result = $this->result;
        }
        //Ugly hack here
        mysqli_data_seek($result, 0);
        $temp = mysqli_fetch_array($result);
        return $temp[0];
    }

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

    public function escape($text): string
    {
        return mysqli_real_escape_string($this->connection_id, $text);
    }

    public function affected_rows(): int|string
    {
        return mysqli_affected_rows($this->connection_id);
    }

    public function free_result($result): void
    {
        mysqli_free_result($result);
    }

}
