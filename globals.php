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
 * File: globals.php
 * Signature: 7b716433581858a154d2ae09ab90f269
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

if (str_contains($_SERVER['PHP_SELF'], 'globals.php'))
{
    exit;
}
session_name('MCCSID');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
require 'lib/basic_error_handler.php';
set_error_handler('error_php');
require 'global_func.php';
$domain = determine_game_urlbase();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0)
{
    $login_url = "https://{$domain}/login.php";
    header("Location: {$login_url}");
    exit;
}
$userid = $_SESSION['userid'] ?? 0;
require 'header.php';

global $_CONFIG;
include 'config.php';
const MONO_ON = 1;
require "class/class_db_{$_CONFIG['driver']}.php";
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
global $jobquery, $housequery;
if (isset($jobquery) && $jobquery)
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*, `j`.*, `jr`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `jobs` AS `j` ON `j`.`jID` = `u`.`job`
                     LEFT JOIN `jobranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
elseif (isset($housequery) && $housequery)
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*, `h`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `houses` AS `h` ON `h`.`hWILL` = `u`.`maxwill`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
else
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
$ir = $db->fetch_row($is);
if ($ir['force_logout'] != '0')
{
    $db->query(
            "UPDATE `users`
    			SET `force_logout` = 0
    			WHERE `userid` = {$userid}");
    session_unset();
    session_destroy();
    $login_url = "https://{$domain}/login.php";
    header("Location: {$login_url}");
    exit;
}
global $macropage;
if ($macropage && !$ir['verified'] && $set['validate_on'] == 1)
{
    $macro_url = "https://{$domain}/macro1.php?refer=$macropage";
    header("Location: {$macro_url}");
    exit;
}
check_level();
$h = new headers();
if (!isset($nohdr) || !$nohdr)
{
    $h->startheaders();
    $fm = money_formatter($ir['money']);
    $cm = money_formatter($ir['crystals'], '');
    $lv = date('F j, Y, g:i a', (int)$ir['laston']);
    global $atkpage;
    if ($atkpage)
    {
        $h->userdata($ir, $lv, $fm, $cm, 0);
    }
    else
    {
        $h->userdata($ir, $lv, $fm, $cm);
    }
    global $menuhide;
    if (!$menuhide)
    {
        $h->menuarea();
    }
}
