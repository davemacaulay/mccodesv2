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
 * File: viewuser.php
 * Signature: 359abfc90736815bd4dd5e155cd1edf8
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $h, $set;
require_once('globals.php');
$_GET['u'] =
        (isset($_GET['u']) && is_numeric($_GET['u'])) ? abs(intval($_GET['u']))
                : '';
if (!$_GET['u'])
{
    echo 'Invalid use of file';
}
else
{
    $q =
            $db->query(
                    "SELECT `userid`, `user_level`, `laston`, `last_login`,
                    `signedup`, `duties`, `donatordays`, `username`, `gender`,
                    `daysold`, `money`, `crystals`, `level`, `friend_count`,
                    `enemy_count`, `display_pic`, `hp`, `maxhp`, `gang`,
                    `fedjail`, `hospital`, `hospreason`, `jail`, `jail_reason`,
                    `bankmoney`, `cybermoney`, `lastip`, `lastip`,
                    `lastip_login`, `lastip_signup`, `staffnotes`, `cityname`,
                    `hNAME`, `gangNAME`, `fed_days`, `fed_reason`
                    FROM `users` `u`
                    INNER JOIN `cities` AS `c`
                    ON `u`.`location` = `c`.`cityid`
                    INNER JOIN `houses` AS `h`
                    ON `u`.`maxwill` = h.`hWILL`
                    LEFT JOIN `gangs` AS `g`
                    ON `g`.`gangID` = `u`.`gang`
                    LEFT JOIN `fedjail` AS `f`
                    ON `f`.`fed_userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['u']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Sorry, we could not find a user with that ID, check your source.';
    }
    else
    {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $checkulevel =
                [0 => 'NPC', 1 => 'Member', 2 => 'Owner',
                        3 => 'Secretary', 5 => 'Assistant'];
        $userl = $checkulevel[$r['user_level']];
        $lon =
                ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston'])
                        : 'Never';
        $ula = ($r['laston'] == 0) ? 'Never' : datetime_parse($r['laston']);
        $ull =
                ($r['last_login'] == 0) ? 'Never'
                        : datetime_parse($r['last_login']);
        $sup = date('F j, Y g:i:s a', $r['signedup']);
        $u_duties =
                ($r['user_level'] > 1) ? 'Duties: ' . $r['duties'] . '<br />'
                        : '';
        $user_name =
                ($r['donatordays'])
                        ? '<span style="color:red; font-weight:bold;">'
                                . $r['username'] . '</span> [' . $r['userid']
                                . '] <img src="donator.gif" alt="Donator: '
                                . $r['donatordays']
                                . ' Days Left" title="Donator: '
                                . $r['donatordays'] . ' Days Left" />'
                        : $r['username'] . ' [' . $r['userid'] . ']';
        $on =
                ($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15 * 60)
                        ? '<font color="green"><b>Online</b></font>'
                        : '<font color="red"><b>Offline</b></font>';
        $ref_q =
                $db->query(
                        "SELECT COUNT(`refID`)
                         FROM `referals`
                         WHERE `refREFER` = {$r['userid']}");
        $ref = $db->fetch_single($ref_q);
        $db->free_result($ref_q);
        echo "
		<h3>Profile for {$r['username']}</h3>
    	<table width='100%' cellspacing='1' class='table'>
    	<tr>
    		<th>General Info</th>
    		<th>Financial Info</th>
    		<th>Display Pic</th>
    	</tr>
    	<tr>
    		<td>
                Name: $user_name<br />
                User Level: $userl<br />
                        $u_duties
                Gender: {$r['gender']}<br />
                Signed Up: $sup<br />
                Last Active: $lon<br />
                Last Action: $ula<br />
                Last Login: $ull<br />
                Online: $on<br />
                Days Old: {$r['daysold']}<br />
                Location: {$r['cityname']}</td><td>
                Money: " . money_formatter($r['money'])
                . "<br />
                Crystals: {$r['crystals']}<br />
                Property: {$r['hNAME']}<br />
                Referals: {$ref}<br />
                Friends: {$r['friend_count']}<br />
                Enemies: {$r['enemy_count']}
    		</td>
    		<td>
   		";
        echo ($r['display_pic'])
                ? '<img src="' . $r['display_pic']
                        . '" width="150px" height="150px" alt="User Display Pic" title="User Display Pic" />'
                : 'No Image';
        $sh = ($ir['user_level'] > 1) ? 'Staff Info' : '&nbsp;';
        echo "
			</td>
		</tr>
		<tr>
			<th>Physical Info</th>
			<th>Links</th>
			<th>$sh</th>
		</tr>
		<tr>
			<td>
				Level: {$r['level']}<br />
				Health: {$r['hp']}/{$r['maxhp']}<br />
   		";
        echo ($r['gang'])
                ? 'Gang: <a href="gangs.php?action=view&ID=' . $r['gang']
                        . '">' . $r['gangNAME'] . '</a>' : '';

        if ($r['fedjail'])
        {
            echo "
            <br />
            <span style='font-weight: bold; color: red;'>
            In federal jail for {$r['fed_days']} day(s).
            <br />
                        {$r['fed_reason']}
            </span>
               ";
        }
        if ($r['hospital'])
        {
            echo "
            <br />
            <span style='font-weight: bold; color: red;'>
            In hospital for {$r['hospital']} minutes.
            <br />
                        {$r['hospreason']}
            </span>
               ";
        }
        if ($r['jail'])
        {
            echo "
            <br />
            <span style='font-weight: bold; color: red;'>
            In jail for {$r['jail']} minutes.
            <br />
                        {$r['jail_reason']}
            </span>
               ";
        }

        echo "
			</td>
			<td>
				[<a href='mailbox.php?action=compose&ID={$r['userid']}'>Send Mail</a>]
				<br /><br />
				[<a href='sendcash.php?ID={$r['userid']}'>Send Cash</a>]
				<br /><br />
   		";
        if ($set['sendcrys_on'])
        {
            echo "
            [<a href='sendcrys.php?ID={$r['userid']}'>Send Crystals</a>]
            <br /><br />
               ";
        }
        if ($set['sendbank_on'])
        {
            if ($ir['bankmoney'] >= 0 && $r['bankmoney'] >= 0)
            {
                echo "
            [<a href='sendbank.php?ID={$r['userid']}'>Bank Xfer</a>]
            <br /><br />
               ";
            }
            if ($ir['cybermoney'] >= 0 && $r['cybermoney'] >= 0)
            {
                echo "
            [<a href='sendcyber.php?ID={$r['userid']}'>CyberBank Xfer</a>]
            <br /><br />
               ";
            }
        }
        echo "
				[<a href='attack.php?ID={$r['userid']}'>Attack</a>]
				<br /><br />
				[<a href='contactlist.php?action=add&ID={$r['userid']}'>Add Contact</a>]
   		";
        if (in_array($ir['user_level'], [2, 3, 5]))
        {
            echo "
        <br /><br />
        [<a href='jailuser.php?userid={$r['userid']}'>Jail</a>]
        <br /><br />
        [<a href='mailban.php?userid={$r['userid']}'>MailBan</a>]
           ";
        }
        if ($ir['donatordays'] > 0)
        {
            echo "
        <br /><br />
        [<a href='friendslist.php?action=add&ID={$r['userid']}'>Add Friends</a>]
        <br /><br />
        [<a href='blacklist.php?action=add&ID={$r['userid']}'>Add Enemies</a>]
        <br />
           ";
        }
        echo '
			</td>
			<td>
   		';
        if (in_array($ir['user_level'], [2, 3, 5]))
        {
            $r['lastiph'] = @gethostbyaddr($r['lastip']);
            $r['lastiph'] = checkblank($r['lastiph']);
            $r['lastip_loginh'] = @gethostbyaddr($r['lastip_login']);
            $r['lastip_loginh'] = checkblank($r['lastip_loginh']);
            $r['lastip_signuph'] = @gethostbyaddr($r['lastip_signup']);
            $r['lastip_signuph'] = checkblank($r['lastip_signuph']);
            echo "
            <h3>Internet Info</h3>
            <table width='100%' border='0' cellspacing='1' class='table'>
            		<tr>
            			<td></td>
            			<td class='h'>IP</td>
            			<td class='h'>Hostname</td>
            		</tr>
            		<tr>
            			<td class='h'>Last Hit</td>
            			<td>$r[lastip]</td>
            			<td>$r[lastiph]</td>
            		</tr>
            		<tr>
            			<td class='h'>Last Login</td>
            			<td>$r[lastip_login]</td>
            			<td>$r[lastip_loginh]</td>
            		</tr>
            		<tr>
            			<td class='h'>Signup</td>
            			<td>$r[lastip_signup]</td>
            			<td>$r[lastip_signuph]</td>
            		</tr>
            </table>

            <form action='staffnotes.php' method='post'>
            	Staff Notes:
            	<br />
            	<textarea rows=7 cols=40 name='staffnotes'>"
                    . htmlentities($r['staffnotes'], ENT_QUOTES, 'ISO-8859-1')
                    . "</textarea>
            	<br />
            	<input type='hidden' name='ID' value='{$_GET['u']}' />
            	<input type='submit' value='Change' />
            </form>
               ";
        }
        echo '
			</tr>
		</table>
   		';
    }
}

function checkblank($in)
{
    if (!$in)
    {
        return 'N/A';
    }
    return $in;
}
$h->endpage();
