<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

session_name('MCCSID');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
global $db;
require_once('global_func.php');
if (isset($_SESSION['userid']))
{
    $sessid = (int) $_SESSION['userid'];
    if (isset($_SESSION['attacking']) && $_SESSION['attacking'] > 0)
    {
        echo 'You lost all your EXP for running from the fight.<br />';
        require_once('globals_nonauth.php');
        $db->query(
                "UPDATE `users`
                 SET `exp` = 0, `attacking` = 0
                 WHERE `userid` = {$sessid}");
        $_SESSION['attacking'] = 0;
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        die("<a href='login.php'>Continue to login...</a>");
    }
}
session_regenerate_id(true);
session_unset();
session_destroy();
$login_url = 'https://' . determine_game_urlbase() . '/login.php';
header("Location: {$login_url}");
