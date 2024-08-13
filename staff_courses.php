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
if (!check_access('manage_courses')) {
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
function process_course_post_data(): void
{
    global $db;
    $_POST['cost'] =
        (isset($_POST['cost']) && is_numeric($_POST['cost']))
            ? abs(intval($_POST['cost'])) : '';
    $_POST['energy'] =
        (isset($_POST['energy']) && is_numeric($_POST['energy']))
            ? abs(intval($_POST['energy'])) : '';
    $_POST['days'] =
        (isset($_POST['days']) && is_numeric($_POST['days']))
            ? abs(intval($_POST['days'])) : '';
    $_POST['str'] =
        (isset($_POST['str']) && is_numeric($_POST['str']))
            ? abs(intval($_POST['str'])) : '';
    $_POST['agil'] =
        (isset($_POST['agil']) && is_numeric($_POST['agil']))
            ? abs(intval($_POST['agil'])) : '';
    $_POST['gua'] =
        (isset($_POST['gua']) && is_numeric($_POST['gua']))
            ? abs(intval($_POST['gua'])) : '';
    $_POST['lab'] =
        (isset($_POST['lab']) && is_numeric($_POST['lab']))
            ? abs(intval($_POST['lab'])) : '';
    $_POST['iq'] =
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
}

/**
 * @return void
 */
function addcourse(): void
{
    global $db, $h;
    process_course_post_data();
    if ($_POST['name'] && $_POST['desc'] && $_POST['cost'] && $_POST['days'] && $_POST['cost'] > 0 && $_POST['energy'] > 0
            && $_POST['str'] > -1 && $_POST['agil'] > -1 && $_POST['gua'] > -1 && $_POST['lab'] > -1 && $_POST['iq'] > -1)
    {
        staff_csrf_stdverify('staff_addcourse',
                'staff_courses.php?action=addcourse');
        $db->query(
                "INSERT INTO `courses`
                 VALUES(NULL, '{$_POST['name']}', '{$_POST['desc']}', '{$_POST['cost']}',
                 '{$_POST['energy']}', '{$_POST['days']}', '{$_POST['str']}', '{$_POST['gua']}',  '{$_POST['lab']}', '{$_POST['agil']}',
                 '{$_POST['iq']}')");
        echo 'Course ' . $_POST['name'] . ' added.<br />&gt; <a href="staff.php">Goto Main</a>';
        $h->endpage();
        exit;
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

/**
 * @return void
 */
function editcourse(): void
{
    global $db, $h;
    if (!isset($_POST['step']))
    {
        $_POST['step'] = '0';
    }
    switch ($_POST['step'])
    {
    case '2':
        process_course_post_data();
        if (empty($_POST['name']) || empty($_POST['desc']) || empty($_POST['cost'])
                || empty($_POST['days']) || $_POST['energy'] < 0
                || $_POST['str'] < 0 || $_POST['agil'] < 0 || $_POST['gua'] < 0 || $_POST['lab'] < 0
                || $_POST['iq'] < 0)
        {
            echo 'Something went wrong.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        staff_csrf_stdverify('staff_editcourse2',
                'staff_courses.php?action=editcourse');
        $db->query(
                "UPDATE `courses`
                 SET `crNAME` = '{$_POST['name']}',
                 `crDESC` = '{$_POST['desc']}', `crCOST` = {$_POST['cost']},
                 `crENERGY` = {$_POST['energy']}, `crDAYS` = {$_POST['days']}, `crSTR` = {$_POST['str']},
                 `crGUARD` = {$_POST['gua']}, `crLABOUR` = {$_POST['lab']}, `crAGIL` = {$_POST['agil']},
                 `crIQ` = {$_POST['iq']}
                 WHERE `crID` = {$_POST['id']}");
        echo 'Course ' . $_POST['name']
                . ' was edited successfully.<br />
                &gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Edited course {$_POST['name']}");
        $h->endpage();
        exit;
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
            $h->endpage();
            exit;
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

/**
 * @return void
 */
function delcourse(): void
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
            $h->endpage();
            exit;
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
        $h->endpage();
        exit;
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
