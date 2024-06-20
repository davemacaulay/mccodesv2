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
 * File: battletent.php
 * Signature: 123e75cf1636fa36e03f6f04f9a2bfc2
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
echo "<h3>Battle Tent</h3>
<b>Welcome to the battle tent! Here you can challenge NPCs for money.</b>
<table width=100% cellspacing=1 class='table'>
	<tr style='background: gray; '>
		<th>Bot Name</th>
		<th>Level</th>
		<th>Times Owned</th>
		<th>Ready To Be Challenged?</th>
		<th>Location</th>
		<th>Money Won</th>
		<th>Challenge</th>
	</tr>";
$q =
        $db->query(
                "SELECT `cb`.`cb_money`, `c`.`npcid`, `cy`.`cityname`,
                        `u`.`userid`, `username`, `level`, `hp`, `maxhp`, `location`, `hospital`, `jail`
                FROM `challengebots` AS `cb`
                LEFT JOIN `users` AS `u` ON `cb`.`cb_npcid` = `u`.`userid`
                LEFT JOIN `challengesbeaten` AS `c` ON `c`.`npcid` = `u`.`userid` AND `c`.`userid` = $userid
                LEFT JOIN `cities` AS `cy` ON `u`.`location` = `cy`.`cityid`");
while ($r = $db->fetch_row($q))
{
    $earn = $r['cb_money'];
    $v = $r['userid'];
    $countq =
            $db->query(
                    "SELECT COUNT(`npcid`) FROM `challengesbeaten` WHERE `npcid` = $v");
    $times = $db->fetch_single($countq);
    $db->free_result($countq);
    echo "<tr><td>{$r['username']}</td><td>{$r['level']}</td><td>$times</td><td>";
    if ($r['hp'] >= $r['maxhp'] / 2 && $r['location'] == $ir['location']
            && !$ir['hospital'] && !$ir['jail'] && !$r['hospital']
            && !$r['jail'])
    {
        echo '<font color=green>Yes</font>';
    }
    else
    {
        echo '<font color=red>No</font>';
    }
    echo "</td><td>{$r['cityname']}</td><td>$earn</td><td>";
    if ($r['npcid'])
    {
        echo '<i>Already</i>';
    }
    else
    {
        echo "<a href='attack.php?ID={$r['userid']}'>Challenge</a>";
    }
    echo '</td></tr>';
}
$db->free_result($q);
echo '</table>';
$h->endpage();
