<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $h;
require_once('sglobals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = 'index';
}
/**
 * @return void
 */
function manually_fire_cron(): void
{
    global $db, $h;
    if (!check_access('administrator')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    $get_crons = $db->query(
        'SELECT id, name FROM cron_times ORDER BY name',
    );
    $crons = [];
    while ($row = $db->fetch_row($get_crons)) {
        $crons[] = $row['name'];
    }
    $_POST['cron'] = array_key_exists('cron', $_POST) && in_array($_POST['cron'], $crons) ? strtolower($_POST['cron']) : null;
    if (array_key_exists('submit', $_POST)) {
        if (empty($_POST['cron'])) {
            echo 'Invalid cron name given';
            $h->endpage();
            exit;
        }
        require_once __DIR__.'/crons/CronHandler.php';
        (CronHandler::getInstance($db))->run($_POST['cron'], 1);
        stafflog_add('Manually fired cron: '.$_POST['cron']);
        echo $_POST['cron'].' cron fired.';
    }
    echo '
    <h3>Manually Fire Cron</h3>
    There is no confirmation. Be certain you have selected the correct cron before submitting the form.<br>
    <form action="staff.php?action=fire-cron" method="post">
        <label for="cron">Select Cron</label>
        <select name="cron" id="cron">
            <option value="0" disabled selected>-- SELECT --</option>
            ';
            foreach ($crons as $cron) {
                echo '<option value="'.$cron.'"'.($cron === $_POST['cron'] ? ' selected' : '').'>'.$cron.'</option>';
            }
    echo '
        </select><br>
        <button type="submit" name="submit">
            Fire!
        </button>
    </form>';
}

switch ($_GET['action']) {
    case 'basicset':
        basicsettings();
        break;
    case 'announce':
        announcements();
        break;
    case 'fire-cron':
        manually_fire_cron();
        break;
    default:
        index();
        break;
}
/**
 * @param string $name
 * @param array $options
 * @param mixed $selected
 * @return string
 */
function render_menu_options(string $name, array $options, mixed $selected = null): string
{
    $ret = '<select name="' . $name . '">';
    foreach ($options as $key => $value) {
        $ret .= '<option value="' . $key . '"' . ($key == $selected ? ' selected' : '') . '>' . $value . '</option>';
    }
    $ret .= '</select>';
    return $ret;
}

/**
 * @return void
 */
function display_basic_settings_form(): void
{
    global $set;
    $csrf               = request_csrf_html('staff_basicset');
    $idempotent_options = [
        0 => 'Off',
        1 => 'On',
    ];
    $period_options     = [
        '5' => 'Every 5 Minutes',
        '15' => 'Every 15 Minutes',
        '60' => 'Every Hour',
        'login' => 'Every Login',
    ];
    echo "
        <h3>Basic Settings</h3>
        <hr />
        <form action='staff.php?action=basicset' method='post'>
            Game Name: <input type='text' name='game_name' value='{$set['game_name']}' /><br />
            Game Owner: <input type='text' name='game_owner' value='{$set['game_owner']}' /><br />
            Game Description:<br />
            <textarea rows='7' cols='50' name='game_description'>{$set['game_description']}</textarea><br />
            PayPal Address: <input type='text' name='paypal' value='{$set['paypal']}' /><br />
            Use Timestamps Instead of Cron Jobs: ".render_menu_options('use_timestamps_over_crons', $idempotent_options, $set['use_timestamps_over_crons']). '<br>
            Gym/Crimes Validation: ' . render_menu_options('validate_on', $idempotent_options, $set['validate_on']) . '<br />
            Validation Period: ' . render_menu_options('validate_period', $period_options, $set['validate_period']) . '<br />
            Registration CAPTCHA: ' . render_menu_options('regcap_on', $idempotent_options, $set['regcap_on']) . '<br />
            Send Crystals: ' . render_menu_options('sendcrys_on', $idempotent_options, $set['sendcrys_on']) . '<br />
            Bank Transfers: ' . render_menu_options('sendbank_on', $idempotent_options, $set['sendbank_on']) . "<br />
            Energy Refill Price (crystals): <input type='text' name='ct_refillprice' value='{$set['ct_refillprice']}' /><br />
            IQ per crystal: <input type='text' name='ct_iqpercrys' value='{$set['ct_iqpercrys']}' /><br />
            Money per crystal: <input type='text' name='ct_moneypercrys' value='{$set['ct_moneypercrys']}' /><br />
            Will Potion Item: " . item_dropdown('willp_item', $set['willp_item']) . "<br />
            {$csrf}
            <input type='submit' value='Update Settings' />
        </form>";
}

/**
 * @return void
 */
function process_post_data(): void
{
    global $db;
    $preg_strs = ['game_name', 'game_owner'];
    foreach ($preg_strs as $str) {
        $_POST[$str] = array_key_exists($str, $_POST) && preg_match('/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i', $_POST[$str])
            ? $db->escape(strip_tags(stripslashes($_POST[$str])))
            : '';
    }
    $nums = ['ct_refillprice', 'ct_iqpercrys', 'ct_moneypercrys', 'willp_item'];
    foreach ($nums as $num) {
        $_POST[$num] = array_key_exists($num, $_POST) && is_numeric($_POST[$num])
            ? abs(intval($_POST[$num]))
            : '';
    }
    $idempotents = ['validate_on', 'regcap_on', 'sendcrys_on', 'sendbank_on'];
    foreach ($idempotents as $idempotent) {
        $_POST[$idempotent] = array_key_exists($idempotent, $_POST) && in_array($_POST[$idempotent], [0, 1])
            ? $_POST[$idempotent]
            : false;
    }

    $_POST['game_description'] = isset($_POST['game_description'])
        ? $db->escape(strip_tags(stripslashes($_POST['game_description'])))
        : '';
    $_POST['paypal']           = isset($_POST['paypal']) && filter_input(INPUT_POST, 'paypal', FILTER_VALIDATE_EMAIL)
        ? $db->escape(stripslashes($_POST['paypal']))
        : '';
    $_POST['validate_period']  = isset($_POST['validate_period']) && in_array($_POST['validate_period'], ['5', '15', '60', 'login'])
        ? $_POST['validate_period']
        : false;
}

/**
 * @return void
 */
function update_basic_settings(): void
{
    global $db, $h;
    staff_csrf_stdverify('staff_basicset', 'staff.php?action=basicset');
    unset($_POST['verf']);
    if (!empty($_POST['willp_item'])) {
        $qi =
            $db->query(
                'SELECT `itmid`
                             FROM `items`
                             WHERE `itmid` = ' . $_POST['willp_item']);
        if ($db->num_rows($qi) == 0) {
            echo '
                The item you tried to input doesn\'t seem to be a real item.<br />
                &gt; <a href="staff.php?action=basicset">Go Back</a>
                   ';
            $h->endpage();
            exit;
        }
    } else {
        $_POST['willp_item'] = 0;
        echo 'Please remember to make a will potion item and set it<br />';
    }
    foreach ($_POST as $k => $v) {
        $db->query(
            "UPDATE `settings`
                     SET `conf_value` = '$v'
                     WHERE `conf_name` = '$k'");
    }
    echo '
        Settings updated!<br />
        &gt; <a href="staff.php?action=basicset">Go Back</a>
           ';
    stafflog_add('Updated the basic game settings');
}

/**
 * @return void
 */
function basicsettings(): void
{
    global $h;
    if (!check_access('administrator')) {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    process_post_data();
    if (empty($_POST['game_name']) || empty($_POST['game_owner'])
        || empty($_POST['game_description']) || empty($_POST['paypal'])
        || empty($_POST['ct_refillprice'])
        || empty($_POST['ct_iqpercrys'])
        || empty($_POST['ct_moneypercrys'])
        || is_bool($_POST['validate_on'])
        || is_bool($_POST['validate_period'])
        || is_bool($_POST['regcap_on']) || is_bool($_POST['sendcrys_on'])
        || is_bool($_POST['sendbank_on'])) {
        display_basic_settings_form();
    } else {
        update_basic_settings();
    }
}

/**
 * @return void
 */
function announcements(): void
{
    global $db, $h;
    if (!check_access('administrator')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    if (!empty($_POST['text'])) {
        staff_csrf_stdverify('staff_announcement', 'staff.php?action=announce');
        $_POST['text'] =
            $db->escape(
                htmlentities(stripslashes($_POST['text']), ENT_QUOTES,
                    'ISO-8859-1'));
        $db->query(
            "INSERT INTO `announcements`
                 VALUES('{$_POST['text']}', " . time() . ')');
        $db->query(
            'UPDATE `users`
                 SET `new_announcements` = `new_announcements` + 1');
        echo '
        Announcement added!<br />
        &gt; <a href="staff.php">Back</a>
           ';
        stafflog_add('Added a new announcement');
    } else {
        $csrf = request_csrf_html('staff_announcement');
        echo '
        Adding an announcement...
        <br />
        Please try to make sure the announcement is concise and covers everything you want it to.
        <form action="staff.php?action=announce" method="post">
            Announcement text:<br />
            <textarea name="text" rows="10" cols="60"></textarea>
            <br />
            ' . $csrf
            . '
            <input type="submit" value="Add Announcement" />
        </form>
           ';
    }
}

/**
 * @return void
 */
function index(): void
{
    global $db, $set, $_CONFIG;
    if (check_access('administrator')) {
        $versq = $db->query('SELECT VERSION()');
        $mv    = $db->fetch_single($versq);
        $db->free_result($versq);
        $versionno = intval('20503');
        $version   = '2.0.5b';
        echo "
        <h3>System Info</h3>
        <hr />
        <table width='75%' cellspacing='1' class='table'>
                <tr>
                    <th>PHP Version:</th>
                    <td>" . phpversion()
            . "</td>
                </tr>
                <tr>
                    <th>MySQL Version:</th>
                    <td>$mv</td>
                </tr>
                <tr>
                    <th>MySQL Driver:</th>
                    <td>" . $_CONFIG['driver']
            . "</td>
                </tr>
                <tr>
                    <th>Codes Version:</th>
                    <td>$version (Build: $versionno)</td>
                </tr>
                <tr>
                    <th>Update Status:</th>
                    <td>
                        <iframe
                            src='https://www.mccodes.com/update_check.php?version={$versionno}'
                            width='250' height='30'></iframe>
                    </td>
                </tr>
        </table>
        <hr />
        <h3>Last 20 Staff Actions</h3><hr />
        <table width='100%' cellspacing='1' class='table'>
                <tr>
                    <th>Staff</th>
                    <th>Action</th>
                    <th>Time</th>
                    <th>IP</th>
                </tr>
           ";
        $q =
            $db->query(
                'SELECT `user`, `action`, `time`, `ip`, `username`
                         FROM `stafflog` AS `s`
                         INNER JOIN `users` AS `u`
                         ON `s`.`user` = `u`.`userid`
                         ORDER BY `s`.`time` DESC
                         LIMIT 20');
        while ($r = $db->fetch_row($q)) {
            echo "
            <tr>
                <td>{$r['username']} [{$r['user']}]</td>
                <td>{$r['action']}</td>
                <td>" . date('F j Y g:i:s a', (int)$r['time'])
                . "</td>
                <td>{$r['ip']}</td>
            </tr>
               ";
        }
        $db->free_result($q);
        echo '</table><hr />';
    }
    echo '<h3>Staff Notepad</h3><hr />';
    if (isset($_POST['pad'])) {
        staff_csrf_stdverify('staff_notepad', 'staff.php');
        $pad = $db->escape(stripslashes($_POST['pad']));
        $db->query(
            "UPDATE `settings`
                 SET `conf_value` = '{$pad}'
                 WHERE `conf_name` = 'staff_pad'");
        $set['staff_pad'] = stripslashes($_POST['pad']);
        echo '<b>Staff Notepad Updated!</b><hr />';
    }
    $csrf = request_csrf_html('staff_notepad');
    echo "
    <form action='staff.php' method='post'>
        <textarea rows='10' cols='60' name='pad'>"
        . htmlentities($set['staff_pad'], ENT_QUOTES, 'ISO-8859-1')
        . "</textarea>
        <br />
        {$csrf}
        <input type='submit' value='Update Notepad' />
    </form>
       ";
}

$h->endpage();
