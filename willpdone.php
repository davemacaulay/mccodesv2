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
 * File: willpdone.php
 * Signature: 14b8e9d303477a3436372ee636523591
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
if (!isset($_GET['action']))
{
    ob_get_clean();
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if ($_GET['action'] == "cancel")
{
    echo 'You have cancelled your donation. Please donate later...';
}
else if ($_GET['action'] == "done")
{
    if (!$_GET['tx'])
    {
        echo 'Get a life.';
        die($h->endpage());
    }
    echo 'Thank you for your payment to ' . $set['game_name']
            . '. Your transaction has been completed, and a receipt for
            your purchase has been emailed to you. You may log into your
            account at <a href="http://www.paypal.com">www.paypal.com</a>
            to view details of this transaction.
            Your Will Potion should be credited within a few minutes,
            if not, contact an admin for assistance.';
}
$h->endpage();
