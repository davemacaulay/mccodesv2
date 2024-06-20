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
 * File: staff_items.php
 * Signature: a40400b7147fbbca65f46da8f7d6b2b5
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('sglobals.php');
//This contains item stuffs
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'newitem':
    new_item_form();
    break;
case 'newitemsub':
    new_item_submit();
    break;
case 'giveitem':
    give_item_form();
    break;
case 'giveitemsub':
    give_item_submit();
    break;
case 'killitem':
    kill_item_form();
    break;
case 'killitemsub':
    kill_item_submit();
    break;
case 'edititem':
    edit_item_begin();
    break;
case 'edititemform':
    edit_item_form();
    break;
case 'edititemsub':
    edit_item_sub();
    break;
case 'newitemtype':
    newitemtype();
    break;
default:
    echo "Error: This script requires an action.";
    break;
}

function new_item_form()
{
    global $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    $csrf = request_csrf_html('staff_newitem');
    echo "
    <h3>Adding an item to the game</h3>
    <form action='staff_items.php?action=newitemsub' method='post'>
    	Item Name: <input type='text' name='itmname' value='' />
    <br />
    	Item Desc.: <input type='text' name='itmdesc' value='' />
    <br />
    	Item Type: " . itemtype_dropdown('itmtype')
            . "
    <br />
    	Item Buyable: <input type='checkbox' name='itmbuyable' checked='checked' />
    <br />
    	Item Price: <input type='text' name='itmbuyprice' />
    <br />
    	Item Sell Value: <input type='text' name='itmsellprice' />
    <br />
    <br />
    <hr />
	<b>Usage Form</b>";
    for ($i = 1; $i <= 3; $i++)
    {
        echo "<hr />
		<b><u>Effect {$i}</u></b>
		<br />
		On?
			<input type='radio' name='effect{$i}on' value='1' /> Yes
			<input type='radio' name='effect{$i}on' value='0' checked='checked' /> No
		<br />
    	Stat: <select name='effect{$i}stat' type='dropdown'>
    		<option value='energy'>Energy</option>
    		<option value='will'>Will</option>
    		<option value='brave'>Brave</option>
    		<option value='hp'>Health</option>
    		<option value='strength'>Strength</option>
    		<option value='agility'>Agility</option>
    		<option value='guard'>Guard</option>
    		<option value='labour'>Labour</option>
    		<option value='IQ'>IQ</option>
    		<option value='hospital'>Hospital Time</option>
    		<option value='jail'>Jail Time</option>
    		<option value='money'>Money</option>
    		<option value='crystals'>Crystals</option>
    		<option value='cdays'>Education Days Left</option>
    		<option value='bankmoney'>Bank money</option>
    		<option value='cybermoney'>Cyber money</option>
    		<option value='crimexp'>Crime XP</option>
    	</select>
    	Direction: <select name='effect{$i}dir' type='dropdown'>
    		<option value='pos'>Increase</option>
    		<option value='neg'>Decrease</option>
    	</select>
   		<br />
    	Amount: <input type='text' name='effect{$i}amount' value='0' />
    	<select name='effect{$i}type' type='dropdown'>
    		<option value='figure'>Value</option>
    		<option value='percent'>Percent</option>
    	</select>";
    }
    echo "
        <hr />
        <b>Combat Usage</b>
        <br />
        Weapon Power: <input type='text' name='weapon' value='0' />
        <br />
        Armor Defense: <input type='text' name='armor' value='0' />
        <hr />
        {$csrf}
        <input type='submit' value='Add Item To Game' />
	</form>
  	";
}

function new_item_submit()
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    staff_csrf_stdverify('staff_newitem', 'staff_items.php?action=newitem');
    $itmname =
            (isset($_POST['itmname'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['itmname']))
                    ? $db->escape(strip_tags(stripslashes($_POST['itmname'])))
                    : '';
    $itmdesc =
            (isset($_POST['itmdesc']))
                    ? $db->escape(strip_tags(stripslashes($_POST['itmdesc'])))
                    : '';
    $weapon =
            (isset($_POST['weapon']) && is_numeric($_POST['weapon']))
                    ? abs(intval($_POST['weapon'])) : 0;
    $armor =
            (isset($_POST['armor']) && is_numeric($_POST['armor']))
                    ? abs(intval($_POST['armor'])) : 0;
    $_POST['itmtype'] =
            (isset($_POST['itmtype']) && is_numeric($_POST['itmtype']))
                    ? abs(intval($_POST['itmtype'])) : '';
    $_POST['itmbuyprice'] =
            (isset($_POST['itmbuyprice']) && is_numeric($_POST['itmbuyprice']))
                    ? abs(intval($_POST['itmbuyprice'])) : '';
    $_POST['itmsellprice'] =
            (isset($_POST['itmsellprice'])
                    && is_numeric($_POST['itmsellprice']))
                    ? abs(intval($_POST['itmsellprice'])) : '';
    if (empty($itmname) || empty($itmdesc) || empty($_POST['itmtype'])
            || empty($_POST['itmbuyprice']) || empty($_POST['itmsellprice']))
    {
        echo 'You missed one or more of the fields. Please go back and try again.<br />
        &gt; <a href="staff_items.php?action=newitem">Go Back</a>';
        die($h->endpage());
    }
    $itmbuy = ($_POST['itmbuyable'] == 'on') ? 1 : 0;
    $effects = array();
    for ($i = 1; $i <= 3; $i++)
    {
        $efxkey = "effect{$i}";
        $_POST[$efxkey . 'stat'] =
                (isset($_POST[$efxkey . 'stat'])
                        && in_array($_POST[$efxkey . 'stat'],
                                array('energy', 'will', 'brave', 'hp',
                                        'strength', 'agility', 'guard',
                                        'labour', 'IQ', 'hospital', 'jail',
                                        'money', 'crystals', 'cdays',
                                        'bankmoney', 'cybermoney', 'crimexp')))
                        ? $_POST[$efxkey . 'stat'] : 'energy';
        $_POST[$efxkey . 'dir'] =
                (isset($_POST[$efxkey . 'dir'])
                        && in_array($_POST[$efxkey . 'dir'],
                                array('pos', 'neg'))) ? $_POST[$efxkey . 'dir']
                        : 'pos';
        $_POST[$efxkey . 'type'] =
                (isset($_POST[$efxkey . 'type'])
                        && in_array($_POST[$efxkey . 'type'],
                                array('figure', 'percent')))
                        ? $_POST[$efxkey . 'type'] : 'figure';
        $_POST[$efxkey . 'amount'] =
                (isset($_POST[$efxkey . 'amount'])
                        && is_numeric($_POST[$efxkey . 'amount']))
                        ? abs(intval($_POST[$efxkey . 'amount'])) : 0;
        $_POST[$efxkey . 'on'] =
                (isset($_POST[$efxkey . 'on'])
                        && in_array($_POST[$efxkey . 'on'], array('1', '0')))
                        ? $_POST[$efxkey . 'on'] : 0;
        $effects[$i] =
                $db->escape(
                        serialize(
                                array("stat" => $_POST[$efxkey . 'stat'],
                                        "dir" => $_POST[$efxkey . 'dir'],
                                        "inc_type" => $_POST[$efxkey . 'type'],
                                        "inc_amount" => abs(
                                                (int) $_POST[$efxkey
                                                        . 'amount']))));
    }
    $db->query(
                    "INSERT INTO `items`
                     VALUES(NULL, {$_POST['itmtype']}, '$itmname', '$itmdesc',
                     {$_POST['itmbuyprice']}, {$_POST['itmsellprice']},
                     $itmbuy, '{$_POST['effect1on']}', '{$effects[1]}',
                     '{$_POST['effect2on']}', '{$effects[2]}',
                     '{$_POST['effect3on']}', '{$effects[3]}', $weapon,
                     $armor)");
    stafflog_add("Created item {$_POST['itmname']}");
    echo 'The ' . $_POST['itmname']
            . ' Item was added to the game.<br />
            &gt; <a href="staff_items.php?action=newitem">Go Home</a>';
    die($h->endpage());
}

function give_item_form()
{
    global $ir, $h;
    if (!in_array($ir['user_level'], array(2, 3)))
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    $csrf = request_csrf_html('staff_giveitem');
    echo "
    <h3>Giving Item To User</h3>
    <form action='staff_items.php?action=giveitemsub' method='post'>
    	User: " . user_dropdown() . "
    	<br />
    	Item: " . item_dropdown()
            . "
    	<br />
    	Quantity: <input type='text' name='qty' value='1' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Give Item' />
    </form>
       ";
}

function give_item_submit()
{
    global $db, $ir, $h;
    if (!in_array($ir['user_level'], array(2, 3)))
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    staff_csrf_stdverify('staff_giveitem', 'staff_items.php?action=giveitem');
    $_POST['item'] =
            (isset($_POST['item']) && is_numeric($_POST['item']))
                    ? abs(intval($_POST['item'])) : '';
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['qty'] =
            (isset($_POST['qty']) && is_numeric($_POST['qty']))
                    ? abs(intval($_POST['qty'])) : '';
    if (empty($_POST['item']) || empty($_POST['user']) || empty($_POST['qty']))
    {
        echo 'Something was inputted incorrectly, please try again.<br />
        &gt; <a href="staff_items.php?action=giveitem">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT COUNT(`itmid`)
                     FROM `items`
                     WHERE `itmid` = ' . $_POST['item']);
    $q2 =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->fetch_single($q) == 0 OR $db->fetch_single($q2) == 0)
    {
        $db->free_result($q);
        $db->free_result($q2);
        echo 'Item/User doesn\'t seem to exist.<br />
        &gt; <a href="staff_items.php?action=giveitem">Go Back</a>';
        die($h->endpage());
    }
    $db->free_result($q);
    $db->free_result($q2);
    item_add($_POST['user'], $_POST['item'], $_POST['qty']);
    stafflog_add(
            "Gave {$_POST['qty']} of item ID {$_POST['item']} to user ID {$_POST['user']}");
    echo 'You gave ' . $_POST['qty'] . ' of item ID ' . $_POST['item']
            . ' to user ID ' . $_POST['user']
            . '<br />
            &gt; <a href="staff.php">Go Back</a>';
    die($h->endpage());
}

function kill_item_form()
{
    global $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    $csrf = request_csrf_html('staff_killitem');
    echo "
    <h3>Deleting Item</h3>
    The item will be permanently removed from the game.
    <br />
    <form action='staff_items.php?action=killitemsub' method='post'>
    	Item: " . item_dropdown()
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='Kill Item' />
    </form>
       ";
}

function kill_item_submit()
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    staff_csrf_stdverify('staff_killitem', 'staff_items.php?action=killitem');
    $_POST['item'] =
            (isset($_POST['item']) && is_numeric($_POST['item']))
                    ? abs(intval($_POST['item'])) : '';
    if (empty($_POST['item']))
    {
        echo 'Invalid Item.<br />
        &gt; <a href="staff_items.php?action=killitem">Go Back</a>';
        die($h->endpage());
    }
    $d =
            $db->query(
                    "SELECT `itmname`
                     FROM `items`
                     WHERE `itmid` = {$_POST['item']}");
    if ($db->num_rows($d) == 0)
    {
        $db->free_result($d);
        echo 'Item doesn\'t seem to exist.<br />
        &gt; <a href="staff_items.php?action=killitem">Go Back</a>';
        die($h->endpage());
    }
    $itemname = $db->fetch_single($d);
    $db->free_result($d);
    $db->query("DELETE FROM `items`
     WHERE `itmid` = {$_POST['item']}");
    $db->query(
            "DELETE FROM `shopitems`
             WHERE `sitemITEMID` = {$_POST['item']}");
    $db->query(
            "DELETE FROM `inventory`
     		 WHERE `inv_itemid` = {$_POST['item']}");
    $db->query(
            "DELETE FROM `itemmarket`
     		 WHERE `imITEM` = {$_POST['item']}");
    stafflog_add("Deleted item {$itemname}");
    echo 'The ' . $itemname
            . ' Item was removed from the game.<br />
            &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function edit_item_begin()
{
    global $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />&gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    $csrf = request_csrf_html('staff_edititem1');
    echo "
    <h3>Editing Item</h3>
    You can edit any aspect of this item.<br />
    <form action='staff_items.php?action=edititemform' method='post'>
    	Item: " . item_dropdown()
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='Edit Item' />
    </form>
       ";
}

function edit_item_form()
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    staff_csrf_stdverify('staff_edititem1', 'staff_items.php?action=edititem');
    $_POST['item'] =
            (isset($_POST['item']) && is_numeric($_POST['item']))
                    ? abs(intval($_POST['item'])) : '';
    if (empty($_POST['item']))
    {
        echo 'Invalid Item.<br />
        &gt; <a href="staff_items.php?action=killitem">Go Back</a>';
        die($h->endpage());
    }
    $d =
            $db->query(
                    "SELECT *
                     FROM `items`
                     WHERE `itmid` = {$_POST['item']}");
    if ($db->num_rows($d) == 0)
    {
        $db->free_result($d);
        echo 'Item doesn\'t seem to exist.<br />
        &gt; <a href="staff_items.php?action=edititem">Go Back</a>';
        die($h->endpage());
    }
    $itemi = $db->fetch_row($d);
    $db->free_result($d);
    $csrf = request_csrf_html('staff_edititem2');
    $itmname = addslashes($itemi['itmname']);
    $itmdesc = addslashes($itemi['itmdesc']);
    echo "
    <h3>Editing Item</h3>
    <form action='staff_items.php?action=edititemsub' method='post'>
    	<input type='hidden' name='itmid' value='{$_POST['item']}' />
    	Item Name: <input type='text' name='itmname' value='{$itmname}' />
    	<br />
    	Item Desc.: <input type='text' name='itmdesc' value='{$itmdesc}' />
    	<br />
    	Item Type: " . itemtype_dropdown('itmtype', $itemi['itmtype'])
            . "
    	<br />
    	Item Buyable: <input type='checkbox' name='itmbuyable'
       " . (($itemi['itmbuyable']) ? "checked='checked'" : '')
            . "
    	 />
    	<br />
    	Item Price: <input type='text' name='itmbuyprice' value='{$itemi['itmbuyprice']}' />
    	<br />
    	Item Sell Value: <input type='text' name='itmsellprice' value='{$itemi['itmsellprice']}' />
    	<hr />
    	<b>Usage Form</b>
    	<hr />
       ";
    $stats =
            array("energy" => "Energy", "will" => "Will", "brave" => "Brave",
                    "hp" => "Health", "strength" => "Strength",
                    "agility" => "Agility", "guard" => "Guard",
                    "labour" => "Labour", "IQ" => "IQ",
                    "hospital" => "Hospital Time", "jail" => "Jail Time",
                    "money" => "Money", "crystals" => "Crystals",
                    "cdays" => "Education Days Left",
                    "bankmoney" => "Bank money",
                    "cybermoney" => "Cyber money", "crimexp" => "Crime XP");
    for ($i = 1; $i <= 3; $i++)
    {
        if (!empty($itemi["effect" . $i]))
        {
            $efx = unserialize($itemi["effect" . $i]);
        }
        else
        {
            $efx = array("inc_amount" => 0);
        }
        $switch1 =
                ($itemi['effect' . $i . '_on'] > 0) ? " checked='checked'" : "";
        $switch2 =
                ($itemi['effect' . $i . '_on'] > 0) ? "" : " checked='checked'";
        echo "
        <b><u>Effect {$i}</u></b>
        <br />
        On?
        		<input type='radio' name='effect{$i}on' value='1'$switch1 /> Yes
        		<input type='radio' name='effect{$i}on' value='0'$switch2 /> No
        <br />
        Stat: <select name='effect{$i}stat' type='dropdown'>
        ";
        foreach ($stats as $k => $v)
        {
            echo ($k == $efx['stat'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                            . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
        }
        $str =
                ($efx['dir'] == "neg")
                        ? '<option value="pos">Increase</option>
                        	<option value="neg" selected="selected">Decrease</option>'
                        : '<option value="pos" selected="selected">Increase</option>
                        	<option value="neg">Decrease</option>';
        $str2 =
                ($efx['inc_type'] == "percent")
                        ? '<option value="figure">Value</option>
                        	<option value="percent" selected="selected">Percent</option>'
                        : '<option value="figure" selected="selected">Value</option>
                        	<option value="percent">Percent</option>';

        echo "
        </select>
        	Direction: <select name='effect{$i}dir' type='dropdown'> {$str} </select>
        <br />
        	Amount: <input type='text' name='effect{$i}amount' value='{$efx['inc_amount']}' />
        		<select name='effect{$i}type' type='dropdown'>{$str2}</select>
        <hr />
           ";
    }
    echo "
    <b>Combat Usage</b>
    <br />
    	Weapon Power: <input type='text' name='weapon' value='{$itemi['weapon']}' />
    <br />
    	Armor Defense: <input type='text' name='armor' value='{$itemi['armor']}' />
    <hr />
    	{$csrf}
    	<input type='submit' value='Edit Item' />
    </form>
       ";
}

function edit_item_sub()
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />&gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    staff_csrf_stdverify('staff_edititem2', 'staff_items.php?action=edititem');
    $itmname =
            (isset($_POST['itmname'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['itmname']))
                    ? $db->escape(strip_tags(stripslashes($_POST['itmname'])))
                    : '';
    $itmdesc =
            (isset($_POST['itmdesc']))
                    ? $db->escape(strip_tags(stripslashes($_POST['itmdesc'])))
                    : '';
    $weapon =
            (isset($_POST['weapon']) && is_numeric($_POST['weapon']))
                    ? abs(intval($_POST['weapon'])) : 0;
    $armor =
            (isset($_POST['armor']) && is_numeric($_POST['armor']))
                    ? abs(intval($_POST['armor'])) : 0;
    $_POST['itmtype'] =
            (isset($_POST['itmtype']) && is_numeric($_POST['itmtype']))
                    ? abs(intval($_POST['itmtype'])) : '';
    $_POST['itmbuyprice'] =
            (isset($_POST['itmbuyprice']) && is_numeric($_POST['itmbuyprice']))
                    ? abs(intval($_POST['itmbuyprice'])) : '';
    $_POST['itmsellprice'] =
            (isset($_POST['itmsellprice'])
                    && is_numeric($_POST['itmsellprice']))
                    ? abs(intval($_POST['itmsellprice'])) : '';
    $_POST['itmid'] =
            (isset($_POST['itmid']) && is_numeric($_POST['itmid']))
                    ? abs(intval($_POST['itmid'])) : '';
    if (empty($itmname) || empty($itmdesc) || empty($_POST['itmtype'])
            || empty($_POST['itmbuyprice']) || empty($_POST['itmsellprice'])
            || empty($_POST['itmid']))
    {
        echo 'You missed one or more of the fields. Please go back and try again.<br />
        &gt; <a href="staff_items.php?action=edititem">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT COUNT(`itmid`)
                     FROM `items`
                     WHERE `itmid` = ' . $_POST['itmid']);
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid item.<br />
        &gt; <a href="staff_items.php?action=edititem">Go Back</a>';
        die($h->endpage());
    }
    $db->free_result($q);
    $itmbuy = ($_POST['itmbuyable'] == 'on') ? 1 : 0;
    $effects = array();
    for ($i = 1; $i <= 3; $i++)
    {
        $efxkey = "effect{$i}";
        $_POST[$efxkey . 'stat'] =
                (isset($_POST[$efxkey . 'stat'])
                        && in_array($_POST[$efxkey . 'stat'],
                                array('energy', 'will', 'brave', 'hp',
                                        'strength', 'agility', 'guard',
                                        'labour', 'IQ', 'hospital', 'jail',
                                        'money', 'crystals', 'cdays',
                                        'bankmoney', 'cybermoney', 'crimexp')))
                        ? $_POST[$efxkey . 'stat'] : 'energy';
        $_POST[$efxkey . 'dir'] =
                (isset($_POST[$efxkey . 'dir'])
                        && in_array($_POST[$efxkey . 'dir'],
                                array('pos', 'neg'))) ? $_POST[$efxkey . 'dir']
                        : 'pos';
        $_POST[$efxkey . 'type'] =
                (isset($_POST[$efxkey . 'type'])
                        && in_array($_POST[$efxkey . 'type'],
                                array('figure', 'percent')))
                        ? $_POST[$efxkey . 'type'] : 'figure';
        $_POST[$efxkey . 'amount'] =
                (isset($_POST[$efxkey . 'amount'])
                        && is_numeric($_POST[$efxkey . 'amount']))
                        ? abs(intval($_POST[$efxkey . 'amount'])) : 0;
        $_POST[$efxkey . 'on'] =
                (isset($_POST[$efxkey . 'on'])
                        && in_array($_POST[$efxkey . 'on'], array('1', '0')))
                        ? $_POST[$efxkey . 'on'] : 0;
        $effects[$i] =
                $db->escape(
                        serialize(
                                array("stat" => $_POST[$efxkey . 'stat'],
                                        "dir" => $_POST[$efxkey . 'dir'],
                                        "inc_type" => $_POST[$efxkey . 'type'],
                                        "inc_amount" => abs(
                                                (int) $_POST[$efxkey
                                                        . 'amount']))));
    }
    $db->query(
            'UPDATE `items` SET `itmtype` = ' . $_POST['itmtype']
                    . ',`itmname` = "' . $itmname . '",`itmdesc` = "'
                    . $itmdesc . '",`itmbuyprice` = ' . $_POST['itmbuyprice']
                    . ',`itmsellprice` = ' . $_POST['itmsellprice']
                    . ',`itmbuyable` = ' . $itmbuy . ',`effect1_on` = "'
                    . $_POST['effect1on'] . '",`effect1` = "' . $effects[1]
                    . '",`effect2_on` = "' . $_POST['effect2on']
                    . '",`effect2` = "' . $effects[2] . '",`effect3_on` = "'
                    . $_POST['effect3on'] . '",`effect3` = "' . $effects[3]
                    . '",`weapon` = ' . $weapon . ',`armor` = ' . $armor
                    . ' WHERE `itmid` = ' . $_POST['itmid']);
    stafflog_add("Edited item {$_POST['itmname']}");
    echo 'The ' . $_POST['itmname']
            . ' Item was edited successfully.<br />
            &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function newitemtype()
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    $_POST['name'] =
            (isset($_POST['name'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['name']))
                    ? $db->escape(strip_tags(stripslashes($_POST['name'])))
                    : '';
    if (!empty($_POST['name']))
    {
        staff_csrf_stdverify('staff_newitemtype',
                'staff_items.php?action=newitemtype');
        $db->query(
                "INSERT INTO `itemtypes`
         		 VALUES(NULL, '{$_POST['name']}')");
        stafflog_add('Added item type ' . $_POST['name']);
        echo 'Item Type ' . $_POST['name']
                . ' added.<br />
                &gt; <a href="staff.php">Go Home</a>';
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_newitemtype');
        echo "
        <h3>Add Item Type</h3>
        <hr />
        <form action='staff_items.php?action=newitemtype' method='post'>
        	Name: <input type='text' name='name' />
        	<br />
        	{$csrf}
        	<input type='submit' value='Add Item Type' />
        </form>
           ";
    }
}
$h->endpage();
