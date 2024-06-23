<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
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
