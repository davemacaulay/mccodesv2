<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $h;
require_once('globals.php');

/**
 * @param $goBackTo
 * @return void
 */
function csrf_error($goBackTo): void
{
    global $h;
    echo '<h3>Error</h3><hr />
    Your action has been blocked for your security.<br />
    Please make gang actions quickly after you open the form
    	- do not leave it open in tabs.<br />
    &gt; <a href="yourgang.php?action=' . $goBackTo . '">Try Again</a>';
    $h->endpage();
    exit;
}

/**
 * @param $formid
 * @param $goBackTo
 * @return void
 */
function csrf_stdverify($formid, $goBackTo): void
{
    if (!isset($_POST['verf'])
            || !verify_csrf_code($formid, stripslashes($_POST['verf'])))
    {
        csrf_error($goBackTo);
    }
}

if (!$ir['gang'])
{
    echo "You're not in a gang.";
}
else
{
    $gq =
            $db->query(
                    "SELECT `g`.*, `oc`.*
                     FROM `gangs` AS `g`
                     LEFT JOIN `orgcrimes` AS `oc`
                     ON `g`.`gangCRIME` = `oc`.`ocID`
                     WHERE `g`.`gangID` = {$ir['gang']}");
    if ($db->num_rows($gq) == 0)
    {
        echo "Error: Your gang has been deleted.<br />
        &gt; <a href='index.php'>Home</a>";
        $h->endpage();
        exit;
    }
    $gangdata = $db->fetch_row($gq);
    $db->free_result($gq);
    echo "
	<h3><u>Your Gang - {$gangdata['gangNAME']}</u></h3>
   	";
    $wq =
            $db->query(
                    "SELECT COUNT(`warID`)
                     FROM `gangwars`
                     WHERE `warDECLARER` = {$ir['gang']}
                     OR `warDECLARED` = {$ir['gang']}");
    if ($db->fetch_single($wq) > 0)
    {
        echo "
		<h3>
			<a href='yourgang.php?action=warview'>
			<span style='color: red;'>Your gang is currently in "
                . $db->fetch_single($wq)
                . ' war(s).</span>
            </a>
        </h3>
   		';
    }
    $db->free_result($wq);
    if (!isset($_GET['action']))
    {
        $_GET['action'] = '';
    }
    switch ($_GET['action'])
    {
    case 'summary':
        gang_summary();
        break;
    case 'members':
        gang_memberlist();
        break;
    case 'kick':
        gang_staff_kick();
        break;
    case 'forums':
        gang_forums();
        break;
    case 'donate':
        gang_donate();
        break;
    case 'donate2':
        gang_donate2();
        break;
    case 'warview':
        gang_warview();
        break;
    case 'staff':
        gang_staff();
        break;
    case 'leave':
        gang_leave();
        break;
    case 'atklogs':
        gang_atklogs();
        break;
    case 'crimes':
        gang_crimes();
        break;
    default:
        gang_index();
        break;
    }
}

/**
 * @return void
 */
function gang_index(): void
{
    global $db, $ir, $userid, $gangdata;
    echo "
    <table cellspacing=1 class='table'>
    		<tr>
    			<td><a href='yourgang.php?action=summary'>Summary</a></td>
    			<td><a href='yourgang.php?action=donate'>Donate</a></td>
    		</tr>
    		<tr>
    			<td><a href='yourgang.php?action=members'>Members</a></td>
    			<td><a href='yourgang.php?action=crimes'>Crimes</a></td>
    		</tr>
    		<tr>
    			<td><a href='yourgang.php?action=forums'>Forums</a></td>
    			<td><a href='yourgang.php?action=leave'>Leave</a></td>
    		</tr>
    		<tr>
    			<td><a href='yourgang.php?action=atklogs'>Attack Logs</a></td>
    			<td>
       ";
    if ($gangdata['gangPRESIDENT'] == $userid
            || $gangdata['gangVICEPRES'] == $userid)
    {
        echo "<a href='yourgang.php?action=staff&amp;act2=idx'>Staff Room</a>";
    }
    else
    {
        echo '&nbsp;';
    }
    echo "
				</td>
			</tr>
	</table>
	<br />
	<table cellspacing='1' class='table'>
		<tr>
			<td align='center' class='h'>Gang Announcement</td>
		</tr>
		<tr>
			<td bgcolor='#DDDDDD'>{$gangdata['gangAMENT']}</td>
		</tr>
	</table>
	<br />
	<b>Last 10 Gang Events</b>
	<br />
   	";
    $q =
            $db->query(
                    "SELECT `gevTIME`, `gevTEXT`
                     FROM `gangevents`
                     WHERE `gevGANG` = {$ir['gang']}
                     ORDER BY `gevTIME` DESC
                     LIMIT 10");
    echo "
	<table width='75%' cellspacing='1' class='table'>
		<tr>
			<th>Time</th>
			<th>Event</th>
		</tr>
   	";
    while ($r = $db->fetch_row($q))
    {
        echo '
		<tr>
			<td>' . date('F j Y, g:i:s a', (int)$r['gevTIME'])
                . "</td>
			<td>{$r['gevTEXT']}</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function gang_summary(): void
{
    global $db, $gangdata;
    echo '
    <b>General</b>
    <br />
       ';
    $pq =
            $db->query(
                    "SELECT `username`
                     FROM `users`
                     WHERE `userid` = {$gangdata['gangPRESIDENT']}");
    if ($db->num_rows($pq) > 0)
    {
        $ldrnm = $db->fetch_single($pq);
        echo "President:
        	<a href='viewuser.php?u={$gangdata['gangPRESIDENT']}'>
        	{$ldrnm}
        	</a><br />";
    }
    else
    {
        echo 'President: None<br />';
    }
    $db->free_result($pq);
    $vpq =
            $db->query(
                    "SELECT `username`
                     FROM `users`
                     WHERE `userid` = {$gangdata['gangVICEPRES']}");
    if ($db->num_rows($vpq) > 0)
    {
        $vldrnm = $db->fetch_single($vpq);
        echo "Vice-President:
        	<a href='viewuser.php?u={$gangdata['gangVICEPRES']}'>
        	{$vldrnm}
        	</a><br />";
    }
    else
    {
        echo 'Vice-President: None<br />';
    }
    $db->free_result($vpq);
    $cnt =
            $db->query(
                    "SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `gang` = {$gangdata['gangID']}");
    echo '
    Members: ' . $db->fetch_single($cnt)
            . "
    <br />
    Capacity: {$gangdata['gangCAPACITY']}
    <br />
    Respect Level: {$gangdata['gangRESPECT']}
    <hr />
    <b>Financial:</b>
    <br />
    Money in vault: " . money_formatter((int)$gangdata['gangMONEY'])
            . "
    <br />
    Crystals in vault: {$gangdata['gangCRYSTALS']}
       ";
}

/**
 * @return void
 */
function gang_memberlist(): void
{
    global $db, $userid, $gangdata;
    echo "
    <table cellspacing='1' class='table'>
    		<tr>
    			<th>User</th>
    			<th>Level</th>
    			<th>Days In Gang</th>
    			<th>&nbsp;</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `userid`, `username`, `daysingang`, `level`
                     FROM `users`
                     WHERE `gang` = {$gangdata['gangID']}
                     ORDER BY `daysingang` DESC, `level` DESC");
    $csrf = request_csrf_html('yourgang_kickuser');
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
        	<td><a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a></td>
        	<td>{$r['level']}</td>
        	<td>{$r['daysingang']}</td>
        	<td>
           ";
        if ($gangdata['gangPRESIDENT'] == $userid
                || $gangdata['gangVICEPRES'] == $userid)
        {
            echo "
            <form action='yourgang.php?action=kick' method='post'>
            	<input type='hidden' name='ID' value='{$r['userid']}' />
            	{$csrf}
            	<input type='submit' value='Kick' />
            </form>";
        }
        else
        {
            echo '&nbsp;';
        }
        echo '
			</td>
		</tr>
   		';
    }
    $db->free_result($q);
    echo "
	</table>
	<br />
	&gt; <a href='yourgang.php'>Go Back</a>
   	";
}

/**
 * @return void
 */
function gang_staff_kick(): void
{
    global $db, $ir, $userid, $gangdata;
    if ($gangdata['gangPRESIDENT'] == $userid
            || $gangdata['gangVICEPRES'] == $userid)
    {
        csrf_stdverify('yourgang_kickuser', 'members');
        $_POST['ID'] =
                (isset($_POST['ID']) && is_numeric($_POST['ID']))
                        ? abs(intval($_POST['ID'])) : 0;
        $who = $_POST['ID'];
        if ($who == $gangdata['gangPRESIDENT'])
        {
            echo 'The gang president cannot be kicked.';
        } elseif ($who == $userid) {
            echo 'You cannot kick yourself. If you wish to leave,
            transfer your powers to someone else and then leave like normal.';
        } else {
            $q =
                $db->query(
                    "SELECT `username`
                             FROM `users`
                             WHERE `userid` = $who
                             AND `gang` = {$gangdata['gangID']}");
            if ($db->num_rows($q) > 0) {
                $kdata = $db->fetch_row($q);
                $db->query(
                    "UPDATE `users`
                         SET `gang` = 0, `daysingang` = 0
                         WHERE `userid` = $who");
                $d_username =
                    htmlentities($kdata['username'], ENT_QUOTES,
                        'ISO-8859-1');
                $d_oname    =
                    htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
                echo "<b>{$d_username}</b> was kicked from the Gang.";
                $their_event =
                    "You were kicked out of {$gangdata['gangNAME']} by "
                    . "<a href='viewuser.php?u={$userid}'>"
                    . $d_oname . '</a>';
                event_add($who, $their_event);
                $gang_event =
                    $db->escape(
                        "<a href='viewuser.php?u={$who}'>"
                        . $d_username
                        . '</a> was kicked out of the gang by '
                        . "<a href='viewuser.php?u={$userid}'>"
                        . $d_oname . '</a>');
                $db->query(
                    "INSERT INTO `gangevents`
                         VALUES(NULL, {$gangdata['gangID']}, " . time()
                    . ", '{$gang_event}');");
            } else {
                echo 'Trying to kick non-existent user';
            }
            $db->free_result($q);
        }
    }
    else
    {
        echo 'You do not have permission to perform this action.';
    }
}

/**
 * @return void
 */
function gang_forums(): void
{
    global $db, $ir, $gangdata, $domain;
    $q =
            $db->query(
                    "SELECT `ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'gang'
                     AND `ff_owner` = {$ir['gang']}");
    if ($db->num_rows($q) == 0)
    {
        $gangdata['gangNAME'] = $db->escape($gangdata['gangNAME']);
        $db->query(
                "INSERT INTO `forum_forums`
                 VALUES(NULL, '{$gangdata['gangNAME']}', '', 0, 0, 0, 0, 'N/A',
                 0, 'N/A', 'gang', {$ir['gang']})");
        $r = [];
        $r['ff_id'] = $db->insert_id();
    }
    else
    {
        $r = $db->fetch_row($q);
        if ($r['ff_name'] != $gangdata['gangNAME'])
        {
            $gangdata['gangNAME'] = $db->escape($gangdata['gangNAME']);
            $db->query(
                    "UPDATE `forum_forums`
                     SET `ff_name` = '{$gangdata['gangNAME']}'
                     WHERE `ff_id` = {$r['ff_id']}");
        }
    }
    $db->free_result($q);
    ob_get_clean();
    $forum_url = "https://{$domain}/forums.php?viewforum={$r['ff_id']}";
    header("Location: {$forum_url}");
    exit;
}

/**
 * @return void
 */
function gang_donate(): void
{
    global $ir;
    $csrf = request_csrf_html('yourgang_donate');
    echo '
    <b>Enter the amounts you wish to donate.</b>
    <br />
    You have ' . money_formatter($ir['money'])
            . " money and {$ir['crystals']} crystals.
    <br />
    <form action='yourgang.php?action=donate2' method='post'>
    	<table height='300' cellspacing='1' class='table'>
    		<tr>
    			<td>
    				<b>Money:</b><br />
    				<input type='text' name='money' value='0' />
    			</td>
    			<td>
    				<b>Crystals:</b><br />
    				<input type='text' name='crystals' value='0' />
    			</td>
    		</tr>
    		<tr>
    			<td colspan='2' align='center'>
    			    {$csrf}
    				<input type='submit' value='Donate' />
    			</td>
    		</tr>
    	</table>
    </form>
       ";
}

/**
 * @return void
 */
function gang_donate2(): void
{
    global $db, $ir, $userid, $gangdata, $h;
    csrf_stdverify('yourgang_donate', 'donate');
    $_POST['money'] =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 0;
    $_POST['crystals'] =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    if (empty($_POST['money']) && empty($_POST['crystals']))
    {
        echo 'Invalid amount, please go back and try again.<br />
        &gt; <a href="yourgang.php?action=donate">Back</a>';
        $h->endpage();
        exit;
    }
    if ($_POST['money'] > $ir['money'])
    {
        echo 'You can\'t donate more money than you have,
        	please go back and try again.<br />
        &gt; <a href="yourgang.php?action=donate">Back</a>';
    } elseif ($_POST['crystals'] > $ir['crystals']) {
        echo 'You can\'t donate more crystals than you have,
        	please go back and try again.<br />
        &gt; <a href="yourgang.php?action=donate">Back</a>';
    } else {
        $db->query(
            "UPDATE `users`
                 SET `money` = `money` - {$_POST['money']},
                 `crystals` = `crystals` - {$_POST['crystals']}
                 WHERE `userid` = $userid");
        $db->query(
            "UPDATE `gangs`
                 SET `gangMONEY` = `gangMONEY` + {$_POST['money']},
                 `gangCRYSTALS` = `gangCRYSTALS` + {$_POST['crystals']}
                 WHERE `gangID` = {$gangdata['gangID']}");
        $my_name    = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
        $gang_event =
            $db->escape(
                "<a href='viewuser.php?u={$userid}'>" . $my_name
                . '</a>' . ' donated '
                . money_formatter($_POST['money'])
                . ' and/or '
                . number_format($_POST['crystals'])
                . ' crystals to the Gang.');
        $db->query(
            "INSERT INTO `gangevents`
                 VALUES(NULL, {$gangdata['gangID']}, " . time()
            . ", '{$gang_event}')");
        echo 'You donated ' . money_formatter($_POST['money'])
            . " and/or {$_POST['crystals']} crystals to the Gang.<br />
              &gt; <a href='index.php'>Go Home</a>";
    }
}

/**
 * @return void
 */
function gang_leave(): void
{
    global $db, $ir, $userid, $gangdata, $h;
    if ($gangdata['gangPRESIDENT'] == $userid
            || $gangdata['gangVICEPRES'] == $userid)
    {
        echo "You cannot leave while you are still president
        	or vice-president of your gang.<br />
        &gt; <a href='yourgang.php'>Back</a>";
        $h->endpage();
        exit;
    }
    if (isset($_POST['submit']) && $_POST['submit'] == 'Yes, leave!')
    {
        csrf_stdverify('yourgang_leave', 'leave');
        $db->query(
                "UPDATE `users`
        		 SET `gang` = 0, `daysingang` = 0
        		 WHERE `userid` = {$userid}");
        $gang_event =
                $db->escape(
                        "<a href='viewuser.php?u={$userid}'>"
                                . htmlentities($ir['username'], ENT_QUOTES,
                                        'ISO-8859-1') . '</a> left the Gang.');
        $db->query(
                "INSERT INTO `gangevents`
                                VALUES(NULL, {$ir['gang']}, " . time()
                        . ", '{$gang_event}')");
    } elseif (isset($_POST['submit']) && $_POST['submit'] == 'No, stay!') {
        echo "You stayed in your gang.<br />
        &gt; <a href='yourgang.php'>Go back</a>";
    } else {
        $csrf = request_csrf_html('yourgang_leave');
        echo "Are you sure you wish to leave your gang?
        <form action='yourgang.php?action=leave' method='post'>
            {$csrf}
        	<input type='submit' name='submit' value='Yes, leave!' />
        	<br />
        	<br />
        	<input type='submit' name='submit' value='No, stay!'
        	 onclick=\"window.location='yourgang.php';\" />
        </form>";
    }
}

/**
 * @return void
 */
function gang_warview(): void
{
    global $db, $ir, $gangdata;
    $wq =
            $db->query(
                    "SELECT *
                     FROM `gangwars`
                     WHERE `warDECLARER` = {$ir['gang']}
                     OR `warDECLARED` = {$ir['gang']}");
    echo "<b>These are the wars your gang is in.</b><br />
	<table width='75%' cellspacing='1' class='table'>
		<tr>
			<th>Time Started</th>
			<th>Versus</th>
			<th>Who Declared</th>
		</tr>";
    while ($r = $db->fetch_row($wq))
    {
        if ($gangdata['gangID'] == $r['warDECLARER'])
        {
            $w = 'You';
            $f = 'warDECLARED';
        }
        else
        {
            $w = 'Them';
            $f = 'warDECLARER';
        }
        $d = date('F j, Y, g:i:s a', (int)$r['warTIME']);
        $ggq =
                $db->query(
                        'SELECT `gangID`, gangNAME`
         				 FROM `gangs`
         				 WHERE `gangID` = ' . $r[$f]);
        $them = $db->fetch_row($ggq);
        echo "<tr>
        		<td>$d</td>
        		<td>
        			<a href='gangs.php?action=view&amp;ID={$them['gangID']}'>
                    {$them['gangNAME']}
                    </a>
                </td>
                <td>$w</td>
              </tr>";
    }
    echo '</table>';
}

/**
 * @return void
 */
function gang_atklogs(): void
{
    global $db, $ir;
    $atks =
            $db->query(
                    "SELECT `a`.*, `u1`.`username` AS `attackern`,
                     `u1`.`gang` AS `attacker_gang`,
                     `u2`.`username` AS `attackedn`,
                     `u2`.`gang` AS `attacked_gang`
                     FROM `attacklogs` AS `a`
                     INNER JOIN `users` AS `u1`
                     ON `a`.`attacker` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `a`.`attacked` = `u2`.`userid`
                     WHERE (`u1`.`gang` = {$ir['gang']}
                     OR `u2`.`gang` = {$ir['gang']})
                     AND `result` = 'won'
                     ORDER BY `time` DESC
                     LIMIT 50");
    echo "<b>Attack Logs - The last 50 attacks involving someone in your gang</b><br />
	<table width='75%' cellspacing='1' class='table'>
		<tr>
			<th>Time</th>
			<th>Attack</th>
		</tr>";
    while ($r = $db->fetch_row($atks))
    {
        if ($r['attacker_gang'] == $ir['gang'])
        {
            $color = 'green';
        }
        else
        {
            $color = 'red';
        }
        $d = date('F j, Y, g:i:s a', (int)$r['time']);
        echo "<tr>
        		<td>$d</td>
        		<td>
        			<a href='viewuser.php?u={$r['attacker']}'>{$r['attackern']}</a>
        			<span style='color: $color;'>attacked</font>
        			<a href='viewuser.php?u={$r['attacked']}'>{$r['attackedn']}</a>
        		</td>
        	  </tr>";
    }
    $db->free_result($atks);
    echo '</table>';
}

/**
 * @return void
 */
function gang_crimes(): void
{
    global $gangdata;
    if ($gangdata['gangCRIME'] > 0)
    {
        echo "This is the crime your gang is planning at the moment.<br />
		<b>Crime:</b> {$gangdata['ocNAME']}<br />
		<b>Hours Left:</b> {$gangdata['gangCHOURS']}";
    }
    else
    {
        echo 'Your gang is not currently planning a crime.';
    }
}

/**
 * @return void
 */
function gang_staff(): void
{
    global $userid, $gangdata, $h;
    if ($gangdata['gangPRESIDENT'] == $userid
            || $gangdata['gangVICEPRES'] == $userid)
    {
        if (!isset($_GET['act2']))
        {
            $_GET['act2'] = 'idx';
        }
        switch ($_GET['act2'])
        {
        case 'apps':
            gang_staff_apps();
            break;
        case 'vault':
            gang_staff_vault();
            break;
        case 'vicepres':
            gang_staff_vicepres();
            break;
        case 'pres':
            gang_staff_pres();
            break;
        case 'upgrade':
            gang_staff_upgrades();
            break;
        case 'declare':
            gang_staff_wardeclare();
            break;
        case 'surrender':
            gang_staff_surrender();
            break;
        case 'viewsurrenders':
            gang_staff_viewsurrenders();
            break;
        case 'crimes':
            gang_staff_orgcrimes();
            break;
        case 'massmailer':
            gang_staff_massmailer();
            break;
        case 'desc':
            gang_staff_desc();
            break;
        case 'ament':
            gang_staff_ament();
            break;
        case 'name':
            gang_staff_name();
            break;
        case 'tag':
            gang_staff_tag();
            break;
        case 'masspayment':
            gang_staff_masspayment();
            break;
        default:
            gang_staff_idx();
            break;
        }
    }
    else
    {
        echo 'Are you lost?<br />
        &gt; <a href="yourgang.php">Go back</a>';
        $h->endpage();
        exit;
    }
}

/**
 * @return void
 */
function gang_staff_idx(): void
{
    global $userid, $gangdata;
    echo "
    <b>General</b>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=vault'>Vault Management</a>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=apps'>Application Management</a>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=vicepres'>Change Vice-President</a>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=upgrade'>Upgrade Gang</a>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=crimes'>Organised Crimes</a>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=masspayment'>Mass Payment</a>
    <br />
    <a href='yourgang.php?action=staff&amp;act2=ament'>Change Gang Announcement</a>
    <br />
       ";
    if ($gangdata['gangPRESIDENT'] == $userid)
    {
        echo "
        <hr />
        <a href='yourgang.php?action=staff&amp;act2=pres'>Change President</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=declare'>Declare War</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=surrender'>Surrender</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=viewsurrenders'>View or Accept Surrenders</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=massmailer'>Mass Mail Gang</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=name'>Change Gang Name</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=desc'>Change Gang Desc.</a>
        <br />
        <a href='yourgang.php?action=staff&amp;act2=tag'>Change Gang Tag</a>
           ";
    }
}

/**
 * @return void
 */
function gang_staff_apps(): void
{
    global $db, $ir, $userid, $gangdata, $h;
    $_POST['app'] =
            (isset($_POST['app']) && is_numeric($_POST['app']))
                    ? abs(intval($_POST['app'])) : '';
    $what =
            (isset($_POST['what'])
                    && in_array($_POST['what'], ['accept', 'decline'],
                            true)) ? $_POST['what'] : '';
    if (!empty($_POST['app']) && !empty($what))
    {
        csrf_stdverify('yourgang_staff_apps', 'staff&amp;act2=apps');
        $aq =
                $db->query(
                        "SELECT `appUSER`, `username`
                         FROM `applications` AS `a`
                         INNER JOIN `users` AS `u`
                         ON `a`.`appUSER` = `u`.`userid`
                         WHERE `a`.`appID` = {$_POST['app']}
                         AND `a`.`appGANG` = {$gangdata['gangID']}");
        if ($db->num_rows($aq) > 0)
        {
            $appdata = $db->fetch_row($aq);
            if ($what == 'decline')
            {
                $db->query(
                        "DELETE FROM `applications`
                         WHERE `appID` = {$_POST['app']}");
                event_add($appdata['appUSER'],
                    "Your application to join the {$gangdata['gangNAME']} gang was declined");
                $gang_event =
                        $db->escape(
                                "<a href='viewuser.php?u={$userid}'>"
                                        . $ir['username']
                                        . '</a> has declined '
                                        . "<a href='viewuser.php?u={$appdata['appUSER']}'>"
                                        . $appdata['username']
                                        . '</a>\'s application to join the Gang.');
                $db->query(
                        "INSERT INTO `gangevents`
                         VALUES (NULL, {$gangdata['gangID']}, " . time()
                                . ", '{$gang_event}')");
                echo "
                You have declined the application by {$appdata['username']}.
                <br />
                <a href='yourgang.php?action=staff&amp;act2=apps'>&gt; Back</a>
                   ";
            }
            else
            {
                $cnt =
                        $db->query(
                                "SELECT COUNT(`userid`)
                                 FROM `users`
                                 WHERE `gang` = {$gangdata['gangID']}");
                if ($gangdata['gangCAPACITY'] <= $db->fetch_single($cnt))
                {
                    $db->free_result($cnt);
                    echo 'Your gang is full, you must upgrade it to hold more before you can accept another user!';
                    $h->endpage();
                    exit;
                } elseif ($appdata['gang'] != 0) {
                    $db->free_result($cnt);
                    echo 'That person is already in a gang.';
                    $h->endpage();
                    exit;
                }
                $db->free_result($cnt);
                $db->query(
                        "DELETE FROM `applications`
                         WHERE `appID` = {$_POST['app']}");
                event_add($appdata['appUSER'],
                    "Your application to join the {$gangdata['gangNAME']} gang was accepted, Congrats!");
                $gang_event =
                        $db->escape(
                                "<a href='viewuser.php?u={$userid}'>"
                                        . $ir['username']
                                        . '</a> has accepted '
                                        . "<a href='viewuser.php?u={$appdata['appUSER']}'>"
                                        . $appdata['username']
                                        . '</a>\'s application to join the Gang.');
                $db->query(
                        "INSERT INTO `gangevents`
                         VALUES (NULL, {$gangdata['gangID']}, " . time()
                                . ", '{$gang_event}')");
                $db->query(
                        "UPDATE `users`
                         SET `gang` = {$gangdata['gangID']},
                         `daysingang` = 0
                         WHERE `userid` = {$appdata['appUSER']}");
                echo "
                You have accepted the application by {$appdata['username']}.
                <br />
                &gt; <a href='yourgang.php?action=staff&amp;act2=apps'>Back</a>
                   ";
            }
        }
        else
        {
            echo "Invalid application.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=apps'>Back</a>";
        }
        $db->free_result($aq);
    }
    else
    {
        echo "
        <b>Applications</b>
        <br />
        <table width='85%' cellspacing='1' class='table'>
        		<tr>
        			<th>User</th>
        			<th>Level</th>
        			<th>Money</th>
        			<th>Reason</th>
        			<th>&nbsp;</th>
        		</tr>
   		";
        $q =
                $db->query(
                        "SELECT `appTEXT`, `userid`, `username`, `level`,
                         `money`, `appID`
                         FROM `applications` AS `a`
                         INNER JOIN `users` AS `u`
                         ON `a`.`appUSER` = `u`.`userid`
                         WHERE `a`.`appGANG` = {$gangdata['gangID']}");
        $csrf = request_csrf_html('yourgang_staff_apps');
        while ($r = $db->fetch_row($q))
        {
            $r['appTEXT'] =
                    htmlentities($r['appTEXT'], ENT_QUOTES, 'ISO-8859-1',
                            false);
            echo "
            <tr>
            	<td>
            		<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
            		[{$r['userid']}]
            	</td>
            	<td>{$r['level']}</td>
            	<td>" . money_formatter((int)$r['money'])
                    . "</td>
            	<td>{$r['appTEXT']}</td>
            	<td>
            		<form action='yourgang.php?action=staff&amp;act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['appID']}' />
            			<input type='hidden' name='what' value='accept' />
            			{$csrf}
            			<input type='submit' value='Accept' />
            		</form>
            		<form action='yourgang.php?action=staff&amp;act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['appID']}' />
            			<input type='hidden' name='what' value='decline' />
            			{$csrf}
            			<input type='submit' value='Decline' />
            		</form>
            	</td>
            </tr>
               ";
        }
        echo '</table>';
    }
}

/**
 * @return void
 */
function gang_staff_vault(): void
{
    global $db, $gangdata, $h;
    $_POST['who'] =
            (isset($_POST['who']) && is_numeric($_POST['who']))
                    ? abs(intval($_POST['who'])) : '';
    if (!empty($_POST['who']))
    {
        csrf_stdverify('yourgang_staff_vault', 'staff&amp;act2=vault');
        $_POST['crystals'] =
                (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                        ? abs(intval($_POST['crystals'])) : 0;
        $_POST['money'] =
                (isset($_POST['money']) && is_numeric($_POST['money']))
                        ? abs(intval($_POST['money'])) : 0;
        if ($_POST['crystals'] > $gangdata['gangCRYSTALS'])
        {
            echo 'The vault does not have that many crystals!';
        } elseif ($_POST['money'] > $gangdata['gangMONEY']) {
            echo 'The vault does not have that much money!';
        } elseif ($_POST['money'] == 0 && $_POST['crystals'] == 0) {
            echo 'You cannot give nothing away.';
        } else {
            $who = $_POST['who'];
            $md  =
                $db->query(
                    "SELECT `username`
             				 FROM `users`
             				 WHERE `userid` = $who
             				 AND `gang` = {$gangdata['gangID']}");
            if ($db->num_rows($md) == 0) {
                $db->free_result($md);
                echo "That user doesn't exist or isn't in this gang.<br />
                &gt; <a href='yourgang.php?action=staff&amp;act2=vault'>Back</a>";
                $h->endpage();
                exit;
            }
            $dname =
                htmlentities($db->fetch_single($md), ENT_QUOTES,
                    'ISO-8859-1');
            $db->free_result($md);
            $money = $_POST['money'];
            $crys  = $_POST['crystals'];
            $db->query(
                "UPDATE `users`
                     SET `money` = `money` + $money,
                     `crystals` = `crystals` + $crys
                     WHERE `userid` = $who");
            $db->query(
                "UPDATE `gangs`
                     SET `gangMONEY` = `gangMONEY` - $money,
                     `gangCRYSTALS` = `gangCRYSTALS` - $crys
                     WHERE `gangID` = {$gangdata['gangID']}");
            event_add($who,
                'You were given ' . money_formatter($money)
                . " and/or $crys crystals from your Gang.");
            $gang_event =
                $db->escape(
                    "<a href='viewuser.php?u=$who'>" . $dname
                    . '</a> was given '
                    . money_formatter($money) . ' and/or '
                    . number_format($crys)
                    . ' crystals from the Gang.');
            $db->query(
                "INSERT INTO `gangevents`
                     VALUES(NULL, {$gangdata['gangID']}, " . time()
                . ",'{$gang_event}')");
            echo "<a href='viewuser.php?u=$who'>{$dname}</a> was given "
                . money_formatter($money) . ' and/or '
                . number_format($crys) . ' crystals from the Gang.';
        }
    }
    else
    {
        $csrf = request_csrf_html('yourgang_staff_vault');
        echo 'The vault has ' . money_formatter((int)$gangdata['gangMONEY'])
                . " and {$gangdata['gangCRYSTALS']} crystals.<br />
        <form action='yourgang.php?action=staff&amp;act2=vault' method='post'>
        Give
        	\$<input type='text' name='money' /> and
        	<input type='text' name='crystals' /> crystals
        <br />
        To: <select name='who' type='dropdown'>";
        $q =
                $db->query(
                        "SELECT `userid` , `username`
                         FROM `users`
                         WHERE `gang` = {$gangdata['gangID']}");
        while ($r = $db->fetch_row($q))
        {
            echo "\n<option value='{$r['userid']}'>{$r['username']}</option>";
        }
        $db->free_result($q);
        echo "</select><br />
        {$csrf}
		<input type='submit' value='Give' /></form>";
    }
}

/**
 * @return void
 */
function gang_staff_vicepres(): void
{
    global $db, $gangdata, $h;
    if (isset($_POST['subm']))
    {
        csrf_stdverify('gang_staff_vicepres', 'staff&amp;act2=vicepres');
        $_POST['vp'] =
                (isset($_POST['vp']) && is_numeric($_POST['vp']))
                        ? abs(intval($_POST['vp'])) : 0;
        $q =
                $db->query(
                        "SELECT `userid`, `username`
                         FROM `users`
                         WHERE `userid` = {$_POST['vp']}
                         AND `gang` = {$gangdata['gangID']}");
        if ($db->num_rows($q) < 1)
        {
            $db->free_result($q);
            echo "Invalid user or user not in your gang.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=vicepres'>Back</a>";
            $h->endpage();
            exit;
        }
        $memb = $db->fetch_row($q);
        $db->free_result($q);
        $db->query(
                "UPDATE `gangs`
                 SET `gangVICEPRES` = {$_POST['vp']}
                 WHERE `gangID` = {$gangdata['gangID']}");
        event_add($memb['userid'],
            "You were transferred vice-presidency of {$gangdata['gangNAME']}.");
        $m_name = htmlentities($memb['username'], ENT_QUOTES, 'ISO-8859-1');
        echo "Vice-Presidency was transferred to {$m_name}";
    }
    else
    {
        $csrf = request_csrf_html('gang_staff_vicepres');
        $vp = $gangdata['gangVICEPRES'];
        echo "
        <form action='yourgang.php?action=staff&amp;act2=vicepres' method='post'>
			Enter the ID of the new vice-president.<br />
			<input type='hidden' name='subm' value='submit' />
			{$csrf}
			ID: <input type='text' name='vp' value='{$vp}' maxlength='7' size='7' /><br />
			<input type='submit' value='Change' />
		</form>";
    }
}

/**
 * @return void
 */
function gang_staff_wardeclare(): void
{
    global $db, $gangdata, $h;
    if (isset($_POST['subm']))
    {
        csrf_stdverify('yourgang_staff_declare', 'staff&amp;act2=declare');
        $_POST['gang'] =
                (isset($_POST['gang']) && is_numeric($_POST['gang']))
                        ? abs(intval($_POST['gang'])) : 0;
        if ($_POST['gang'] == $gangdata['gangID'])
        {
            echo "You can't declare war on your own gang.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=declare'>Go back</a>";
            $h->endpage();
            exit;
        }
        // Check for existence
        $data_q =
                $db->query(
                        "SELECT `gangNAME`
         				 FROM `gangs`
         				 WHERE `gangID` = {$_POST['gang']}");
        if ($db->num_rows($data_q) == 0)
        {
            $db->free_result($data_q);
            echo "Invalid gang to declare on.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=declare'>Go back</a>";
            $h->endpage();
            exit;
        }
        $them = $db->fetch_single($data_q);
        $db->free_result($data_q);
        $db->query(
                "INSERT INTO `gangwars`
                 VALUES(NULL, {$gangdata['gangID']}, {$_POST['gang']}, "
                        . time() . ')');
        $event =
                $db->escape(
                        "<a href='gangs.php?action=view&amp;ID={$gangdata['gangID']}'>"
                                . $gangdata['gangNAME']
                                . '</a> declared war on '
                                . "<a href='gangs.php?action=view&amp;ID={$_POST['gang']}'>"
                                . $them . '</a>');
        $ev_time = time();
        $db->query(
                "INSERT INTO `gangevents`
                VALUES(NULL, {$gangdata['gangID']}, {$ev_time}, '$event'),
                (NULL, {$_POST['gang']}, {$ev_time}, '$event')");
        echo 'You have declared war!';
    }
    else
    {
        $csrf = request_csrf_html('yourgang_staff_declare');
        echo "
        <form action='yourgang.php?action=staff&amp;act2=declare' method='post'>
			Choose who to declare war on.<br />
			<input type='hidden' name='subm' value='submit' />
			Gang: <select name='gang' type='dropdown'>";
        $q =
                $db->query(
                        "SELECT `gangID`, `gangNAME`
         				 FROM `gangs`
         				 WHERE `gangID` != {$gangdata['gangID']}");
        while ($r = $db->fetch_row($q))
        {
            echo "<option value='{$r['gangID']}'>{$r['gangNAME']}</option>\n";
        }
        $db->free_result($q);
        echo "</select><br />
        	{$csrf}
			<input type='submit' value='Declare' />
		</form>";
    }
}

/**
 * @return void
 */
function gang_staff_surrender(): void
{
    global $db, $gangdata, $h;
    if (!isset($_POST['subm']))
    {
        $wq =
                $db->query(
                        "SELECT *
                         FROM `gangwars`
                         WHERE `warDECLARER` = {$gangdata['gangID']}
                         OR `warDECLARED` = {$gangdata['gangID']}");
        if ($db->num_rows($wq) > 0)
        {
            $csrf = request_csrf_html('yourgang_staff_surrender');
            echo "
        	<form action='yourgang.php?action=staff&amp;act2=surrender' method='post'>
				Choose who to surrender to.<br />
				<input type='hidden' name='subm' value='submit' />
				Gang: <select name='war' type='dropdown'>\n";
            while ($r = $db->fetch_row($wq))
            {
                if ($gangdata['gangID'] == $r['warDECLARER'])
                {
                    $f = 'warDECLARED';
                }
                else
                {
                    $f = 'warDECLARER';
                }
                $ggq =
                        $db->query(
                                "SELECT `gangNAME`
                                 FROM `gangs`
                                 WHERE `gangID` = {$r[$f]}");
                $them = $db->fetch_single($ggq);
                $db->free_result($ggq);
                echo "<option value='{$r['warID']}'>{$them}</option>\n";
            }
            echo "</select><br />
				Message: <input type='text' name='msg' /><br />
				{$csrf}
				<input type='submit' value='Surrender' />
			</form>";
        }
        else
        {
            echo "You aren't in any wars!";
        }
        $db->free_result($wq);
    }
    else
    {
        csrf_stdverify('yourgang_staff_surrender', 'staff&amp;act2=surrender');
        $_POST['war'] =
                (isset($_POST['war']) && is_numeric($_POST['war']))
                        ? abs(intval($_POST['war'])) : 0;
        $e_msg =
                $db->escape(
                        htmlentities(stripslashes($_POST['msg']), ENT_QUOTES,
                                'ISO-8859-1'));
        $wq =
                $db->query(
                        "SELECT *
                         FROM gangwars
                         WHERE `warID` = {$_POST['war']}");
        if ($db->num_rows($wq) == 0)
        {
            $db->free_result($wq);
            echo "Invalid war.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=surrender'>Back</a>";
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($wq);
        $db->free_result($wq);
        if ($gangdata['gangID'] == $r['warDECLARER'])
        {
            $f = 'warDECLARED';
        } elseif ($gangdata['gangID'] == $r['warDECLARED']) {
            $f = 'warDECLARER';
        } else {
            echo "Invalid war.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=surrender'>Back</a>";
            $h->endpage();
            exit;
        }
        $db->query(
                "INSERT INTO `surrenders`
                 VALUES(NULL, {$_POST['war']}, {$gangdata['gangID']}, {$r[$f]},
                 '{$e_msg}')");
        $ggq =
                $db->query(
                        "SELECT `gangNAME`
         				 FROM `gangs`
        				 WHERE `gangID` = {$r[$f]}");
        $them = $db->fetch_single($ggq);
        $db->free_result($ggq);
        $event =
                $db->escape(
                        "<a href='gangs.php?action=view&amp;ID={$gangdata['gangID']}'>"
                                . $gangdata['gangNAME']
                                . '</a> have asked to surrender the war against '
                                . "<a href='gangs.php?action=view&amp;ID={$r[$f]}'>"
                                . $them . '</a>');
        $e_time = time();
        $db->query(
                "INSERT INTO `gangevents`
                 VALUES(NULL, {$gangdata['gangID']}, {$e_time}, '$event'),
                 (NULL, {$r[$f]}, {$e_time}, '$event')");
        echo 'You have asked to surrender.';
    }
}

/**
 * @return void
 */
function gang_staff_viewsurrenders(): void
{
    global $db, $gangdata, $h;
    if (!isset($_POST['subm']))
    {
        $wq =
                $db->query(
                        "SELECT `surID`, `surMSG`, `w`.*
                         FROM `surrenders` AS `s`
                         INNER JOIN `gangwars` AS `w`
                         ON `s`.`surWAR` = `w`.`warID`
                         WHERE `surTO` = {$gangdata['gangID']}");
        if ($db->num_rows($wq) > 0)
        {
            $csrf = request_csrf_html('yourgang_staff_acceptsurrender');
            echo "
        	<form action='yourgang.php?action=staff&amp;act2=viewsurrenders' method='post'>
				Choose who to accept the surrender from.<br />
				<input type='hidden' name='subm' value='submit' />
				Gang: <select name='sur' type='dropdown'>";
            while ($r = $db->fetch_row($wq))
            {
                if ($gangdata['gangID'] == $r['warDECLARER'])
                {
                    $f = 'warDECLARED';
                }
                else
                {
                    $f = 'warDECLARER';
                }
                $ggq =
                        $db->query(
                                "SELECT `gangNAME`
                                 FROM `gangs`
                                 WHERE `gangID` = {$r[$f]}");
                $them = $db->fetch_single($ggq);
                $db->free_result($ggq);
                echo "<option value='{$r['surID']}'>War vs. {$them} (Msg: {$r['surMSG']})</option>";
            }
            echo "</select><br />
                {$csrf}
            	<input type='submit' value='Accept Surrender' />
            </form>";
        }
        else
        {
            echo 'There are no active surrenders for you to deal with.';
        }
        $db->free_result($wq);
    }
    else
    {
        csrf_stdverify('yourgang_staff_acceptsurrender',
                'staff&amp;act2=viewsurrenders');
        $_POST['sur'] =
                (isset($_POST['sur']) && is_numeric($_POST['sur']))
                        ? abs(intval($_POST['sur'])) : 0;
        $q =
                $db->query(
                        "SELECT `w`.*
                         FROM `surrenders` AS `s`
                         INNER JOIN `gangwars` AS `w`
                         ON `s`.`surWAR` = `w`.`warID`
                         WHERE `surID` = {$_POST['sur']}
                         AND `surTO` = {$gangdata['gangID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo "Invalid surrender.<br />
            &gt; <a href='yourgang.php?action=staff&amp;act2=viewsurrenders'>Back</a>";
            $h->endpage();
            exit;
        }
        $surr = $db->fetch_row($q);
        $db->free_result($q);
        $warID = $surr['warID'];
        if ($gangdata['gangID'] == $surr['warDECLARER'])
        {
            $f = 'warDECLARED';
        }
        else
        {
            $f = 'warDECLARER';
        }
        // Fix: delete all surrenders for the same war at same time
        $db->query(
                "DELETE FROM `surrenders`
         		 WHERE `surWAR` = {$warID}");
        $db->query(
                "DELETE FROM `gangwars`
         		 WHERE `warID` = {$warID}");
        $ggq =
                $db->query(
                        "SELECT `gangNAME`
         				 FROM `gangs`
         				 WHERE `gangID` = {$surr[$f]}");
        $them = $db->fetch_single($ggq);
        $db->free_result($ggq);
        $event =
                $db->escape(
                        "<a href='gangs.php?action=view&amp;ID={$gangdata['gangID']}'>"
                                . $gangdata['gangNAME']
                                . '</a> have accepted the surrender from '
                                . "<a href='gangs.php?action=view&amp;ID={$surr[$f]}'>"
                                . $them . '</a>, the war is over!');
        $ev_time = time();
        $db->query(
                "INSERT INTO `gangevents`
                 VALUES(NULL, {$gangdata['gangID']}, {$ev_time}, '$event'),
                 (NULL, {$surr[$f]}, {$ev_time}, '$event')");
        echo "You have accepted the surrender from {$them}, the war is over.";
    }
}

/**
 * @return void
 */
function gang_staff_orgcrimes(): void
{
    global $db, $gangdata, $h;
    $_POST['crime'] =
            (isset($_POST['crime']) && is_numeric($_POST['crime']))
                    ? abs(intval($_POST['crime'])) : 0;
    if ($_POST['crime'])
    {
        csrf_stdverify('yourgang_staff_orgcrimes', 'staff&amp;act2=crimes');
        if ($gangdata['gangCRIME'] != 0)
        {
            echo 'Your gang is already doing a crime!';
        }
        else
        {
            // Check Existence
            $crime_eq =
                    $db->query(
                            "SELECT COUNT(`ocID`)
                             FROM `orgcrimes`
                             WHERE `ocID` = {$_POST['crime']}");
            if ($db->fetch_single($crime_eq) == 0)
            {
                $db->free_result($crime_eq);
                echo "Invalid crime.<br />
            	&gt; <a href='yourgang.php?action=staff&amp;act2=crimes'>Back</a>";
                $h->endpage();
                exit;
            }
            $db->free_result($crime_eq);
            $db->query(
                    "UPDATE `gangs`
                     SET `gangCRIME` = {$_POST['crime']}, `gangCHOURS` = 24
                     WHERE `gangID` = {$gangdata['gangID']}");
            echo 'You have started to plan this crime. It will take 24 hours.';
        }
    }
    else
    {
        $cnt =
                $db->query(
                        "SELECT COUNT(`userid`)
                         FROM `users`
                         WHERE `gang` = {$gangdata['gangID']}");
        $membs = $db->fetch_single($cnt);
        $db->free_result($cnt);
        $q =
                $db->query(
                        "SELECT `ocID`, `ocNAME`, `ocUSERS`
                         FROM `orgcrimes`
                         WHERE `ocUSERS` <= $membs");
        if ($db->num_rows($q) > 0)
        {
            $csrf = request_csrf_html('yourgang_staff_orgcrimes');
            echo "<h3>Organised Crimes</h3>
			<form action='yourgang.php?action=staff&amp;act2=crimes' method='post'>
				Choose a crime that your gang should commit.<br />
				<select name='crime' type='dropdown'>";
            while ($r = $db->fetch_row($q))
            {
                echo "<option value='{$r['ocID']}'>{$r['ocNAME']}
                		({$r['ocUSERS']} members needed)</option>\n";
            }
            echo "</select>
            	<br />
            	{$csrf}
            	<input type='submit' value='Commit' />
            </form>";
        }
        else
        {
            echo '<h3>Organised Crimes</h3>
            There are no crimes that your gang can do.';
        }
        $db->free_result($q);
    }
}

/**
 * @return void
 */
function gang_staff_pres(): void
{
    global $db, $userid, $gangdata, $h;
    if ($gangdata['gangPRESIDENT'] == $userid)
    {
        if (isset($_POST['subm']))
        {
            csrf_stdverify('yourgang_staff_president', 'staff&amp;act2=pres');
            $_POST['pres'] =
                    (isset($_POST['pres']) && is_numeric($_POST['pres']))
                            ? abs(intval($_POST['pres'])) : 0;
            $q =
                    $db->query(
                            "SELECT `userid`, `username`
                             FROM `users`
                             WHERE `userid` = {$_POST['pres']}
                             AND `gang` = {$gangdata['gangID']}");
            if ($db->num_rows($q) < 1)
            {
                $db->free_result($q);
                echo "Invalid user or user not in your gang.<br />
            	&gt; <a href='yourgang.php?action=staff&amp;act2=pres'>Back</a>";
                $h->endpage();
                exit;
            }
            $memb = $db->fetch_row($q);
            $db->free_result($q);
            $db->query(
                    "UPDATE `gangs`
                     SET `gangPRESIDENT` = {$_POST['pres']}
                     WHERE `gangID` = {$gangdata['gangID']}");
            event_add($memb['userid'],
                "You were transferred presidency of {$gangdata['gangNAME']}.");
            echo "Presidency was transferred to {$memb['username']}<br />
            &gt; <a href='yourgang.php'>Gang home</a>";
        }
        else
        {
            $currp = $gangdata['gangPRESIDENT'];
            $csrf = request_csrf_html('yourgang_staff_president');
            echo "
            <form action='yourgang.php?action=staff&amp;act2=pres' method='post'>
				Enter the ID of the new president.<br />
				<input type='hidden' name='subm' value='submit' />
				ID: <input type='text' name='pres' value='{$currp}' maxlength='7' size='7' /><br />
				{$csrf}
				<input type='submit' value='Change' />
			</form>";
        }
    }
    else
    {
        echo 'This action is only available to the president of the gang.';
    }
}

/**
 * @return void
 */
function gang_staff_upgrades(): void
{
    global $db, $gangdata;
    if (isset($_POST['membs']))
    {
        csrf_stdverify('yourgang_staff_capacity', 'staff&amp;act2=upgrade');
        $_POST['membs'] =
                (is_numeric($_POST['membs']))
                        ? abs(intval($_POST['membs'])) : 0;
        if ($_POST['membs'] == 0)
        {
            echo "There's no point upgrading 0 capacity.";
        } elseif ($_POST['membs'] * 100000 > $gangdata['gangMONEY']) {
            echo 'Your gang does not have enough money to upgrade that much capacity.';
        } else {
            $cost = $_POST['membs'] * 100000;
            $db->query(
                "UPDATE `gangs`
                     SET `gangCAPACITY` = `gangCAPACITY` + {$_POST['membs']},
                     `gangMONEY` = `gangMONEY` - $cost
            		 WHERE `gangID` = {$gangdata['gangID']}");
            echo 'You paid ' . money_formatter($cost)
                . " to add {$_POST['membs']} capacity to your gang.";
        }
    }
    else
    {
        $csrf = request_csrf_html('yourgang_staff_capacity');
        echo "<h3>Capacity</h3>
		Current Capacity: {$gangdata['gangCAPACITY']}<br />
		<form action='yourgang.php?action=staff&amp;act2=upgrade' method='post'>
			Enter the amount of extra capacity you need.
			Each extra member slot costs " . money_formatter(100000)
                . ".<br />
			<input type='text' name='membs' value='1' /><br />
			{$csrf}
			<input type='submit' value='Buy' />
		</form>";
    }
}

/**
 * @return void
 */
function gang_staff_massmailer(): void
{
    global $db, $ir, $gangdata;
    $_POST['text'] =
            (isset($_POST['text']) && strlen($_POST['text']) < 500)
                    ? $db->escape(
                            htmlentities(stripslashes($_POST['text']),
                                    ENT_QUOTES, 'ISO-8859-1')) : '';
    if (!empty($_POST['text']))
    {
        csrf_stdverify('yourgang_staff_massmailer',
                'staff&amp;act2=massmailer');
        $subj = 'This is a mass mail from your gang';
        $mass_time = time();
        $q =
                $db->query(
                        "SELECT `username`, `userid`
                         FROM `users`
                         WHERE `gang` = {$gangdata['gangID']}");
        while ($r = $db->fetch_row($q))
        {
            $db->query(
                    "INSERT INTO `mail`
                     VALUES(NULL, 0, {$ir['userid']}, {$r['userid']},
                     {$mass_time}, '$subj', '{$_POST['text']}')");
            echo "Mass mail sent to {$r['username']}.<br />";
        }
        $db->free_result($q);
        echo "
		Mass mail sending complete!
		<br />
		&gt; <a href='yourgang.php?action=staff'>Go Back</a>
   		";
    }
    else
    {
        $csrf = request_csrf_html('yourgang_staff_massmailer');
        echo "
        <h3>Mass Mailer</h3>
        <form action='yourgang.php?action=staff&amp;act2=massmailer' method='post'>
        	Text: <br />
        	<textarea name='text' rows='7' cols='40'></textarea>
        	<br />
        	{$csrf}
        	<input type='submit' value='Send' />
        </form>
           ";
    }
}

/**
 * @return void
 */
function gang_staff_masspayment(): void
{
    global $db, $gangdata;
    $_POST['amt'] =
            (isset($_POST['amt']) && is_numeric($_POST['amt']))
                    ? abs(intval($_POST['amt'])) : 0;
    if ($_POST['amt'])
    {
        csrf_stdverify('yourgang_staff_masspayment',
                'staff&amp;act2=masspayment');
        $q =
                $db->query(
                        "SELECT `userid`, `username`
                         FROM `users`
                         WHERE `gang` = {$gangdata['gangID']}");
        while ($r = $db->fetch_row($q))
        {
            if ($gangdata['gangMONEY'] >= $_POST['amt'])
            {
                event_add($r['userid'],
                    'You were given ' . money_formatter($_POST['amt'])
                    . ' from your gang.');
                $db->query(
                        "UPDATE `users`
                         SET `money` = `money` + {$_POST['amt']}
                         WHERE `userid` = {$r['userid']}");
                $gangdata['gangMONEY'] -= $_POST['amt'];
                echo "Money sent to {$r['username']}.<br />";
            }
            else
            {
                echo "Not enough in the vault to pay {$r['username']}!<br />";
            }
        }
        $db->query(
                "UPDATE `gangs`
                 SET `gangMONEY` = {$gangdata['gangMONEY']}
                 WHERE `gangID` = {$gangdata['gangID']}");
        $credit_evt =
                $db->escape(
                        'A mass payment of ' . money_formatter($_POST['amt'])
                                . ' was sent to the members of the Gang.');
        $db->query(
                "INSERT INTO `gangevents`
                 VALUES(NULL, {$gangdata['gangID']}, " . time()
                        . ", '{$credit_evt}')");
        echo "Mass payment sending complete!<br />
		&gt; <a href='yourgang.php?action=staff'>Back</a>";
    }
    else
    {
        $csrf = request_csrf_html('yourgang_staff_masspayment');
        echo "<h3>Mass Payment</h3>
		<form action='yourgang.php?action=staff&amp;act2=masspayment' method='post'>
			Amount: <input type='text' name='amt' value='0' /><br />
			{$csrf}
			<input type='submit' value='Send' />
		</form>";
    }
}

/**
 * @return void
 */
function gang_staff_desc(): void
{
    global $db, $userid, $gangdata;
    if ($gangdata['gangPRESIDENT'] == $userid)
    {
        if (isset($_POST['subm']) && isset($_POST['desc']))
        {
            csrf_stdverify('yourgang_staff_desc', 'staff&amp;act2=desc');
            $desc =
                    $db->escape(
                            nl2br(
                                    htmlentities(
                                            stripslashes($_POST['desc']),
                                            ENT_QUOTES, 'ISO-8859-1')));
            $db->query(
                    "UPDATE `gangs`
                     SET `gangDESC` = '{$desc}'
                     WHERE `gangID` = {$gangdata['gangID']}");
            echo "Gang description changed!<br />
			&gt; <a href='yourgang.php?action=staff'>Back</a>";
        }
        else
        {
            $desc_for_area = strip_tags($gangdata['gangDESC']);
            $csrf = request_csrf_html('yourgang_staff_desc');
            echo "Current Description: <br />
            {$gangdata['gangDESC']}
            <form action='yourgang.php?action=staff&amp;act2=desc' method='post'>
				Enter the new description.<br />
				<input type='hidden' name='subm' value='submit' />
				Desc: <br />
				<textarea name='desc' cols='40' rows='7'>{$desc_for_area}</textarea><br />
				{$csrf}
				<input type='submit' value='Change' />
			</form>";
        }
    }
    else
    {
        echo 'This action is only available to the president of the gang.';
    }
}

/**
 * @return void
 */
function gang_staff_ament(): void
{
    global $db, $userid, $gangdata;
    if ($gangdata['gangPRESIDENT'] == $userid)
    {
        if (isset($_POST['subm']) && isset($_POST['ament']))
        {
            csrf_stdverify('yourgang_staff_ament', 'staff&amp;act2=ament');
            $ament =
                    $db->escape(
                            nl2br(
                                    htmlentities(
                                            stripslashes($_POST['ament']),
                                            ENT_QUOTES, 'ISO-8859-1')));
            $db->query(
                    "UPDATE `gangs`
                     SET `gangAMENT` = '{$ament}'
                     WHERE `gangID` = {$gangdata['gangID']}");
            echo "Gang announcement changed!<br />
			&gt; <a href='yourgang.php?action=staff'>Back</a>";
        }
        else
        {
            $am_for_area = strip_tags($gangdata['gangAMENT']);
            $csrf = request_csrf_html('yourgang_staff_ament');
            echo "Current Announcement: <br />
            {$gangdata['gangAMENT']}
            <form action='yourgang.php?action=staff&amp;act2=ament' method='post'>
				Enter the new announcement.<br />
				<input type='hidden' name='subm' value='submit' />
				Announcement: <br />
				<textarea name='ament' cols='40' rows='7'>{$am_for_area}</textarea><br />
				{$csrf}
				<input type='submit' value='Change' />
			</form>";
        }
    }
    else
    {
        echo 'This action is only available to the president of the gang.';
    }
}

/**
 * @return void
 */
function gang_staff_name(): void
{
    global $db, $userid, $gangdata;
    if ($gangdata['gangPRESIDENT'] == $userid)
    {
        if (isset($_POST['subm']) && isset($_POST['name']))
        {
            csrf_stdverify('yourgang_staff_name', 'staff&amp;act2=name');
            $name =
                    $db->escape(
                            htmlentities(stripslashes($_POST['name']),
                                    ENT_QUOTES, 'ISO-8859-1'));
            $db->query(
                    "UPDATE `gangs`
                     SET `gangNAME` = '{$name}'
                     WHERE `gangID` = {$gangdata['gangID']}");
            echo "Gang name changed!<br />
			&gt; <a href='yourgang.php?action=staff'>Back</a>";
        }
        else
        {
            $csrf = request_csrf_html('yourgang_staff_name');
            $gname = $gangdata['gangNAME'];
            echo "
            <form action='yourgang.php?action=staff&amp;act2=name' method='post'>
				Enter the new gang name.<br />
				<input type='hidden' name='subm' value='submit' />
				Name: <input type='text' name='name' value='{$gname}' /><br />
				{$csrf}
				<input type='submit' value='Change' />
			</form>";
        }
    }
    else
    {
        echo 'This action is only available to the president of the gang.';
    }
}

/**
 * @return void
 */
function gang_staff_tag(): void
{
    global $db, $userid, $gangdata;
    if ($gangdata['gangPRESIDENT'] == $userid)
    {
        if (isset($_POST['subm']) && isset($_POST['tag']))
        {
            csrf_stdverify('yourgang_staff_tag', 'staff&amp;act2=tag');
            $tag =
                    $db->escape(
                            htmlentities(stripslashes($_POST['tag']),
                                    ENT_QUOTES, 'ISO-8859-1'));
            $db->query(
                    "UPDATE `gangs`
                     SET `gangPREF` = '{$tag}'
                     WHERE `gangID` = {$gangdata['gangID']}");
            echo "Gang tag changed!<br />
			&gt; <a href='yourgang.php?action=staff'>Back</a>";
        }
        else
        {
            $csrf = request_csrf_html('yourgang_staff_tag');
            $gtag = $gangdata['gangPREF'];
            echo "
            <form action='yourgang.php?action=staff&amp;act2=tag' method='post'>
				Enter the new gang tag.<br />
				<input type='hidden' name='subm' value='submit' />
				Tag: <input type='text' name='tag' value='{$gtag}' /><br />
				{$csrf}
				<input type='submit' value='Change' />
			</form>";
        }
    }
    else
    {
        echo 'This action is only available to the president of the gang.';
    }
}

$h->endpage();
