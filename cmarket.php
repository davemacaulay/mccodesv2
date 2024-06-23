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
echo '
	<h3>Crystal Market</h3>
   ';
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'buy':
    crystal_buy();
    break;
case 'remove':
    crystal_remove();
    break;
case 'add':
    crystal_add();
    break;
default:
    cmarket_index();
    break;
}

/**
 * @return void
 */
function cmarket_index(): void
{
    global $db, $userid;
    echo "
	<a href='cmarket.php?action=add'>&gt; Add A Listing</a><br /><br />
	Viewing all listings...

	<table width='95%' align='center' cellspacing='1' cellpadding='1' class='table'>
	<tr>
	<td width='25%'>Adder</td>
	<td width='25%'>Qty</td>
	<td width='15%'>Price Each</td>
	<td width='15%'>Price Total</td>
	<td width='10%'>Links</td>
	</tr>";

    $sql =
        'SELECT `cm`.`cmADDER`, `cm`.`cmPRICE`, `cmID`, `cmQTY`,
              `u`.`userid`, `username`, `level`, `money`, `crystals`,
              `gender`, `donatordays`
              FROM `crystalmarket` AS `cm`
              LEFT JOIN `users` AS `u` ON `u`.`userid` = `cm`.`cmADDER`
              ORDER BY (`cmPRICE`/`cmQTY`) ASC';
    $q = $db->query($sql);

    while ($r = $db->fetch_row($q))
    {

        if ($r['cmADDER'] == $userid)
        {
            $link =
                    '<a href="cmarket.php?action=remove&ID=' . $r['cmID']
                            . '">Remove</a>';
        }
        else
        {
            $link =
                    "<a href='cmarket.php?action=buy&ID={$r['cmID']}'>Buy</a>";
        }
        $each = (float) $r['cmPRICE'] * $r['cmQTY'];
        $r['money'] = number_format((int)$r['money']);

        echo "
		<br />
		<tr>
		<td>
		<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
		</td>
		<td>{$r['cmQTY']}</td>
		<td> " . money_formatter((int)$r['cmPRICE']) . '</td> <td>'
                . money_formatter((int)$each)
                . "</td> <td>[{$link}]
		</td> </tr>";
    }
    $db->free_result($q);
    echo '
	</table>
	';
}

/**
 * @return void
 */
function crystal_remove(): void
{
    global $db, $userid, $h;
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    if (empty($_GET['ID']))
    {
        echo 'Something went wrong.<br />&gt; <a href="cmarket.php" alt="Go Back" title="Go Back">Go Back</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    'SELECT `cmQTY` FROM `crystalmarket` WHERE `cmID` = '
                            . $_GET['ID'] . ' AND `cmADDER` = ' . $userid);
    if (!$db->num_rows($q))
    {
        echo "Error, either these crystals do not exist, or you are not the owner.
	<br />
	<a href='cmarket.php'>&gt; Back</a>";
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    $db->query(
            'UPDATE `users` SET `crystals` = `crystals` + ' . $r['cmQTY']
                    . ' WHERE `userid` = ' . $userid);
    $db->query('DELETE FROM `crystalmarket` WHERE `cmID` = ' . $_GET['ID']);
    echo "
	Crystals removed from market!
	<br />
	&gt; <a href='cmarket.php'> Back</a>
	";
}

/**
 * @return void
 */
function crystal_buy(): void
{
    global $db, $ir, $userid, $h;
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    if (empty($_GET['ID']))
    {
        echo 'Something went wrong.<br />&gt; <a href="cmarket.php" alt="Go Back" title="Go Back">Go Back</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    'SELECT `cmPRICE`, `cmQTY`, `cmADDER` FROM `crystalmarket` WHERE `cmID` = '
                            . $_GET['ID']);
    if (!$db->num_rows($q))
    {
        echo '
	Error, either these crystals do not exist, or they have already been bought.
	<br />
	<a href="cmarket.php">&gt; Back</a>
	';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    $_POST['QTY'] =
            (isset($_POST['QTY']) && is_numeric($_POST['QTY']))
                    ? abs(intval($_POST['QTY'])) : '';
    if ($_GET['ID'] > 0 && $_POST['QTY'])
    {
        $cprice = $r['cmPRICE'] * $_POST['QTY'];
        if ($cprice > $ir['money'])
        {
            echo '
	Error, you do not have the funds to buy these crystals.
	<br />
	<a href="cmarket.php">&gt; Back</a>
	';
            $h->endpage();
            exit;
        }
        if ($_POST['QTY'] > $r['cmQTY'])
        {
            echo '
	Error, you selected more crystals than there are available in this listing.
	<br />
	<a href="cmarket.php">&gt; Back</a>
	';
            $h->endpage();
            exit;
        }
        $db->query(
                'UPDATE `users` SET `crystals` = `crystals` + '
                        . $_POST['QTY'] . ', `money` = `money` - ' . $cprice
                        . ' WHERE `userid` = ' . $userid);
        if ($_POST['QTY'] < $r['cmQTY'])
        {
            $db->query(
                    'UPDATE `crystalmarket` SET `cmQTY` = `cmQTY` - '
                            . $_POST['QTY'] . ' WHERE `cmID` = ' . $_GET['ID']);
        }
        elseif ($_POST['QTY'] == $r['cmQTY'])
        {
            $db->query(
                    'DELETE FROM `crystalmarket` WHERE `cmID` = '
                            . $_GET['ID']);
        }
        $db->query(
                'UPDATE `users` SET `money` = `money` + ' . $cprice
                        . ' WHERE `userid` = ' . $r['cmADDER']);

        event_add($r['cmADDER'],
            "<a href='viewuser.php?u=$userid'>{$ir['username']}</a> bought of {$_POST['QTY']} your crystals from the market for "
            . money_formatter((int)$cprice) . '.');

        echo '
	You bought the ' . $_POST['QTY'] . ' crystals from the market for $'
                . number_format((int)$cprice)
                . '.
	<br />
	><a href="cmarket.php">Back</a>
	';
    }
    elseif ($_GET['ID'] > 0 AND !$_POST['QTY'])
    {

        echo "
There is <b>{$r['cmQTY']}</b> available to buy.
<br />
     ";
        echo '
 <form action="cmarket.php?action=buy&ID=' . $_GET['ID']
                . '" method="post">
 Quantity: <input type="text" name="QTY" value="" />
 <br />
 <input type="submit"  value="Buy">
 </form>
      ';

    }

}

/**
 * @return void
 */
function crystal_add(): void
{
    global $db, $ir, $userid, $h;
    $_POST['amnt'] =
            (isset($_POST['amnt']) && is_numeric($_POST['amnt']))
                    ? abs(intval($_POST['amnt'])) : '';
    $_POST['price'] =
            (isset($_POST['price']) && is_numeric($_POST['price']))
                    ? abs(intval($_POST['price'])) : '';
    if (!empty($_POST['amnt']) && !empty($_POST['price']))
    {
        if ($_POST['amnt'] > $ir['crystals'])
        {
            echo 'You are trying to add more crystals to the market than you have.';
            $h->endpage();
            exit;
        }

        $ql =
                $db->query(
                        'SELECT `cmID` FROM `crystalmarket` WHERE cmADDER = '
                                . $userid . ' AND cmPRICE = '
                                . $_POST['price']);
        if ($db->num_rows($ql))
        {
            $gc = $db->fetch_row($ql);
            $db->free_result($ql);
            $db->query(
                    'UPDATE `crystalmarket` SET `cmQTY` = `cmQTY` + '
                            . $_POST['amnt'] . ' WHERE `cmID` = '
                            . $gc['cmID']);

        }
        else
        {
            $db->free_result($ql);
            $tp = $_POST['price'];
            $db->query(
                    'INSERT INTO `crystalmarket` VALUES(NULL, '
                            . $_POST['amnt'] . ', ' . $userid . ', ' . $tp
                            . ')');
        }
        $db->query(
                'UPDATE `users` SET `crystals` = `crystals` - '
                        . $_POST['amnt'] . ' WHERE userid = ' . $userid);
        echo '
	Crystals added to market!
	<br />
	<a href="cmarket.php">&gt; Back</a>
	';
    }
    else
    {
        echo '

	<form action="cmarket.php?action=add" method="post">
	<table width="35%" align="center" style="border:0px; border-style:solid; border-color:#262626; padding-bottom: 1px;padding-top: 1px;padding-right: 1px;padding-left: 1px;" cellspacing="1">
	<tr>
	<td>Crystals:</td> <td><input type="text" name="amnt" value="'
                . $ir['crystals']
                . '" /></td>
	</tr>
	<tr>
	<td>Price Each:</td> <td><input type="text" name="price" value="2000"></td>
	</tr>
	<tr>
	<td colspan="2" align="center">
	<input type="submit" value="Add To Market">
	</tr>
	</table>
	</form>
	';
    }
}
$h->endpage();
