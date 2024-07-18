<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $h;
require_once('sglobals.php');
if (!check_access('view_logs')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
//This contains log stuffs
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'atklogs':
    view_attack_logs();
    break;
case 'itmlogs':
    view_itm_logs();
    break;
case 'cashlogs':
    view_cash_logs();
    break;
case 'cryslogs':
    view_crys_logs();
    break;
case 'banklogs':
    view_bank_logs();
    break;
case 'maillogs':
    view_mail_logs();
    break;
case 'stafflogs':
    view_staff_logs();
    break;
    case 'cron-fails':
        view_cron_fail_logs($db);
        break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function view_attack_logs(): void
{
    global $db;
    echo '
	<h3>Attack Logs</h3>
	<hr />
 	  ';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query('SELECT COUNT(`attacker`)
    				 FROM `attacklogs`');
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no attacks yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=atklogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <br />
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>Time</th>
    			<th>Who Attacked</th>
    			<th>Who Was Attacked</th>
    			<th>Who Won</th>
    			<th>What Happened</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `stole`, `result`, `attacked`, `attacker`, `time`,
                     `u1`.`username` AS `un_attacker`,
                     `u2`.`username` AS `un_attacked`
                     FROM `attacklogs` AS `a`
                     INNER JOIN `users` AS `u1`
                     ON `a`.`attacker` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `a`.`attacked` = `u2`.`userid`
                     ORDER BY `a`.`time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
        echo '
		<tr>
        	<td>' . date('F j, Y, g:i:s a', (int)$r['time'])
                . "</td>
        	<td>{$r['un_attacker']} [{$r['attacker']}]</td>
        	<td>{$r['un_attacked']} [{$r['attacked']}]</td>
           ";
        if ($r['result'] == 'won')
        {
            echo "
			<td>{$r['un_attacker']}</td>
			<td>
   			";
            if ($r['stole'] == -1)
            {
                echo "{$r['un_attacker']} hospitalized {$r['un_attacked']}";
            }
            elseif ($r['stole'] == -2)
            {
                echo "{$r['un_attacker']} attacked {$r['un_attacked']} and left them";
            }
            else
            {
                echo "{$r['un_attacker']} mugged "
                        . money_formatter((int)$r['stole'])
                        . " from {$r['un_attacked']}";
            }
            echo '</td>';
        }
        else
        {
            echo "
			<td>{$r['un_attacked']}</td>
			<td>Nothing</td>
   			";
        }
        echo '</tr>';
    }
    $db->free_result($q);
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=atklogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    $mypage = floor($_GET['st'] / 100) + 1;
    stafflog_add("Looked at the attack logs (Page $mypage)");
}

/**
 * @return void
 */
function view_itm_logs(): void
{
    global $db;
    echo '<h3>Item Xfer Logs</h3><hr />';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query('SELECT COUNT(`ixFROM`)
    				 FROM `itemxferlogs`');
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no item transfers yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=atklogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <br />
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>Time</th>
    			<th>Who Sent</th>
    			<th>Who Received</th>
    			<th>Sender's IP</th>
    			<th>Receiver's IP</th>
    			<th>Same IP?</th>
    			<th>Item</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `ixTO`, `ixFROM`, `ixQTY`, `ixTIME`, `ixTOIP`,
                     `ixFROMIP`, `u1`.`username` AS `sender`,
                     `u2`.`username` AS `sent`, `i`.`itmname` AS `item`
                     FROM `itemxferlogs` AS `ix`
                     INNER JOIN `users` AS `u1`
                     ON `ix`.`ixFROM` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `ix`.`ixTO` = `u2`.`userid`
                     INNER JOIN `items` AS `i`
                     ON `i`.`itmid` = `ix`.`ixITEM`
                     ORDER BY `ix`.`ixTIME` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
        $same =
                ($r['ixFROMIP'] == $r['ixTOIP'])
                        ? '<span style="color: red;">Yes</span>'
                        : '<span style="color: green;">No</span>';
        echo '
		<tr>
        	<td>' . date('F j Y, g:i:s a', (int)$r['ixTIME'])
                . "</td>
        	<td>{$r['sender']} [{$r['ixFROM']}]</td>
        	<td>{$r['sent']} [{$r['ixTO']}]</td>
        	<td>{$r['ixFROMIP']}</td>
        	<td>{$r['ixTOIP']}</td>
        	<td>$same</td>
        	<td>{$r['item']} x{$r['ixQTY']}</td>
        </tr>
           ";
    }
    $db->free_result($q);
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=itmlogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    $mypage = floor($_GET['st'] / 100) + 1;
    stafflog_add("Looked at the Item Xfer Logs (Page $mypage)");
}

/**
 * @return void
 */
function view_cash_logs(): void
{
    global $db;
    echo '<h3>Cash Xfer Logs</h3>';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query('SELECT COUNT(`cxFROM`)
    				 FROM `cashxferlogs`');
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no cash transfers yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=cashlogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <br />
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>ID</th>
    			<th>Time</th>
    			<th>User From</th>
    			<th>User To</th>
    			<th>Multi?</th>
    			<th>Amount</th>
    			<th>&nbsp;</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `cxAMOUNT`, `cxTO`, `cxFROM`, `cxTIME`, `cxID`,
                     `cxTOIP`, `cxFROMIP`, `u1`.`username` AS `sender`,
                     `u2`.`username` AS `sent`
                     FROM `cashxferlogs` AS `cx`
                     INNER JOIN `users` AS `u1`
                     ON `cx`.`cxFROM` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `cx`.`cxTO` = `u2`.`userid`
                     ORDER BY `cx`.`cxTIME` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
        $m =
                ($r['cxFROMIP'] == $r['cxTOIP'])
                        ? '<span style="color: red; font-weight: bold;">MULTI</span>'
                        : '';
        echo "
		<tr>
        	<td>{$r['cxID']}</td>
        	<td>" . date('F j, Y, g:i:s a', (int)$r['cxTIME'])
                . "</td>
        	<td>
        		<a href='viewuser.php?u={$r['cxFROM']}'>{$r['sender']}</a>
        		[{$r['cxFROM']}] (IP: {$r['cxFROMIP']})
        	</td>
        	<td>
        		<a href='viewuser.php?u={$r['cxTO']}'>{$r['sent']}</a>
        		[{$r['cxTO']}] (IP: {$r['cxTOIP']})
        	</td>
        	<td>$m</td>
        	<td> " . money_formatter((int)$r['cxAMOUNT'])
                . "</td>
        	<td>
        		[<a href='staff_punit.php?action=fedform&amp;XID={$r['cxFROM']}'>Jail Sender</a>]
        		[<a href='staff_punit.php?action=fedform&amp;XID={$r['cxTO']}'>Jail Receiver</a>]
        	</td>
        </tr>
           ";
    }
    $db->free_result($q);
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=atklogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    $mypage = floor($_GET['st'] / 100) + 1;
    stafflog_add("Viewed the Cash Xfer Logs (Page $mypage)");
}

/**
 * @return void
 */
function view_bank_logs(): void
{
    global $db;
    echo '<h3>Bank Xfer Logs</h3>';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query('SELECT COUNT(`cxFROM`)
    				 FROM `bankxferlogs`');
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no bank transfers yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=banklogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>ID</th>
    			<th>Time</th>
    			<th>User From</th>
    			<th>User To</th>
    			<th>Multi?</th>
    			<th>Amount</th>
    			<th>Bank Type</th>
    			<th>&nbsp;</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `cxAMOUNT`, `cxTO`, `cxFROM`, `cxTIME`, `cxID`,
                     `cxTOIP`, `cxFROMIP`, `cxBANK`,
                     `u1`.`username` AS `sender`, `u2`.`username` AS `sent`
                     FROM `bankxferlogs` AS `cx`
                     INNER JOIN `users` AS `u1`
                     ON `cx`.`cxFROM` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `cx`.`cxTO` = `u2`.`userid`
                     ORDER BY `cx`.`cxTIME` DESC
                     LIMIT $st, $app");
    $banks = ['bank' => 'City Bank', 'cyber' => 'Cyber Bank'];
    while ($r = $db->fetch_row($q))
    {
        $mb = $banks[$r['cxBANK']];
        $m =
                ($r['cxFROMIP'] == $r['cxTOIP'])
                        ? '<span style="color: red; font-weight: bold;">MULTI</span>'
                        : '';
        echo "
		<tr>
        	<td>{$r['cxID']}</td>
        	<td>" . date('F j, Y, g:i:s a', (int)$r['cxTIME'])
                . "</td>
        	<td>
        		<a href='viewuser.php?u={$r['cxFROM']}'>{$r['sender']}</a>
        		[{$r['cxFROM']}] (IP: {$r['cxFROMIP']})
        	</td>
        	<td>
        		<a href='viewuser.php?u={$r['cxTO']}'>{$r['sent']}</a>
        		[{$r['cxTO']}] (IP: {$r['cxTOIP']})
        	</td>
        	<td>$m</td>
        	<td> " . money_formatter((int)$r['cxAMOUNT'])
                . "</td>
            <td>$mb</td>
            <td>
            	[<a href='staff_punit.php?action=fedform&amp;XID={$r['cxFROM']}'>Jail Sender</a>]
            	[<a href='staff_punit.php?action=fedform&amp;XID={$r['cxTO']}'>Jail Receiver</a>]
            </td>
        </tr>
           ";
    }
    $db->free_result($q);
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=banklogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    $mypage = floor($_GET['st'] / 100) + 1;
    stafflog_add("Viewed the Bank Xfer Logs (Page $mypage)");
}

/**
 * @return void
 */
function view_crys_logs(): void
{
    global $db;
    echo '<h3>Crystal Xfer Logs</h3>';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q =
            $db->query(
                'SELECT COUNT(`cxFROM`)
    				 FROM `crystalxferlogs`');
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no crystal transfers yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=cryslogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>ID</th>
    			<th>Time</th>
    			<th>User From</th>
    			<th>User To</th>
    			<th>Multi?</th>
    			<th>Amount</th>
    			<th>&nbsp;</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `cxAMOUNT`, `cxTO`, `cxFROM`, `cxTIME`, `cxID`,
                     `cxTOIP`, `cxFROMIP`, `u1`.`username` AS `sender`,
                     `u2`.`username` AS `sent`
                     FROM `crystalxferlogs` AS `cx`
                     INNER JOIN `users` AS `u1`
                     ON `cx`.`cxFROM` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `cx`.`cxTO` = `u2`.`userid`
                     ORDER BY `cx`.`cxTIME` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
        $m =
                ($r['cxFROMIP'] == $r['cxTOIP'])
                        ? '<span style="color: red; font-weight: bold;">MULTI</span>'
                        : '';
        echo "
		<tr>
        	<td>{$r['cxID']}</td>
        	<td>" . date('F j, Y, g:i:s a', (int)$r['cxTIME'])
                . "</td>
        	<td>
        		<a href='viewuser.php?u={$r['cxFROM']}'>{$r['sender']}</a>
        		[{$r['cxFROM']}] (IP: {$r['cxFROMIP']})
        	</td>
        	<td>
        		<a href='viewuser.php?u={$r['cxTO']}'>{$r['sent']}</a>
        		[{$r['cxTO']}] (IP: {$r['cxTOIP']})
        	</td>
        	<td>$m</td>
        	<td>{$r['cxAMOUNT']} crystals</td>
        	<td>
        		[<a href='staff_punit.php?action=fedform&amp;XID={$r['cxFROM']}'>Jail Sender</a>]
        		[<a href='staff_punit.php?action=fedform&amp;XID={$r['cxTO']}'>Jail Receiver</a>]
        	</td>
        </tr>
           ";
    }
    $db->free_result($q);
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=cryslogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    $mypage = floor($_GET['st'] / 100) + 1;
    stafflog_add("Viewed the Crystal Xfer Logs (Page $mypage)");
}

/**
 * @return void
 */
function view_mail_logs(): void
{
    global $db;
    echo '<h3>Mail Logs</h3>';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query('SELECT COUNT(`mail_from`)
    				 FROM `mail`');
    $attacks = $db->fetch_single($q);
    if ($attacks == 0)
    {
        echo 'There have been no mails sent yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=maillogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>ID</th>
    			<th>Time</th>
    			<th>User From</th>
    			<th>User To</th>
    			<th width>Subj</th>
    			<th width='30%'>Msg</th>
    			<th>&nbsp;</th>
    		</tr>
    ";
    $q =
            $db->query(
                    "SELECT `mail_text`, `mail_subject`, `mail_to`,
                     `mail_from`, `mail_time`, `mail_id`,
                     `u1`.`username` AS `sender`, `u2`.`username` AS `sent`
                     FROM `mail` AS `m`
                     INNER JOIN `users` AS `u1`
                     ON `m`.`mail_from` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `m`.`mail_to` = `u2`.`userid`
                     WHERE `m`.`mail_from` != 0
                     ORDER BY `m`.`mail_time`  DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
        	<td>{$r['mail_id']}</td>
        	<td>" . date('F j, Y, g:i:s a', (int)$r['mail_time'])
                . "</td>
        	<td>{$r['sender']} [{$r['mail_from']}]</td>
        	<td>{$r['sent']} [{$r['mail_to']}]</td>
        	<td>{$r['mail_subject']}</td>
        	<td>" . strip_tags($r['mail_text'])
                . "</td>
        	<td>
        		[<a href='staff_punit.php?action=mailform&amp;XID={$r['mail_from']}'>MailBan Sender</a>]
        		[<a href='staff_punit.php?action=mailform&amp;XID={$r['mail_to']}'>MailBan Receiver</a>]
        	</td>
        </tr>
           ";
    }
    $db->free_result($q);
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=maillogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }

    $mypage = floor($_GET['st'] / 100) + 1;
    stafflog_add("Viewed the Mail Logs (Page $mypage)");
}

/**
 * @return void
 */
function view_staff_logs(): void
{
    global $db, $ir, $h;
    echo '<h3>Staff Logs</h3>';
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query('SELECT COUNT(`user`)
    				 FROM `stafflog`');
    $attacks = $db->fetch_single($q);
    if ($attacks == 0)
    {
        echo 'There have been no staff actions yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo 'Pages:&nbsp;';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=stafflogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
    echo "
    <table width='100%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>Staff</th>
    			<th>Action</th>
    			<th>Time</th>
    			<th>IP</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `ip`, `time`, `action`, `user`, `u`.`username`
                     FROM `stafflog` AS `s`
                     INNER JOIN `users` AS `u`
                     ON `s`.`user` = u.`userid`
                     ORDER BY `s`.`time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
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
    echo '
    </table>
    <br />
    Pages:&nbsp;
       ';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
        echo ($s == $st) ? '<b>' . $i . '</b>&nbsp;'
                : '<a href="staff_logs.php?action=stafflogs&st=' . $s . '">'
                        . $i . '</a>&nbsp;';
        echo ($i % 25 == 0) ? '<br />' : '';
    }
}

/**
 * @param database $db
 * @param int $count
 * @param int $items_per_page
 * @return array
 */
function paginate(database $db, int $count, int $items_per_page = 25): array
{
    $current_page = $_GET['page'] ?? 1;
    $page_count   = ceil($count / $items_per_page);
    $limit        = $current_page * $items_per_page;
    $get_data     = $db->query(
        'SELECT * FROM logs_cron_fails ORDER BY handled, time_logged LIMIT ' . $limit . ' , 25'
    );
    $data         = [];
    $ret          = '<div class="pagination" style="margin-top: 1em;">[Pages: ';
    while ($row = $db->fetch_row($get_data)) {
        $data[] = $row;
    }
    for ($i = 1; $i < $page_count; ++$i) {
        $ret .= '<a href="staff_logs.php?action=cron-fails&page=' . $i . '"' . ($current_page == $i ? ' style="font-weight:700;"' : '') . '>' . ($current_page == $i ? '('.$i.')' : $i) . '</a>&nbsp;';
    }
    $ret = substr($ret, 0, -6) . ']</div>';
    return [$ret, $data];
}

/**
 * @param database $db
 * @return void
 * @throws Exception
 */
function view_cron_fail_logs(database $db): void
{
    if (!empty($_GET['id']) && isset($_GET['handled'])) {
        $db->query(
            'UPDATE logs_cron_fails SET handled = 1 WHERE id = ' . $_GET['id'],
        );
    }
    $get_count = $db->query(
        'SELECT COUNT(*) FROM logs_cron_fails',
    );
    $count     = (int)$db->fetch_single($get_count);
    [$pages, $data] = paginate($db, $count);
    echo $pages . '<br>';
    echo '
    <h3>Failed Crons</h3>
    <table class="table" style="width: 100%;">
    <thead>
        <tr>
            <th scope="col">Cron</th>
            <th scope="col">Method</th>
            <th scope="col">Message</th>
            <th scope="col">Time Logged</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
    ';
    foreach ($data as $row) {
        $date = new DateTime($row['time_logged']);
        echo '
            <tr' . ($row['handled'] ? ' style="filter: invert(25%);"' : '') . '>
                <td>' . $row['cron'] . '</td>
                <td>' . $row['method'] . '</td>
                <td>' . $row['message'] . '</td>
                <td>' . $date->format('F jS, Y, H:i:s') . '</td>
                <td>
                    ' . (!$row['handled'] ? '<a href="staff_logs.php?action=cron-fails&page=' . $_GET['page'] . '&id=' . $row['id'] . '&handled=1">Flag as handled</a>' : '') . '
                </td>
            </tr>
        ';
    }
    echo '
    </tbody>
    </table>
    ';
    echo $pages;
}
$h->endpage();
