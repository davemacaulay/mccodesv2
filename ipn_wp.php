<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $set;
require_once('globals_nonauth.php');
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value)
{
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if ($fp) {
    fputs($fp, $header . $req);
    while (!feof($fp)) {
        $res = fgets($fp, 1024);
        if (strcmp($res, 'VERIFIED') == 0) {
            $txn_db = $db->escape(stripslashes($txn_id));
            // check the payment_status is Completed
            if ($payment_status != 'Completed') {
                fclose($fp);
                die('');
            }
            $dp_check =
                $db->query(
                    "SELECT COUNT(`dpID`)
                             FROM `dps_accepted`
                             WHERE `dpTXN` = '{$txn_db}'");
            if ($db->fetch_single($dp_check) > 0) {
                $db->free_result($dp_check);
                fclose($fp);
                die('');
            }
            $db->free_result($dp_check);
            $wp_check =
                $db->query(
                    "SELECT COUNT(`dpID`)
                             FROM `willps_accepted`
                             WHERE `dpTXN` = '{$txn_db}'");
            if ($db->fetch_single($wp_check) > 0) {
                $db->free_result($wp_check);
                fclose($fp);
                die('');
            }
            $db->free_result($wp_check);
            // check that txn_id has not been previously processed
            // check that receiver_email is your Primary PayPal email
            if ($receiver_email != $set['paypal']) {
                fclose($fp);
                die('');
            }
            // check that payment_amount/payment_currency are correct
            if ($payment_currency != 'USD') {
                fclose($fp);
                die('');
            }
            // parse for pack
            $packr = explode('|', $item_name);
            if (str_replace('www.', '', $packr[0])
                != str_replace('www.', '', $_SERVER['HTTP_HOST'])) {
                fclose($fp);
                die('');
            }
            if ($packr[1] != 'WP') {
                fclose($fp);
                die('');
            }
            $pack = $packr[2];
            if ($pack != 1 and $pack != 5) {
                fclose($fp);
                die('');
            }
            if (($pack == 1) && $payment_amount != '1.00') {
                fclose($fp);
                die('');
            }
            if ($pack == 5 && $payment_amount != '4.50') {
                fclose($fp);
                die('');
            }
            // grab IDs
            $buyer = abs((int)$packr[3]);
            $for   = $buyer;
            // all seems to be in order, credit it.
            if ($pack == 1) {
                item_add($for, $set['willp_item'], 1);

            } elseif ($pack == 5) {
                item_add($for, $set['willp_item'], 5);

            }
            // process payment

            event_add($for,
                "Your \${$payment_amount} worth of Will Potions ($pack) has been successfully credited.");
            $db->query(
                "INSERT INTO `willps_accepted`
                     VALUES(NULL, {$buyer}, {$for}, '$pack', " . time()
                . ", '$txn_db')");
        }
    }
    fclose($fp);
}
