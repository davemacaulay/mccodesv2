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
$_POST['price'] =
        (isset($_POST['price']) && is_numeric($_POST['price']))
                ? abs(intval($_POST['price'])) : '';
$_POST['QTY'] =
        (isset($_POST['QTY']) && is_numeric($_POST['QTY']))
                ? abs(intval($_POST['QTY'])) : '';
$_POST['currency'] =
        (isset($_POST['currency'])
                && in_array($_POST['currency'], ['money', 'crystals']))
                ? $_POST['currency'] : 'money';
if ($_POST['price'] && $_POST['QTY'] && $_GET['ID'])
{
    if (!isset($_POST['verf'])
            || !verify_csrf_code("imadd_{$_GET['ID']}",
                    stripslashes($_POST['verf'])))
    {
        echo "Your request to add this item to the market has expired.
        Please try again.<br />
		&gt; <a href='imadd.php?ID={$_GET['ID']}'>Back</a>";
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    "SELECT `inv_qty`, `inv_itemid`, `inv_id`, `itmname`
                     FROM `inventory` AS `iv`
                     INNER JOIN `items` AS `i`
                     ON `iv`.`inv_itemid` = `i`.`itmid`
                     WHERE `inv_id` = {$_GET['ID']}
                     AND `inv_userid` = $userid");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid Item ID';
    }
    else
    {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if ($r['inv_qty'] < $_POST['QTY'])
        {
            echo 'You do not have enough of this item.';
            $h->endpage();
            exit;
        }
        $checkq =
                sprintf(
                        'SELECT `imID`
                         FROM `itemmarket`
                         WHERE `imITEM` = %u AND `imPRICE` = %u
                         AND `imADDER` = %u AND `imCURRENCY` = "%s"',
                        $r['inv_itemid'], $_POST['price'], $userid,
                        $_POST['currency']);
        $checkq = $db->query($checkq);
        if ($db->num_rows($checkq) > 0)
        {
            $cqty = $db->fetch_row($checkq);
            $query =
                    sprintf(
                            'UPDATE `itemmarket`
                             SET imQTY = imQTY + %u
                             WHERE imID = %u', $_POST['QTY'], $cqty['imID']);
            $db->query($query);
        }
        else
        {
            $db->query(
                    "INSERT INTO `itemmarket`
                     VALUES (NULL, '{$r['inv_itemid']}', {$userid},
                     {$_POST['price']}, '{$_POST['currency']}',
                     {$_POST['QTY']})");
        }
        $db->free_result($checkq);
        item_remove($userid, $r['inv_itemid'], $_POST['QTY']);
        $imadd_log =
                $db->escape(
                        "{$ir['username']} added {$r['itmname']} "
                                . "x{$_POST['QTY']} to the item market for "
                                . "{$_POST['price']} {$_POST['currency']}");
        $db->query(
                "INSERT INTO `imarketaddlogs`
                VALUES (NULL, {$r['inv_itemid']}, {$_POST['price']},
                {$r['inv_id']}, $userid, " . time() . ", '{$imadd_log}')");
        echo 'Item added to market.';
    }
}
else
{
    $q =
            $db->query(
                    "SELECT COUNT(`inv_id`)
                     FROM `inventory`
                     WHERE `inv_id` = {$_GET['ID']}
                     AND `inv_userid` = $userid");
    if ($db->fetch_single($q) == 0)
    {
        echo 'Invalid Item ID';
    }
    else
    {
        $imadd_csrf = request_csrf_code("imadd_{$_GET['ID']}");
        echo "
Adding an item to the item market...<br />
	<form action='imadd.php?ID={$_GET['ID']}' method='post'>
	<input type='hidden' name='verf' value='{$imadd_csrf}' />
		Quantity: <input type='text' name='QTY' value=''><br />
		Price: <input type='text' name='price' value='0' /><br />
	<select name='currency' type='dropdown'>
		<option value='money'>Money</option>
		<option value='crystals'>Crystals</option>
	</select><br />
		<input type='submit' value='Add' />
	</form>
   ";
    }
    $db->free_result($q);
}
$h->endpage();
