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
 * File: macro2.php
 * Signature: 9d21b98c124cf2068be9c6f86c4282d4
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

$nohdr = 1;
global $db, $ir, $userid, $set, $domain;
require_once('globals.php');
if (!$set['validate_on'] || $ir['verified'])
{
    echo 'What are you doing on this page? Go somewhere else.';
    exit;
}
if (!isset($_POST['refer']) || !is_string($_POST['refer'])
        || !isset($_POST['captcha']) || !is_string($_POST['captcha']))
{
    echo 'Invalid usage.';
    exit;
}
$macro1_url =
        "https://{$domain}/macro1.php?code=invalid&amp;refer="
                . urlencode(stripslashes($_POST['refer']));
if (!isset($_SESSION['captcha']))
{
    header("Location: {$macro1_url}");
    exit;
}
if ($_SESSION['captcha'] != stripslashes($_POST['captcha']))
{
    header("Location: {$macro1_url}");
    exit;
}
if (!isset($_POST['verf'])
        || !verify_csrf_code('validation', stripslashes($_POST['verf'])))
{
    header("Location: {$macro1_url}");
    exit;
}
$ref = $_POST['refer'];
unset($_SESSION['captcha']);
$dest_url = "https://{$domain}/{$ref}";
$db->query(
        "UPDATE `users`
		 SET `verified` = 1
		 WHERE `userid` = {$userid}");
header("Location: {$dest_url}");
