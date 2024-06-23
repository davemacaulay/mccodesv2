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
            if ($packr[1] != 'DP') {
                fclose($fp);
                die('');
            }
            $pack = $packr[2];
            if ($pack != 1 and $pack != 2 and $pack != 3 and $pack != 4
                and $pack != 5) {
                fclose($fp);
                die('');
            }
            if (($pack == 1 || $pack == 2 || $pack == 3)
                && $payment_amount != '3.00') {
                fclose($fp);
                die('');
            }
            if ($pack == 4 && $payment_amount != '5.00') {
                fclose($fp);
                die('');
            }
            if ($pack == 5 && $payment_amount != '10.00') {
                fclose($fp);
                die('');
            }
            // grab IDs
            $buyer = abs((int)$packr[3]);
            $for   = $buyer;
            $t = '';
            // all seems to be in order, credit it.
            if ($pack == 1) {
                $db->query(
                    "UPDATE `users` AS `u`
                         LEFT JOIN `userstats` AS `us`
                         ON `u`.`userid` = `us`.`userid`
                         SET `u`.`money` = `u`.`money` + 5000,
                         `u`.`crystals` = `u`.`crystals` + 50,
                         `us`.`IQ` = `us`.`IQ` + 50,
                         `u`.`donatordays` = `u`.`donatordays` + 30
                         WHERE `u`.`userid` = {$for}");
                $d = 30;
                $t = 'standard';
            } elseif ($pack == 2) {
                $db->query(
                    "UPDATE `users` AS `u`
                         SET `u`.`crystals` = `u`.`crystals` + 100,
                         `u`.`donatordays` = `u`.`donatordays` + 30
                         WHERE `u`.`userid` = {$for}");
                $d = 30;
                $t = 'crystals';
            } elseif ($pack == 3) {
                $db->query(
                    "UPDATE `users` AS `u`
                         LEFT JOIN `userstats` AS `us`
                         ON `u`.`userid` = `us`.`userid`
                         SET `us`.`IQ` = `us`.`IQ` + 50,
                         `u`.`donatordays` = `u`.`donatordays` + 30
                         WHERE `u`.`userid` = {$for}");
                $d = 30;
                $t = 'iq';
            } elseif ($pack == 4) {
                $db->query(
                    "UPDATE `users` AS `u`
                         LEFT JOIN `userstats` AS `us`
                         ON `u`.`userid` = `us`.`userid`
                         SET `u`.`money` = `u`.`money` + 15000,
                         `u`.`crystals` = `u`.`crystals` + 75,
                         `us`.`IQ` = `us`.`IQ` + 80,
                         `u`.`donatordays` = `u`.`donatordays` + 55
                         WHERE `u`.`userid` = {$for}");
                $d = 55;
                $t = 'fivedollars';
            } elseif ($pack == 5) {
                $db->query(
                    "UPDATE `users` AS `u`
                         LEFT JOIN `userstats` AS `us`
                         ON `u`.`userid` = `us`.`userid`
                         SET `u`.`money` = `u`.`money` + 35000,
                         `u`.`crystals` = `u`.`crystals` + 160,
                         `us`.`IQ` = `us`.`IQ` + 180,
                         `u`.`donatordays` = `u`.`donatordays` + 115
                         WHERE `u`.`userid` = {$for}");
                $d = 115;
                $t = 'tendollars';
            }
            // process payment
            event_add($for,
                "Your \${$payment_amount} Pack {$pack} Donator Pack has been successfully credited to you.");
            $db->query(
                "INSERT INTO `dps_accepted`
                     VALUES(NULL, {$buyer}, {$for}, '$t', " . time()
                . ", '$txn_db')");
        }
    }

    fclose($fp);
}
