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
 * File: itemsell.php
 * Signature: 8d8c2e4c6ad716a423bdf308828b8af7
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
$_POST['qty'] =
        (isset($_POST['qty']) && is_numeric($_POST['qty']))
                ? abs(intval($_POST['qty'])) : '';
if (!empty($_POST['qty']) && !empty($_GET['ID']))
{
    $id =
            $db->query(
                    "SELECT `inv_qty`, `itmsellprice`, `itmid`, `itmname`
                     FROM `inventory` AS `iv`
                     INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid`
                     WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND `iv`.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $r = $db->fetch_row($id);
        if (!isset($_POST['verf'])
                || !verify_csrf_code("sellitem_{$_GET['ID']}",
                        stripslashes($_POST['verf'])))
        {
            echo '<h3>Error</h3><hr />
    		This transaction has been blocked for your security.<br />
    		Please sell items quickly after you open the form - do not leave it open in tabs.<br />
    		&gt; <a href="itemsell.php?ID=' . $_GET['ID'] . '">Try Again</a>';
            die($h->endpage());
        }
        if ($_POST['qty'] > $r['inv_qty'])
        {
            echo 'You are trying to sell more than you have!';
        }
        else
        {
            $price = $r['itmsellprice'] * $_POST['qty'];
            item_remove($userid, $r['itmid'], $_POST['qty']);
            $db->query(
                    "UPDATE `users`
                     SET `money` = `money` + {$price}
                     WHERE `userid` = $userid");
            $priceh = money_formatter($price);
            echo 'You sold ' . $_POST['qty'] . ' ' . $r['itmname']
                    . '(s) for ' . $priceh;
            $is_log =
                    $db->escape(
                            "{$ir['username']} sold {$_POST['qty']} {$r['itmname']}(s) for {$priceh}");
            $db->query(
                    "INSERT INTO `itemselllogs`
                    VALUES (NULL, $userid, {$r['itmid']}, $price,
                    {$_POST['qty']}, " . time() . ", '{$is_log}')");
        }
    }
    $db->free_result($id);
}
else if (!empty($_GET['ID']) && empty($_POST['qty']))
{
    $id =
            $db->query(
                    "SELECT `inv_qty`, `itmname`
                     FROM `inventory` AS `iv`
                     INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid`
                     WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND `iv`.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $r = $db->fetch_row($id);
        $code = request_csrf_code("sellitem_{$_GET['ID']}");
        echo "
		<b>Enter how many {$r['itmname']} you want to sell. You have {$r['inv_qty']} to sell.</b>
		<br />
		<form action='itemsell.php?ID={$_GET['ID']}' method='post'>
			<input type='hidden' name='verf' value='{$code}' />
			Quantity: <input type='text' name='qty' value='' />
			<br />
			<input type='submit' value='Sell Items (no prompt so be sure!' />
		</form>
   		";
    }
    $db->free_result($id);
}
else
{
    echo 'Invalid use of file.';
}
$h->endpage();
