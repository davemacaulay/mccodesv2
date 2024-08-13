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
if (!check_access('manage_cities')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'addcity':
        addcity();
        break;
    case 'editcity':
        editcity();
        break;
    case 'delcity':
        delcity();
        break;
    default:
        echo 'Error: This script requires an action.';
        break;
}

/**
 * @return void
 */
function addcity(): void
{
    global $db, $h;
    $minlevel =
        (isset($_POST['minlevel']) && is_numeric($_POST['minlevel']))
            ? abs(intval($_POST['minlevel'])) : '';
    $name     =
        (isset($_POST['name'])
            && preg_match(
                "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['name']))
            ? $db->escape(strip_tags(stripslashes($_POST['name'])))
            : '';
    $desc     =
        (isset($_POST['desc'])
            && preg_match(
                "/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i",
                $_POST['desc']))
            ? $db->escape(strip_tags(stripslashes($_POST['desc'])))
            : '';
    if ($minlevel && $desc && $name) {
        staff_csrf_stdverify('staff_addcity',
            'staff_cities.php?action=addcity');
        $q =
            $db->query(
                "SELECT COUNT(`cityid`)
                         FROM `cities`
                         WHERE `cityname` = '{$name}'");
        if ($db->fetch_single($q) > 0) {
            $db->free_result($q);
            echo 'Sorry, you cannot have two cities with the same name.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q);
        $db->query(
            "INSERT INTO `cities`
                 VALUES(NULL, '$name', '$desc', '$minlevel')");
        echo 'City ' . $name
            . ' added to the game.<br />&gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Created City $name");
    } else {
        $csrf = request_csrf_html('staff_addcity');
        echo "
        <h3>Add City</h3>
        <hr />
        <form action='staff_cities.php?action=addcity' method='post'>
        	Name: <input type='text' name='name' />
        <br />
        	Description: <input type='text' name='desc' />
        <br />
        	Minimum Level: <input type='text' name='minlevel' />
        <br />
        	{$csrf}
        	<input type='submit' value='Add City' />
        </form>
           ";
    }
}

/**
 * @return void
 */
function edit_city_do(): void
{
    global $db, $h;
    $minlevel    =
        (isset($_POST['minlevel']) && is_numeric($_POST['minlevel']))
            ? abs(intval($_POST['minlevel'])) : '';
    $name        =
        (isset($_POST['name'])
            && preg_match(
                "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['name']))
            ? $db->escape(strip_tags(stripslashes($_POST['name'])))
            : '';
    $desc        =
        (isset($_POST['desc'])
            && preg_match(
                "/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i",
                $_POST['desc']))
            ? $db->escape(strip_tags(stripslashes($_POST['desc'])))
            : '';
    $_POST['id'] =
        (isset($_POST['id']) && is_numeric($_POST['id']))
            ? abs(intval($_POST['id'])) : '';
    if (empty($minlevel) || empty($name) || empty($desc)
        || empty($_POST['id'])) {
        echo 'Something went wrong.<br />
            &gt; <a href="staff.php">Goto Main</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_editcity2',
        'staff_cities.php?action=editcity');
    $q =
        $db->query(
            "SELECT COUNT(`cityid`)
                         FROM `cities`
                         WHERE `cityname` = '{$name}'
                         AND `cityid` != {$_POST['id']}");
    if ($db->fetch_single($q) > 0) {
        $db->free_result($q);
        echo 'Sorry, you cannot have two cities with the same name.<br />&gt; <a href="staff.php">Goto Main</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($q);
    $db->query(
        "UPDATE `cities`
                 SET `cityminlevel` = $minlevel, `citydesc` = '$desc',
                 `cityname` = '$name'
                 WHERE `cityid` = {$_POST['id']}");
    echo 'City ' . $name
        . ' was edited successfully.<br />
                &gt; <a href="staff.php">Goto Main</a>';
    stafflog_add("Edited city $name");
}

/**
 * @return void
 */
function edit_city_form(): void
{
    global $db, $h;
    $_POST['city'] =
        (isset($_POST['city']) && is_numeric($_POST['city']))
            ? abs(intval($_POST['city'])) : '';
    if (empty($_POST['city'])) {
        echo 'Something went wrong.<br />
            &gt; <a href="staff.php">Goto Main</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_editcity1',
        'staff_cities.php?action=editcity');
    $q =
        $db->query(
            "SELECT `cityminlevel`, `citydesc`, `cityname`
                         FROM `cities`
                         WHERE `cityid` = {$_POST['city']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        echo 'City doesn\'t exist.<br />
            &gt; <a href="staff.php">Goto Main</a>';
        $h->endpage();
        exit;
    }
    $old = $db->fetch_row($q);
    $db->free_result($q);
    $csrf = request_csrf_html('staff_editcity2');
    echo "
        <h3>Editing a City</h3>
        <hr />
        <form action='staff_cities.php?action=editcity' method='post'>
        	<input type='hidden' name='step' value='2' />
        	<input type='hidden' name='id' value='{$_POST['city']}' />
        	Name: <input type='text' name='name' value='{$old['cityname']}' /><br />
        	Description: <input type='text' name='desc' value='{$old['citydesc']}' /><br />
        	Minimum Level: <input type='text' name='minlevel' value='{$old['cityminlevel']}' /><br />
        	{$csrf}
        	<input type='submit' value='Edit City' />
        </form>
           ";
}

/**
 * @return void
 */
function edit_city_select(): void
{
    $csrf = request_csrf_html('staff_editcity1');
    echo "
        <h3>Editing a City</h3>
        <hr />
        <form action='staff_cities.php?action=editcity' method='post'>
        	<input type='hidden' name='step' value='1' />
        	City: " . location_dropdown('city')
        . "
        <br />
        	{$csrf}
        	<input type='submit' value='Edit City' />
        </form>
           ";
}

/**
 * @return void
 */
function editcity(): void
{
    if (!isset($_POST['step'])) {
        $_POST['step'] = '0';
    }
    switch ($_POST['step']) {
        case '2':
            edit_city_do();
            break;
        case '1':
            edit_city_form();
            break;
        default:
            edit_city_select();
            break;
    }
}

/**
 * @return void
 */
function delcity(): void
{
    global $db, $h;
    $_POST['city'] =
        (isset($_POST['city']) && is_numeric($_POST['city']))
            ? abs(intval($_POST['city'])) : '';
    if ($_POST['city']) {
        $q =
            $db->query(
                "SELECT `cityid`, `cityname`
                         FROM `cities`
                         WHERE `cityid` = {$_POST['city']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            echo 'City doesn\'t exist.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        staff_csrf_stdverify('staff_delcity',
            'staff_cities.php?action=delcity');
        $old = $db->fetch_row($q);
        $db->free_result($q);
        if ($old['cityid'] == 1) {
            echo 'This city cannot be deleted.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $db->query(
            "UPDATE `users`
                 SET `location` = 1
                 WHERE `location` = {$old['cityid']}");
        $db->query(
            "UPDATE `shops`
                 SET `shopLOCATION` = 1
                 WHERE `shopLOCATION` = {$old['cityid']}");
        $db->query(
            "DELETE FROM `cities`
        		 WHERE `cityid` = {$old['cityid']}");
        echo 'City ' . $old['cityname']
            . ' deleted.<br />&gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Deleted city {$old['cityname']}");
    } else {
        $csrf = request_csrf_html('staff_delcity');
        echo "
        <h3>Delete City</h3>
        <hr />
        Deleting a city is permanent - be sure. Any users and shops that are currently in the city you delete will be moved to the default city (ID 1).
        <form action='staff_cities.php?action=delcity' method='post'>
        	City: " . location_dropdown('city')
            . "
        <br />
        	{$csrf}
        	<input type='submit' value='Delete City' />
        </form>
           ";
    }
}

$h->endpage();
