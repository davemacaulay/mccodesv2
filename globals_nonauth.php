<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

if (str_contains($_SERVER['PHP_SELF'], 'globals_nonauth.php'))
{
    exit;
}
session_name('MCCSID');
@session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
require 'lib/basic_error_handler.php';
set_error_handler('error_php');
global $_CONFIG;
include 'config.php';
const MONO_ON = 1;
require "class/class_db_{$_CONFIG['driver']}.php";
require_once('global_func.php');
$db = new database();
$db->configure($_CONFIG['hostname'], $_CONFIG['username'],
        $_CONFIG['password'], $_CONFIG['database']);
$db->connect();
$c = $db->connection_id;
$set = [];
$settq = $db->query('SELECT *
					 FROM `settings`');
while ($r = $db->fetch_row($settq))
{
    $set[$r['conf_name']] = $r['conf_value'];
}
