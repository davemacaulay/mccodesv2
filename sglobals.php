<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

function staff_csrf_error($goBackTo): void
{
    global $h;
    echo '<h3>Error</h3><hr />
    Your action has been blocked for security reasons.<br />
    &gt; <a href="' . $goBackTo . '">Try Again</a>';
    $h->endpage();
    exit;
}

/**
 * Check the CSRF code we received against the one that was registered for the form - using default code properties ($_POST['verf']).
 * If verification fails, end execution immediately.
 * If not, continue.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @param $goBackTo
 * @return bool Whether the user provided a valid code or not
 */
function staff_csrf_stdverify(string $formid, $goBackTo): bool
{
    if (!isset($_POST['verf'])
            || !verify_csrf_code($formid, stripslashes($_POST['verf'])))
    {
        staff_csrf_error($goBackTo);
    }
    return true;
}
if (str_contains($_SERVER['PHP_SELF'], 'sglobals.php'))
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
$userid = (int)($_SESSION['userid'] ?? 0);
require 'header.php';

include 'config.php';
global $_CONFIG;
const MONO_ON = 1;
require "class/class_db_{$_CONFIG['driver']}.php";
$db = new database();
$db->configure($_CONFIG['hostname'], $_CONFIG['username'],
        $_CONFIG['password'], $_CONFIG['database']);
$db->connect();
$c = $db->connection_id;
$set = get_site_settings();
if ($set['use_timestamps_over_crons']) {
    define('SILENT_CRONS', true);
    try {
        require_once __DIR__ . '/crons/cronless_crons.php';
    } catch (Exception $e) {
        echo 'An error occurred' . (defined('DEBUG') && DEBUG ? ':<br>'.$e->getMessage() : '');
        exit;
    }
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
                     WHERE `u`.`userid` = '{$userid}'
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
                     WHERE `u`.`userid` = '{$userid}'
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
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
}
$ir = $db->fetch_row($is);
set_userdata_data_types($ir);
if ($ir['force_logout'] > 0)
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
if (!is_staff())
{
    echo 'This page cannot be accessed.<br />&gt; <a href="index.php">Go Home</a>';
    die;
}
check_level();
$h = new headers();
if (!isset($nohdr) || !$nohdr) {
    $h->startheaders();
    $fm = money_formatter($ir['money']);
    $cm = money_formatter($ir['crystals'], '');
    $lv = date('F j, Y, g:i a', $ir['laston']);
    global $atkpage;
    $staffpage = 1;
    if ($atkpage) {
        $h->userdata($ir, $lv, $fm, $cm, 0);
    } else {
        $h->userdata($ir, $lv, $fm, $cm);
    }
    $h->smenuarea();
}
