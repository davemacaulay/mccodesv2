<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $h, $set;
require_once('globals.php');
if (!isset($_GET['action']))
{
    ob_get_clean();
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if ($_GET['action'] == 'cancel')
{
    echo 'You have cancelled your donation. Please donate later...';
} elseif ($_GET['action'] == 'done') {
    if (!$_GET['tx']) {
        echo 'Get a life.';
        $h->endpage();
        exit;
    }
    echo 'Thank you for your payment to ' . $set['game_name']
        . '. Your transaction has been completed, and a receipt for
            your purchase has been emailed to you. You may log into your
            account at <a href="https://www.paypal.com">www.paypal.com</a>
            to view details of this transaction.
            Your Will Potion should be credited within a few minutes,
            if not, contact an admin for assistance.';
}
$h->endpage();
