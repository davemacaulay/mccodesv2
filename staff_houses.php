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
 * File: staff_houses.php
 * Signature: 46784db6d36b5c8bf73148676bdd2b04
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('sglobals.php');
if ($ir['user_level'] != 2)
{
    echo 'You cannot access this area.<br />
    &gt; <a href="staff.php">Go Back</a>';
    die($h->endpage());
}
//This contains house stuffs
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "addhouse":
    addhouse();
    break;
case "edithouse":
    edithouse();
    break;
case "delhouse":
    delhouse();
    break;
default:
    echo "Error: This script requires an action.";
    break;
}

function addhouse()
{
    global $db, $h;
    $price =
            (isset($_POST['price']) && is_numeric($_POST['price']))
                    ? abs(intval($_POST['price'])) : '';
    $will =
            (isset($_POST['will']) && is_numeric($_POST['will']))
                    ? abs(intval($_POST['will'])) : '';
    $name =
            (isset($_POST['name'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['name']))
                    ? $db->escape(strip_tags(stripslashes($_POST['name'])))
                    : '';
    if ($price && $will && $name)
    {
        staff_csrf_stdverify('staff_addhouse',
                'staff_houses.php?action=addhouse');
        $q =
                $db->query(
                        "SELECT COUNT(`hID`)
                         FROM `houses`
                         WHERE `hWILL` = {$will}");
        if ($db->fetch_single($q) > 0)
        {
            $db->free_result($q);
            echo 'Sorry, you cannot have two houses with the same maximum will.<br />
            &gt; <a href="staff_houses.php?action=addhouse">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "INSERT INTO `houses`
                 VALUES(NULL, '$name', '$price', '$will')");
        stafflog_add('Created House ' . $name);
        echo 'House ' . $name
                . ' added to the game.<br />
                &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_addhouse');
        echo "
        <h3>Add House</h3>
        <hr />
        <form action='staff_houses.php?action=addhouse' method='post'>
        	Name: <input type='text' name='name' /><br />
        	Price: <input type='text' name='price' /><br />
        	Max Will: <input type='text' name='will' /><br />
        	{$csrf}
        	<input type='submit' value='Add House' />
        </form>
           ";
    }
}

function edithouse()
{
    global $db, $h;
    if (!isset($_POST['step']))
    {
        $_POST['step'] = '0';
    }
    switch ($_POST['step'])
    {
    case "2":
        $price =
                (isset($_POST['price']) && is_numeric($_POST['price']))
                        ? abs(intval($_POST['price'])) : 0;
        $will =
                (isset($_POST['will']) && is_numeric($_POST['will']))
                        ? abs(intval($_POST['will'])) : 0;
        $_POST['id'] =
                (isset($_POST['id']) && is_numeric($_POST['id']))
                        ? abs(intval($_POST['id'])) : 0;
        if (!$price || !$will || !$_POST['id'])
        {
            echo 'Sorry, invalid input.
            <br />&gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
            die($h->endpage());
        }
        staff_csrf_stdverify('staff_edithouse2',
                'staff_houses.php?action=edithouse');
        $q =
                $db->query(
                        "SELECT `hID`
                         FROM `houses`
                         WHERE `hWILL` = {$will} AND `hID` != {$_POST['id']}");
        if ($db->num_rows($q))
        {
            echo 'Sorry, you cannot have two houses with the same maximum will.
            <br />&gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
            die($h->endpage());
        }
        $q =
                $db->query(
                        'SELECT `hWILL`
                         FROM `houses`
                         WHERE `hID` = ' . $_POST['ID']);
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid house.<br />
            &gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
            die($h->endpage());
        }
        $oldwill = $db->fetch_single($q);
        $name =
                (isset($_POST['name'])
                        && preg_match(
                                "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                                $_POST['name']))
                        ? $db->escape(strip_tags(stripslashes($_POST['name'])))
                        : '';
        if ($oldwill == 100 && $oldwill != $will)
        {
            echo 'Sorry, this house\'s will bar cannot be edited.<br />
            &gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
            die($h->endpage());
        }
        $db->query(
                "UPDATE `houses`
                 SET `hWILL` = $will, `hPRICE` = $price, `hNAME` = '$name'
                 WHERE `hID` = {$_POST['id']}");
        $db->query(
                "UPDATE `users`
                 SET `maxwill` = $will, `will` = LEAST(`will`, $will)
                 WHERE `maxwill` = {$old['hWILL']}");
        stafflog_add('Edited house ' . $name);
        echo 'House ' . $name
                . ' was edited successfully.<br />
                &gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
        die($h->endpage());
        break;
    case "1":
        $_POST['house'] =
                (isset($_POST['house']) && is_numeric($_POST['house']))
                        ? abs(intval($_POST['house'])) : 0;
        staff_csrf_stdverify('staff_edithouse1',
                'staff_houses.php?action=edithouse');
        $q =
                $db->query(
                        "SELECT `hWILL`, `hPRICE`, `hNAME`
                         FROM `houses`
                         WHERE `hID` = {$_POST['house']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid house.<br />
            &gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
            die($h->endpage());
        }
        $old = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html('staff_edithouse2');
        echo "
        <h3>Editing a House</h3>
        <hr />
        <form action='staff_houses.php?action=edithouse' method='post'>
        	<input type='hidden' name='step' value='2' />
        	<input type='hidden' name='id' value='{$_POST['house']}' />
        	Name: <input type='text' name='name' value='{$old['hNAME']}' />
        	<br />
        	Price: <input type='text' name='price' value='{$old['hPRICE']}' />
        	<br />
        	Max Will: <input type='text' name='will' value='{$old['hWILL']}' />
        	<br />
        	{$csrf}
        	<input type='submit' value='Edit House' />
        </form>
           ";
        break;
    default:
        $csrf = request_csrf_html('staff_edithouse1');
        echo "
        <h3>Editing a House</h3>
        <hr />
        <form action='staff_houses.php?action=edithouse' method='post'>
        	<input type='hidden' name='step' value='1' />
        	House: " . house_dropdown(NULL, "house")
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Edit House' />
        </form>
           ";
        break;
    }
}

function delhouse()
{
    global $db, $h;
    $_POST['house'] =
            (isset($_POST['house']) && is_numeric($_POST['house']))
                    ? abs(intval($_POST['house'])) : '';
    if ($_POST['house'])
    {
        staff_csrf_stdverify('staff_delhouse',
                'staff_houses.php?action=delhouse');
        $q =
                $db->query(
                        "SELECT `hWILL`, `hPRICE`, `hID`, `hNAME`
                         FROM `houses`
                         WHERE `hID` = {$_POST['house']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid house.<br />
            &gt; <a href="staff_houses.php?action=edithouse">Go Back</a>';
            die($h->endpage());
        }
        $old = $db->fetch_row($q);
        $db->free_result($q);
        if ($old['hWILL'] == 100)
        {
            echo 'This house cannot be deleted.<br />
            &gt; <a href="staff_houses.php?action=delhouse">Go Back</a>';
            die($h->endpage());
        }
        $db->query(
                "UPDATE `users`
                 SET `money` = `money` + {$old['hPRICE']},
                 `maxwill` = 100, `will` = LEAST(100, `will`)
                 WHERE `maxwill` = {$old['hWILL']}");
        $db->query(
                "DELETE FROM `houses`
         		 WHERE `hID` = {$old['hID']}");
        stafflog_add('Deleted house ' . $old['hNAME']);
        echo 'House ' . $old['hNAME']
                . ' deleted.<br />
                &gt; <a href="staff_houses.php?action=delhouse">Go Back</a>';
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_delhouse');
        echo "
        <h3>Delete House</h3><hr />
        Deleting a house is permanent - be sure.
        Any users that are currently living in the house you delete
        will be returned to the first house,
        and their money will be refunded.
        <form action='staff_houses.php?action=delhouse' method='post'>
        	House: " . house_dropdown(NULL, "house")
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Delete House' />
        </form>
           ";
    }
}
$h->endpage();
