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
 * File: checkem.php
 * Signature: 9e3cc733331f79cfe0f3dde472f1f004
 * Date: Fri, 20 Apr 12 08:50:30 +0000
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
require_once('global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}

function valid_email($email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email);
}

require_once('globals_nonauth.php');
$email = isset($_POST['email']) ? stripslashes($_POST['email']) : '';
if (empty($email))
{
    die("<font color='red'>Invalid - Blank</font>");
}
if (!valid_email($email))
{
    die("<font color='red'>Invalid - Bad Format</font>");
}
$e_email = $db->escape($email);
$q =
        $db->query(
                "SELECT COUNT(`userid`) FROM users WHERE `email` = '{$e_email}'");
if ($db->fetch_single($q) != 0)
{
    echo '<font color=\'red\'>Invalid - Already In Use</font>';
}
else
{
    echo '<font color=\'green\'>Valid</font>';
}
$db->free_result($q);
