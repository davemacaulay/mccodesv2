<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
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
            $h->endpage();
            exit;
        }
        if ($itemd['itmbuyable'] == 0)
        {
            echo 'This item can\'t be bought!
            <br />&gt; <a href="index.php">Go Home</a>';
            $h->endpage();
            exit;
        }
        if ($itemd['shopLOCATION'] != $ir['location'])
        {
            echo 'You can\'t buy items from other cities.
            <br />&gt; <a href="index.php">Go Home</a>';
            $h->endpage();
            exit;
        }

        $price = (int)($itemd['itmbuyprice'] * $_POST['qty']);
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
