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
 * File: itembuy.php
 * Signature: 11433e8545f9a9c326162d5fe49c239a
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $userid, $h;
require_once('globals.php');

$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
$_POST['qty'] =
        (isset($_POST['qty']) && is_numeric($_POST['qty']))
                ? abs(intval($_POST['qty'])) : '';

if (empty($_GET['ID']) OR empty($_POST['qty']))
{
    echo 'Invalid use of file';
}
else
{
    $q =
            $db->query(
                    "SELECT `itmid`, `itmbuyprice`, `itmname`, `itmbuyable`, `shopLOCATION`
                     FROM `shopitems` AS `si`
                     INNER JOIN `shops` AS `s`
                     ON `si`.`sitemSHOP` = `s`.`shopID`
                     INNER JOIN `items` AS `i`
                     ON `si`.`sitemITEMID` = `i`.`itmid`
                     WHERE `sitemID` = {$_GET['ID']}");
    if ($db->num_rows($q) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $itemd = $db->fetch_row($q);
        if ($ir['money'] < ($itemd['itmbuyprice'] * $_POST['qty']))
        {
            echo 'You don\'t have enough money to buy ' . $_POST['qty'] . ' '
                    . $itemd['itmname']
                    . '!<br />&gt; <a href="index.php">Go Home</a>';
            die($h->endpage());
        }
        if ($itemd['itmbuyable'] == 0)
        {
            echo 'This item can\'t be bought!
            <br />&gt; <a href="index.php">Go Home</a>';
            die($h->endpage());
        }
        if ($itemd['shopLOCATION'] != $ir['location'])
        {
            echo 'You can\'t buy items from other cities.
            <br />&gt; <a href="index.php">Go Home</a>';
            die($h->endpage());
        }

        $price = ($itemd['itmbuyprice'] * $_POST['qty']);
        item_add($userid, $itemd['itmid'], $_POST['qty']);
        $db->query(
                "UPDATE `users`
        		 SET `money` = `money` - $price
        		 WHERE `userid` = $userid");
        $ib_log =
                $db->escape(
                        "{$ir['username']} bought {$_POST['qty']} "
                                . "{$itemd['itmname']}(s) for {$price}");
        $db->query(
                "INSERT INTO `itembuylogs`
                 VALUES (NULL, $userid, {$itemd['itmid']}, $price, {$_POST['qty']},
                  " . time() . ", '{$ib_log}')");
        echo 'You bought ' . $_POST['qty'] . ' ' . $itemd['itmname'] . ' '
                . (($_POST['qty'] > 1) ? 's' : '') . ' for '
                . money_formatter($price)
                . '<br />&gt; <a href="inventory.php">Goto your inventory</a>';
    }
    $db->free_result($q);
}
$h->endpage();
