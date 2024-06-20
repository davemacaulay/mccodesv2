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
 * File: itemsend.php
 * Signature: 234407cd27e8de9699347d210d9f6f1e
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
$_POST['user'] =
        (isset($_POST['user']) && is_numeric($_POST['user']))
                ? abs(intval($_POST['user'])) : '';
$_POST['qty'] =
        (isset($_POST['qty']) && is_numeric($_POST['qty']))
                ? abs(intval($_POST['qty'])) : '';
if (!empty($_POST['qty']) && !empty($_POST['user']))
{
    $id =
            $db->query(
                    "SELECT `inv_qty`, `inv_itemid`, `itmname`, `itmid`
                     FROM `inventory` AS `iv`
                     INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid`
                     WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND iv.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $r = $db->fetch_row($id);
        $m =
                $db->query(
                        "SELECT `lastip`,`username`
                         FROM `users`
                         WHERE `userid` = {$_POST['user']}
                         LIMIT 1");
        if (!isset($_POST['verf'])
                || !verify_csrf_code("senditem_{$_GET['ID']}",
                        stripslashes($_POST['verf'])))
        {
            echo '<h3>Error</h3><hr />
   			This transaction has been blocked for your security.<br />
    		Please send items quickly after you open the form - do not leave it open in tabs.<br />
    		&gt; <a href="itemsend.php?ID=' . $_GET['ID'] . '">Try Again</a>';
            die($h->endpage());
        }
        else if ($_POST['qty'] > $r['inv_qty'])
        {
            echo 'You are trying to send more than you have!';
        }
        else if ($db->num_rows($m) == 0)
        {
            echo 'You are trying to send to an invalid user!';
        }
        else
        {
            $rm = $db->fetch_row($m);
            item_remove($userid, $r['inv_itemid'], $_POST['qty']);
            item_add($_POST['user'], $r['inv_itemid'], $_POST['qty']);
            echo 'You sent ' . $_POST['qty'] . ' ' . $r['itmname'] . '(s) to '
                    . $rm['username'];
            event_add($_POST['user'],
                "You received {$_POST['qty']} {$r['itmname']}(s) from <a href='viewuser.php?u=$userid'>{$ir['username']}</a>");
            $db->query(
                    "INSERT INTO `itemxferlogs`
                     VALUES(NULL, $userid, {$_POST['user']}, {$r['itmid']},
                     {$_POST['qty']}, " . time()
                            . ", '{$ir['lastip']}', '{$rm['lastip']}')");
        }
        $db->free_result($m);
    }
    $db->free_result($id);
}
else if (!empty($_GET['ID']))
{
    $id =
            $db->query(
                    "SELECT `inv_qty`, `itmname`
                     FROM `inventory` iv
                     INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid`
                     WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND `iv`.`inv_userid` = $userid
                     LIMIT 1");
    if ($db->num_rows($id) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $r = $db->fetch_row($id);
        $code = request_csrf_code("senditem_{$_GET['ID']}");
        echo "
		<b>Enter who you want to send {$r['itmname']} to and how many you want to send.
			You have {$r['inv_qty']} to send.</b>
		<br />
		<form action='itemsend.php?ID={$_GET['ID']}' method='post'>
			User ID: <input type='text' name='user' value='' />
			<br />
			Quantity: <input type='text' name='qty' value='' />
			<br />
			<input type='hidden' name='verf' value='{$code}' />
			<input type='submit' value='Send Items (no prompt so be sure!' />
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
