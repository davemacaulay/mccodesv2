<?php
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
 * File: logout.php
 * Signature: 9416661a1a2a397cc7695f6bb952fcaf
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

session_name('MCCSID');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
require_once('global_func.php');
if (isset($_SESSION['userid']))
{
    $sessid = (int) $_SESSION['userid'];
    if (isset($_SESSION['attacking']) && $_SESSION['attacking'] > 0)
    {
        echo "You lost all your EXP for running from the fight.<br />";
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
$login_url = 'http://' . determine_game_urlbase() . '/login.php';
header("Location: {$login_url}");
