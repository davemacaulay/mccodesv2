<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $h;
require_once('globals.php');
echo '<h3>Item Market</h3>';
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}

/**
 * @param $goBackTo
 * @return void
 */
function csrf_error($goBackTo): void
{
    global $h;
    echo '<h3>Error</h3><hr />
    Your transaction has been blocked for your security.<br />
    Please try again.<br />
    &gt; <a href="itemmarket.php?action=' . $goBackTo . '">Try Again</a>';
    $h->endpage();
    exit;
}
switch ($_GET['action'])
{
case 'buy':
    item_buy();
    break;
case 'gift1':
    item_gift1();
    break;
case 'gift2':
    item_gift2();
    break;
case 'remove':
    itemm_remove();
    break;
default:
    imarket_index();
    break;
}

/**
 * @return void
 */
function imarket_index(): void
{
    global $db, $userid;
    echo '
	<br />
	<table width="100%" cellspacing="1" cellpadding="1" class="table" align="center">
		<tr>
			<th width="25%">Adder</th>
			<th width="25%">Item</th>
			<th width="20%">Price Each</th>
			<th width="20%">Price Total</th>
			<th width="10%">Links</th>
		</tr>
   ';

    $q =
            $db->query(
                'SELECT `imPRICE`, `imQTY`, `imCURRENCY`, `imADDER`,
                     `imID`, `itmid`, `itmname`, `userid`,`username`,
                     `itmtypename`
                     FROM `itemmarket` AS `im`
                     INNER JOIN `items` AS `i`
                     ON `im`.`imITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `im`.`imADDER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC');
    $lt = '';
    while ($r = $db->fetch_row($q))
    {
        if ($lt != $r['itmtypename'])
        {
            $lt = $r['itmtypename'];
            echo '
		<tr>
	<th colspan="5" align="center">' . $lt . '</th>
		</tr>
   ';
        }
        $ctprice = (int)($r['imPRICE'] * $r['imQTY']);
        if ($r['imCURRENCY'] == 'money')
        {
            $price = money_formatter((int)$r['imPRICE']);
            $tprice = money_formatter($ctprice);
        }
        else
        {
            $price = number_format((int)$r['imPRICE']) . ' crystal(s)';
            $tprice = number_format($ctprice) . ' crystal(s)';
        }
        if ($r['imADDER'] == $userid)
        {
            $link =
                    "[<a href='itemmarket.php?action=remove&amp;ID={$r['imID']}'>Remove</a>]";
        }
        else
        {
            $link =
                    "[<a href='itemmarket.php?action=buy&amp;ID={$r['imID']}'>Buy</a>]
                    [<a href='itemmarket.php?action=gift1&amp;ID={$r['imID']}'>Gift</a>]";
        }
        echo '
		<tr>
	<td><a href="viewuser.php?u=' . $r['userid'] . '">' . $r['username']
                . '</a> [' . $r['userid']
                . ']</td>
	<td><a href="iteminfo.php?ID=' . $r['itmid'] . '">' . $r['itmname']
                . '</a>
   ';
        if ($r['imQTY'] > 1)
        {
            echo '&nbsp;x' . $r['imQTY'];
        }
        echo '
	</td>
	<td>' . $price . '</td>
	<td>' . $tprice . '</td>
	<td>' . $link . '
   ';
        echo '
	</td>
	</tr>
   ';
    }
    $db->free_result($q);
    echo '
	</table>
   ';
}

/**
 * @return void
 */
function itemm_remove(): void
{
    global $db, $ir, $userid, $h;
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    if (empty($_GET['ID']))
    {
        echo 'Something went wrong.
        <br />&gt; <a href="itemmarket.php" title="Go Back">Go Back</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    "SELECT `imITEM`, `imQTY`, `imADDER`, `imID`, `itmname`
                    FROM `itemmarket` AS `im`
                    INNER JOIN `items` AS `i`
                    ON `im`.`imITEM` = `i`.`itmid`
                    WHERE `im`.`imID` = {$_GET['ID']}
                    AND `im`.`imADDER` = $userid");
    if ($db->num_rows($q) == 0)
    {
        echo "Error, either this item does not exist, or you are not the owner.
			<br />
			&gt; <a href='itemmarket.php'>Back</a>
			";
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    item_add($userid, $r['imITEM'], $r['imQTY']);
    $i = ($db->insert_id()) ? $db->insert_id() : 99999;
    $db->query("DELETE FROM `itemmarket`
    			WHERE `imID` = {$_GET['ID']}");
    $imr_log =
            $db->escape(
                    "{$ir['username']} removed {$r['itmname']} x {$r['imQTY']}"
                            . ' from the item market.');
    $db->query(
            "INSERT INTO `imremovelogs`
             VALUES (NULL, {$r['imITEM']}, {$r['imADDER']}, $userid,
             {$r['imID']}, $i, " . time() . ", '{$imr_log}')");
    echo '
	Item removed from market!
<br />
	<a href="itemmarket.php">&gt; Back</a>
   ';
}

/**
 * @return void
 */
function item_buy(): void
{
    global $db, $ir, $userid, $h;
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    $_POST['QTY'] =
            (isset($_POST['QTY']) && is_numeric($_POST['QTY']))
                    ? abs(intval($_POST['QTY'])) : '';
    if ($_GET['ID'] && !$_POST['QTY'])
    {
        $q =
                $db->query(
                        "SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname`
                         FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i`
                         ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo "Error, this item does not exist.
			<br />
			&gt; <a href='itemmarket.php'>Back</a>
			";
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $imbuy_csrf = request_csrf_code("imbuy_{$_GET['ID']}");
        echo '
		Enter how many <b>' . $r['itmname']
                . '</b> you want to buy.
		<br />
		There is <b>' . $r['imQTY']
                . '</b> available.
		<br />
		<form action="itemmarket.php?action=buy&ID=' . $_GET['ID']
                . '" method="post">
        <input type="hidden" name="verf" value="' . $imbuy_csrf
                . '" />
		Quantity: <input type="text" name="QTY" value="">
		<br />
		<input type="submit" value="Buy">
		</form>
   		';
    }
    elseif (!$_GET['ID'])
    {
        echo 'Invalid use of file.';
    }
    else
    {
        $q =
                $db->query(
                        "SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname`
                         FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i`
                         ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if (!$db->num_rows($q))
        {
            $db->free_result($q);
            echo '
			Error, either this item does not exist, or it has already been bought.
			<br />
			&gt; <a href="itemmarket.php">Back</a>
   			';
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if (!isset($_POST['verf'])
                || !verify_csrf_code("imbuy_{$_GET['ID']}",
                        stripslashes($_POST['verf'])))
        {
            csrf_error('buy&amp;ID=' . $_GET['ID']);
        }
        if ($r['imADDER'] == $userid)
        {
            echo '
			Error, you cannot buy your own items.<br />
			&gt; <a href="itemmarket.php">Back</a>
			';
            $h->endpage();
            exit;
        }
        $curr = $r['imCURRENCY'];
        $final_price = (int)($r['imPRICE'] * $_POST['QTY']);
        if ($final_price > $ir[$curr])
        {
            echo '
			Error, you do not have the funds to buy this item.
			<br />
			&gt; <a href="itemmarket.php">Back</a>
   			';
            $h->endpage();
            exit;
        }
        if ($_POST['QTY'] > $r['imQTY'])
        {
            echo '
			Error, you cannot buy more than <b>' . $r['imQTY'] . ' '
                    . $r['itmname']
                    . '(s)</b>
			<br />
			&gt; <a href="itemmarket.php?action=buy&ID=' . $_GET['ID']
                    . '">Back</a>
			';
            $h->endpage();
            exit;
        }
        item_add($userid, $r['imITEM'], $_POST['QTY']);
        $i = ($db->insert_id()) ? $db->insert_id() : 99999;
        if ($_POST['QTY'] == $r['imQTY'])
        {
            $db->query(
                    "DELETE FROM `itemmarket`
            			WHERE `imID` = {$_GET['ID']}");
        }
        elseif ($_POST['QTY'] < $r['imQTY'])
        {
            $db->query(
                    'UPDATE `itemmarket`
                     SET `imQTY` = `imQTY` - ' . $_POST['QTY']
                            . '
                     WHERE `imID` = ' . $_GET['ID']);
        }

        $db->query(
                "UPDATE `users`
                SET `$curr` = `$curr` - {$final_price}
                WHERE `userid` = $userid");
        $db->query(
                "UPDATE `users`
                SET `$curr` = `$curr` + {$final_price}
                WHERE `userid` = {$r['imADDER']}");
        if ($curr == 'money')
        {
            event_add($r['imADDER'],
                "<a href='viewuser.php?u=$userid'>{$ir['username']}</a>"
                . " bought your {$r['itmname']} item "
                . ' from the market for '
                . money_formatter($final_price) . '.');
            $imb_log =
                    $db->escape(
                            "{$ir['username']} bought {$r['itmname']} x{$r['imQTY']}"
                                    . ' from the item market for '
                                    . money_formatter($final_price)
                                    . " from user ID {$r['imADDER']}");
            $db->query(
                    "INSERT INTO `imbuylogs`
                     VALUES (NULL, {$r['imITEM']}, {$r['imADDER']}, $userid,
                     {$final_price}, {$r['imID']}, {$i}, " . time()
                            . ", '{$imb_log}')");
            echo "
			You bought the {$r['itmname']} x{$_POST['QTY']} from the market for "
                    . money_formatter($final_price) . '.';
        }
        else
        {
            event_add($r['imADDER'],
                "<a href='viewuser.php?u=$userid'>{$ir['username']}</a>"
                . " bought your {$r['itmname']} item "
                . ' from the market for '
                . number_format($final_price) . ' crystals.');
            $imb_log =
                    $db->escape(
                            "{$ir['username']} bought {$r['itmname']} x{$r['imQTY']}"
                                    . ' from the item market for '
                                    . number_format($final_price)
                                    . " crystals from user ID {$r['imADDER']}");
            $db->query(
                    "INSERT INTO `imbuylogs`
                     VALUES (NULL, {$r['imITEM']}, {$r['imADDER']}, $userid,
                     {$final_price}, {$r['imID']}, {$i}, " . time()
                            . ", '{$imb_log}')");
            echo "
			You bought the {$r['itmname']} x{$_POST['QTY']} from the market for "
                    . number_format($final_price) . ' crystals.';
        }
    }

}

/**
 * @return void
 */
function item_gift1(): void
{
    global $db, $ir, $h;
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    if (empty($_GET['ID']))
    {
        echo 'Something went wrong.
        <br />&gt; <a href="itemmarket.php" title="Go Back">Go Back</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    "SELECT `imCURRENCY`, `imPRICE`, `imQTY`, `itmname`
                     FROM `itemmarket` AS `im`
                     INNER JOIN `items` AS `i`
                     ON `i`.`itmid` = `im`.`imITEM`
                     WHERE `im`.`imID` = {$_GET['ID']}");
    if ($db->num_rows($q) == 0)
    {
        echo "
		Error, either this item does not exist, or it has already been bought.
		<br />
		&gt; <a href='itemmarket.php'>Back</a>
		";
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    $curr = $r['imCURRENCY'];
    if ($r['imPRICE'] > $ir[$curr])
    {
        echo '
		Error, you do not have the funds to buy this item.
		<br />
		&gt; <a href="itemmarket.php">Back</a>
   		';
        $h->endpage();
        exit;
    }
    if ($curr == 'money')
    {
        echo "
		Buying the <b>{$r['itmname']}</b> for "
                . money_formatter((int)$r['imPRICE']) . ' each as a gift.';
    }
    else
    {
        echo "
		Buying the <b>{$r['itmname']}</b> for " . number_format((int)$r['imPRICE'])
                . ' crystals each as a gift.';
    }
    $imgift_csrf = request_csrf_code("imgift_{$_GET['ID']}");
    echo "
	<br />
	There is <b>{$r['imQTY']}</b> available.
	<br />
	<form action='itemmarket.php?action=gift2' method='post'>
	<input type='hidden' name='verf' value='{$imgift_csrf}' />
	<input type='hidden' name='ID' value='{$_GET['ID']}' />
	User to give gift to: " . user_dropdown()
            . "
	<br />
	Quantity: <input type='text' name='QTY' value=''>
	<br />
	<input type='submit' value='Buy Item and Send Gift' />
	</form>
	";
}

/**
 * @return void
 */
function item_gift2(): void
{
    global $db, $ir, $userid, $h;
    $_POST['QTY'] =
            (isset($_POST['QTY']) && is_numeric($_POST['QTY']))
                    ? abs(intval($_POST['QTY'])) : '';
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : '';
    if ((empty($_POST['ID']) || empty($_POST['user']) || empty($_POST['QTY'])))
    {
        echo 'Something went wrong.
        <br />&gt; <a href="itemmarket.php" title="Go Back">Go Back</a>';
        $h->endpage();
        exit;
    }
    if (!isset($_POST['verf'])
            || !verify_csrf_code("imgift_{$_POST['ID']}",
                    stripslashes($_POST['verf'])))
    {
        csrf_error('gift1&amp;ID=' . $_GET['ID']);
    }
    $query_user_exist =
            $db->query(
                    "SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = {$_POST['user']}");
    if ($db->fetch_single($query_user_exist) == 0)
    {
        echo '
  		User doesn\'t exist.
  		<br />
 		&gt; <a href="itemmarket.php">Back</a>
   		';
        $h->endpage();
        exit;
    }
    $db->free_result($query_user_exist);
    $q =
            $db->query(
                    "SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                    `imITEM`, `imID`, `itmname`
                    FROM `itemmarket` AS `im`
                    INNER JOIN `items` AS `i`
                    ON `i`.`itmid` = `im`.`imITEM`
                    WHERE `im`.`imID` = {$_POST['ID']}");
    if ($db->num_rows($q) == 0)
    {
        echo '
		Error, either this item does not exist, or it has already been bought.
		<br />
		&gt; <a href="itemmarket.php">Back</a>
   		';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['imADDER'] == $userid)
    {
        echo '
		Error, you cannot buy your own items.<br />
		&gt; <a href="itemmarket.php">Back</a>
		';
        $h->endpage();
        exit;
    }
    $curr = $r['imCURRENCY'];
    $final_price = $r['imPRICE'] * $_POST['QTY'];
    if ($final_price > $ir[$curr])
    {
        echo "
		Error, you do not have the funds to buy this item.
		<br />
		&gt; <a href='itemmarket.php'>Back</a>
		";
        $h->endpage();
        exit;
    }
    if ($_POST['QTY'] > $r['imQTY'])
    {
        echo '
		Error, you cannot buy more than <b>' . $r['imQTY'] . ' '
                . $r['itmname']
                . '(s)</b>
		<br />
		&gt; <a href="itemmarket.php?action=buy&ID=' . $_POST['ID']
                . '">Back</a>
		';
        $h->endpage();
        exit;
    }
    send_gift($r, $curr, $final_price);
}

/**
 * @param array $data
 * @param string $currency
 * @param int $final_price
 * @return void
 */
function send_gift(array $data, string $currency, int $final_price): void
{
    global $db, $ir, $userid;
    item_add($_POST['user'], $data['imITEM'], $_POST['QTY']);
    $i = ($db->insert_id()) ? $db->insert_id() : 99999;
    if ($_POST['QTY'] == $data['imQTY']) {
        $db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_POST['ID']}");
    } elseif ($_POST['QTY'] < $data['imQTY']) {
        $db->query('UPDATE `itemmarket` SET `imQTY` = `imQTY` - ' . $_POST['QTY'] . ' WHERE `imID` = ' . $_POST['ID']);
    }
    $db->query("UPDATE `users` SET `$currency` = `$currency` - {$final_price} WHERE `userid`= $userid");
    $db->query("UPDATE `users` SET `$currency` = `$currency` + {$final_price} WHERE `userid` = {$data['imADDER']}");
    $my_name = '<a href="viewuser.php?u='.$userid.'">'.$ir['username'].'</a>';
    $cost = $currency === 'money' ? money_formatter($final_price) : number_format($final_price);
    event_add($data['imADDER'], $my_name." bought your {$data['itmname']} x{$_POST['QTY']} item(s) from the market for " . $cost . '.');
    event_add($_POST['user'], $my_name . " bought you {$data['itmname']} x{$_POST['QTY']} from the item market as a gift.");
    $u = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']}");
    $uname = $db->fetch_single($u);
    $db->free_result($u);
    $img_log = $db->escape("{$ir['username']} bought {$data['itmname']} x{$data['imQTY']} from the item market for " . $cost . " from user ID {$data['imADDER']} as a gift for $uname [{$_POST['user']}]");
    $db->query("INSERT INTO `imbuylogs` VALUES (NULL, {$data['imITEM']}, {$data['imADDER']}, $userid, {$final_price}, {$data['imID']}, $i, " . time() . ", '{$img_log}')");
    echo "You bought the {$data['itmname']} from the market for " . $cost . " and sent the gift to $uname.";
}
$h->endpage();
