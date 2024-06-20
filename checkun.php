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
 * File: checkun.php
 * Signature: ff59616a71ebcae082693e1df81e2789
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 * @noinspection SpellCheckingInspection
 */

if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
global $db;
require_once('global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
require_once('globals_nonauth.php');
$username =
        isset($_POST['username']) ? stripslashes($_POST['username']) : '';
if (!$username)
{
    die("<font color='red'>Invalid - Blank</font>");
}
if ((strlen($username) < 3))
{
    die("<font color='red'>Invalid - Too Short</font>");
}
if ((strlen($username) > 31))
{
    die("<font color='red'>Invalid - Too Long</font>");
}
$e_username = $db->escape($username);
$q =
        $db->query(
                "SELECT COUNT(`userid`) FROM users WHERE login_name = '{$e_username}' OR username = '{$e_username}'");
if ($db->fetch_single($q))
{
    echo '<font color=\'red\'>Invalid - Taken</font>';
}
else
{
    echo '<font color=\'green\'>Valid</font>';
}
$db->free_result($q);
