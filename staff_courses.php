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
 * File: staff_courses.php
 * Signature: cd12f79bc59259fafe30ee2517389b04
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('sglobals.php');
if ($ir['user_level'] != 2)
{
    echo 'You cannot access this area.<br />
    &gt; <a href="staff.php">Go Back</a>';
    die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'addcourse':
    addcourse();
    break;
case 'editcourse':
    editcourse();
    break;
case 'delcourse':
    delcourse();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

function addcourse()
{
    global $db, $h;
    $cost =
            (isset($_POST['cost']) && is_numeric($_POST['cost']))
                    ? abs(intval($_POST['cost'])) : '';
    $energy =
            (isset($_POST['energy']) && is_numeric($_POST['energy']))
                    ? abs(intval($_POST['energy'])) : '';
    $days =
            (isset($_POST['days']) && is_numeric($_POST['days']))
                    ? abs(intval($_POST['days'])) : '';
    $str =
            (isset($_POST['str']) && is_numeric($_POST['str']))
                    ? abs(intval($_POST['str'])) : '';
    $agil =
            (isset($_POST['agil']) && is_numeric($_POST['agil']))
                    ? abs(intval($_POST['agil'])) : '';
    $gua =
            (isset($_POST['gua']) && is_numeric($_POST['gua']))
                    ? abs(intval($_POST['gua'])) : '';
    $lab =
            (isset($_POST['lab']) && is_numeric($_POST['lab']))
                    ? abs(intval($_POST['lab'])) : '';
    $iq =
            (isset($_POST['iq']) && is_numeric($_POST['iq']))
                    ? abs(intval($_POST['iq'])) : '';
    $_POST['name'] =
            (isset($_POST['name'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['name']))
                    ? $db->escape(strip_tags(stripslashes($_POST['name'])))
                    : '';
    $_POST['desc'] =
            (isset($_POST['desc'])
                    && preg_match(
                            "/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i",
                            $_POST['desc']))
                    ? $db->escape(strip_tags(stripslashes($_POST['desc'])))
                    : '';
    if ($_POST['name'] && $_POST['desc'] && $cost && $days && $cost > 0 && $energy
            && $str && $agil && $gua && $lab && $iq)
    {
        staff_csrf_stdverify('staff_addcourse',
                'staff_courses.php?action=addcourse');
        $db->query(
                "INSERT INTO `courses`
                 VALUES(NULL, '{$_POST['name']}', '{$_POST['desc']}', '$cost',
                 '$energy', '$days', '$str', '$gua',  '$lab', '$agil',
                 '$iq')");
        echo 'Course ' . $_POST['name']
                . ' added.<br />&gt; <a href="staff.php">Goto Main</a>';
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_addcourse');
        echo "
        <h3>Add Course</h3><hr />
        <form action='staff_courses.php?action=addcourse' method='post'>
        	Name: <input type='text' name='name' />
        <br />
        	Description: <input type='text' name='desc' />
        <br />
        	Cost (Money): <input type='text' name='cost' />
        <br />
        	Cost (Energy): <input type='text' name='energy' />
        <br />
        	Length (Days): <input type='text' name='days' />
        <br />
        	Strength Gain: <input type='text' name='str' />
        <br />
        	Agility Gain: <input type='text' name='agil' />
        <br />
        	Guard Gain: <input type='text' name='gua' />
        <br />
        	Labour Gain: <input type='text' name='lab' />
        <br />
        	IQ Gain: <input type='text' name='iq' />
        <br />
        	{$csrf}
        	<input type='submit' value='Add Course' />
        </form>
           ";
    }
}

function editcourse()
{
    global $db, $h;
    if (!isset($_POST['step']))
    {
        $_POST['step'] = '0';
    }
    switch ($_POST['step'])
    {
    case '2':
        $cost =
                (isset($_POST['cost']) && is_numeric($_POST['cost']))
                        ? abs(intval($_POST['cost'])) : '';
        $energy =
                (isset($_POST['energy']) && is_numeric($_POST['energy']))
                        ? abs(intval($_POST['energy'])) : '';
        $days =
                (isset($_POST['days']) && is_numeric($_POST['days']))
                        ? abs(intval($_POST['days'])) : '';
        $str =
                (isset($_POST['str']) && is_numeric($_POST['str']))
                        ? abs(intval($_POST['str'])) : '';
        $agil =
                (isset($_POST['agil']) && is_numeric($_POST['agil']))
                        ? abs(intval($_POST['agil'])) : '';
        $gua =
                (isset($_POST['gua']) && is_numeric($_POST['gua']))
                        ? abs(intval($_POST['gua'])) : '';
        $lab =
                (isset($_POST['lab']) && is_numeric($_POST['lab']))
                        ? abs(intval($_POST['lab'])) : '';
        $iq =
                (isset($_POST['iq']) && is_numeric($_POST['iq']))
                        ? abs(intval($_POST['iq'])) : '';
        $_POST['name'] =
                (isset($_POST['name'])
                        && preg_match(
                                "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                                $_POST['name']))
                        ? $db->escape(strip_tags(stripslashes($_POST['name'])))
                        : '';
        $_POST['desc'] =
                (isset($_POST['desc'])
                        && preg_match(
                                "/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i",
                                $_POST['desc']))
                        ? $db->escape(strip_tags(stripslashes($_POST['desc'])))
                        : '';
        if (empty($_POST['name']) || empty($_POST['desc']) || empty($cost)
                || empty($days) || empty($energy)
                || empty($str) || empty($agil) || empty($gua) || empty($lab)
                || empty($iq))
        {
            echo 'Something went wrong.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            die($h->endpage());
        }
        staff_csrf_stdverify('staff_editcourse2',
                'staff_courses.php?action=editcourse');
        $db->query(
                "UPDATE `courses`
                 SET `crNAME` = '{$_POST['name']}',
                 `crDESC` = '{$_POST['desc']}', `crCOST` = $cost,
                 `crENERGY` = $energy, `crDAYS` = $days, `crSTR` = $str,
                 `crGUARD` = $gua, `crLABOUR` = $lab, `crAGIL` = $agil,
                 `crIQ` = $iq
                 WHERE `crID` = {$_POST['id']}");
        echo 'Course ' . $_POST['name']
                . ' was edited successfully.<br />
                &gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Edited course {$_POST['name']}");
        die($h->endpage());
    case '1':
        $_POST['course'] =
                (isset($_POST['course']) && is_numeric($_POST['course']))
                        ? abs(intval($_POST['course'])) : '';
        $q =
                $db->query(
                        "SELECT `crIQ`, `crLABOUR`, `crGUARD`, `crAGIL`,
                         `crSTR`, `crDAYS`, `crENERGY`, `crCOST`, `crDESC`,
                         `crNAME`
                         FROM `courses`
                         WHERE `crID` = {$_POST['course']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid course.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            die($h->endpage());
        }
        staff_csrf_stdverify('staff_editcourse1',
                'staff_courses.php?action=editcourse');
        $old = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html('staff_editcourse2');
        echo "
        <h3>Editing a Course</h3>
        <hr />
        <form action='staff_courses.php?action=editcourse' method='post'>
        	<input type='hidden' name='step' value='2' />
        	<input type='hidden' name='id' value='{$_POST['course']}' />
        	Name: <input type='text' name='name' value='{$old['crNAME']}' />
        <br />
        	Description: <input type='text' name='desc' value='{$old['crDESC']}' />
        <br />
        	Cost (Money): <input type='text' name='cost' value='{$old['crCOST']}' />
        <br />
        	Cost (Energy): <input type='text' name='energy' value='{$old['crENERGY']}' />
        <br />
        	Length (Days): <input type='text' name='days' value='{$old['crDAYS']}' />
        <br />
        	Strength Gain: <input type='text' name='str' value='{$old['crSTR']}' />
        <br />
        	Agility Gain: <input type='text' name='agil' value='{$old['crAGIL']}' />
        <br />
        	Guard Gain: <input type='text' name='gua' value='{$old['crGUARD']}' />
        <br />
        	Labour Gain: <input type='text' name='lab' value='{$old['crLABOUR']}' />
        <br />
        	IQ Gain: <input type='text' name='iq' value='{$old['crIQ']}' />
        <br />
        	{$csrf}
        	<input type='submit' value='Edit Course' />
        </form>
   		";
        break;
    default:
        $csrf = request_csrf_html('staff_editcourse1');
        echo "
        <h3>Editing a Course</h3>
        <hr />
        <form action='staff_courses.php?action=editcourse' method='post'>
        	<input type='hidden' name='step' value='1' />
        	Course: " . course_dropdown()
                . "
        <br />
        	{$csrf}
        	<input type='submit' value='Edit Course' />
        </form>
           ";
        break;
    }
}

function delcourse()
{
    global $db, $h;
    $_POST['course'] =
            (isset($_POST['course']) && is_numeric($_POST['course']))
                    ? abs(intval($_POST['course'])) : '';
    if ($_POST['course'])
    {
        staff_csrf_stdverify('staff_delcourse',
                'staff_courses.php?action=delcourse');
        $q =
                $db->query(
                        "SELECT `crNAME`
                         FROM `courses`
                         WHERE `crID` = {$_POST['course']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid course.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            die($h->endpage());
        }
        $old = $db->fetch_row($q);
        $db->free_result($q);
        $db->query(
                "UPDATE `users`
                 SET `course` = 0, `cdays` = 0
                 WHERE `course` = {$_POST['course']}");
        $db->query(
                "DELETE FROM `courses`
        		 WHERE `crID` = {$_POST['course']}");
        echo 'Course ' . $old['crNAME']
                . ' deleted.<br />
                &gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Deleted course {$old['crNAME']}");
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_delcourse');
        echo "
        <h3>Deleting a Course</h3>
        <hr />
        <form action='staff_courses.php?action=delcourse' method='post'>
        	Course: " . course_dropdown()
                . "<br />
            {$csrf}
        	<input type='submit' value='Delete Course' />
        </form>
           ";
    }
}
$h->endpage();
