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
 * File: macro1.php
 * Signature: 5f549f6de133f8528387fee2c6d13ec1
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
if (!$set['validate_on'] || $ir['verified'])
{
    echo "What are you doing on this page? Go somewhere else.";
    $h->endpage();
    exit;
}
if (!isset($_GET['refer']) || !is_string($_GET['refer']))
{
    echo "Invalid usage.";
    $h->endpage();
    exit;
}
unset($_SESSION['captcha']);
$chars =
        "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?\\/%^";
$len = strlen($chars);
$_SESSION['captcha'] = "";
for ($i = 0; $i < 6; $i++)
{
    $_SESSION['captcha'] .= $chars[rand(0, $len - 1)];
}
$valid_csrf = request_csrf_code('validation');
echo "<h3>Validation</h3><hr />
Enter the text you see in the image into the box below.
<form action='macro2.php' method='post'>";
if (isset($_GET['code']))
{
    echo "<font color='red'><b>Invalid code or blank</b></font><br />";
}
$_GET['refer'] =
        addslashes(
                htmlentities(stripslashes($_GET['refer']), ENT_QUOTES,
                        'ISO-8859-1'));
echo "
<img src='captcha_verify.php' alt='CAPTCHA - refresh if invisible' /><br />
Text: <input type='text' name='captcha' /><br />
<input type='hidden' name='verf' value='{$valid_csrf}' />
<input type='hidden' name='refer' value='{$_GET['refer']}' />
<input type='submit' value='Verify' /></form>";
$h->endpage();
