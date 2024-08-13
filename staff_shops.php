<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $ir, $h;
require_once('sglobals.php');
if (!check_access('manage_shops')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'newshop':
    new_shop_form();
    break;
case 'newshopsub':
    new_shop_submit();
    break;
case 'newstock':
    new_stock_form();
    break;
case 'newstocksub':
    new_stock_submit();
    break;
case 'delshop':
    delshop();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function new_shop_form(): void
{
    $csrf = request_csrf_html('staff_newshop');
    echo "
    <h3>Adding a New Shop</h3>
    <form action='staff_shops.php?action=newshopsub' method='post'>
    	Shop Name: <input type='text' name='sn' value='' />
    	<br />
    	Shop Desc: <input type='text' name='sd' value='' />
    	<br />
    	Shop Location: " . location_dropdown('sl')
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='Create Shop' />
    </form>
       ";
}

/**
 * @return void
 */
function new_shop_submit(): void
{
    global $db, $h;
    staff_csrf_stdverify('staff_newshop', 'staff_shops.php?action=newshop');
    $_POST['sl'] =
            (isset($_POST['sl']) && is_numeric($_POST['sl']))
                    ? abs(intval($_POST['sl'])) : 0;
    $_POST['sn'] =
            (isset($_POST['sn'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['sn']))
                    ? $db->escape(strip_tags(stripslashes($_POST['sn']))) : '';
    $_POST['sd'] =
            (isset($_POST['sd']))
                    ? $db->escape(strip_tags(stripslashes($_POST['sd']))) : '';
    if (empty($_POST['sn']) || empty($_POST['sd']))
    {
        echo 'You missed a field, go back and try again.<br />
        &gt; <a href="staff_shops.php?action=newshop">Go Back</a>';
    }
    else
    {
        $q =
                $db->query(
                        'SELECT COUNT(`cityid`)
                         FROM `cities`
                         WHERE `cityid` = ' . $_POST['sl']);
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Location doesn\'t seem to exist.<br />
            &gt; <a href="staff_shops.php?action=newshop">Go Back</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q);
        $db->query(
                "INSERT INTO `shops`
                VALUES(NULL, {$_POST['sl']}, '{$_POST['sn']}', '{$_POST['sd']}')");
        stafflog_add('Added Shop ' . $_POST['sn']);
        echo 'The ' . $_POST['sn']
                . ' Shop was successfully added to the game.<br />
                &gt; <a href="staff.php">Go Home</a>';
        $h->endpage();
        exit;
    }
}

/**
 * @return void
 */
function new_stock_form(): void
{
    $csrf = request_csrf_html('staff_newstock');
    echo "
    <h3>Adding an item to a shop</h3>
    <form action='staff_shops.php?action=newstocksub' method='post'>
    	Shop: " . shop_dropdown() . '
    	<br />
    	Item: ' . item_dropdown()
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='Add Item To Shop' />
    </form>
       ";
}

/**
 * @return void
 */
function new_stock_submit(): void
{
    global $db, $h;
    staff_csrf_stdverify('staff_newstock', 'staff_shops.php?action=newstock');
    $_POST['shop'] =
            (isset($_POST['shop']) && is_numeric($_POST['shop']))
                    ? abs(intval($_POST['shop'])) : '';
    $_POST['item'] =
            (isset($_POST['item']) && is_numeric($_POST['item']))
                    ? abs(intval($_POST['item'])) : '';
    if (empty($_POST['shop']) || empty($_POST['item']))
    {
        echo 'Invalid shop/item.<br />
        &gt; <a href="staff_shops.php?action=newstock">Go Back</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    'SELECT COUNT(`shopID`)
                     FROM `shops`
                     WHERE `shopID` = ' . $_POST['shop']);
    $q2 =
            $db->query(
                    'SELECT COUNT(`itmid`)
                     FROM `items`
                     WHERE `itmid` = ' . $_POST['item']);
    if ($db->fetch_single($q) == 0 || $db->fetch_single($q2) == 0)
    {
        $db->free_result($q);
        $db->free_result($q2);
        echo 'Invalid shop/item.<br />
        &gt; <a href="staff_shops.php?action=newstock">Go Back</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($q);
    $db->free_result($q2);
    $db->query(
            "INSERT INTO `shopitems`
             VALUES(NULL, {$_POST['shop']}, {$_POST['item']})");
    stafflog_add(
            'Added Item ID ' . $_POST['item'] . ' to shop ID '
                    . $_POST['shop']);
    echo 'Item ID ' . $_POST['item'] . ' was successfully added to shop ID '
            . $_POST['shop']
            . '<br />
            &gt; <a href="staff.php">Go Home</a>';
    $h->endpage();
    exit;
}

/**
 * @return void
 */
function delshop(): void
{
    global $db, $h;
    $_POST['shop'] =
            (isset($_POST['shop']) && is_numeric($_POST['shop']))
                    ? abs(intval($_POST['shop'])) : '';
    if (!empty($_POST['shop']))
    {
        staff_csrf_stdverify('staff_delshop', 'staff_shops.php?action=delshop');
        $shpq =
                $db->query(
                        "SELECT `shopNAME`
        				 FROM `shops`
        				 WHERE `shopID` = {$_POST['shop']}");
        if ($db->num_rows($shpq) == 0)
        {
            $db->free_result($shpq);
            echo "Invalid shop.<br />
            &gt; <a href='staff_shops.php?action=delshop'>Go back</a>";
            $h->endpage();
            exit;
        }
        $sn = $db->fetch_single($shpq);
        $db->free_result($shpq);
        $db->query(
                "DELETE FROM `shops`
         		 WHERE `shopID` = {$_POST['shop']}");
        $db->query(
                "DELETE FROM `shopitems`
                 WHERE `sitemSHOP` = {$_POST['shop']}");
        stafflog_add('Deleted Shop ' . $sn);
        echo 'Shop ' . $sn
                . ' Deleted.<br />
                &gt; <a href="staff.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_delshop');
        echo "
        <h3>Delete Shop</h3>
        <hr />
        Deleting a shop will remove it from the game permanently. Be sure.
        <form action='staff_shops.php?action=delshop' method='post'>
        	Shop: " . shop_dropdown()
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Delete Shop' />
        </form>
           ";
    }
}
$h->endpage();
