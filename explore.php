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
 * File: explore.php
 * Signature: 34dbb8d071be088c479855fcd855a20b
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$tresder = rand(100, 999);
if ($ir['jail'] > 0 || $ir['hospital'] > 0)
{
    die("This page cannot be accessed while in jail or hospital.");
}
echo "<b>You begin exploring the area you're in,
		you see a bit that interests you.</b><br />
<table width='75%'>
	<tr height='100'>
		<td valign='top'>
			<u>Market Place</u><br />
			<a href='shops.php'>Shops</a><br />
			<a href='itemmarket.php'>Item Market</a><br />
			<a href='cmarket.php'>Crystal Market</a>
		</td>
		<td valign='top'>
			<u>Serious Money Makers</u><br />
			<a href='monorail.php'>Travel Agency</a><br />
			<a href='estate.php'>Estate Agent</a><br />
			<a href='bank.php'>City Bank</a>";
if ($ir['location'] == 5)
{
    echo "	<br />
			<a href='cyberbank.php'>Cyber Bank</a><br />";
}
echo "	</td>
		<td valign='top'>
			<u>Dark Side</u><br />
			<a href='gangcentral.php'>Gangs</a><br />
			<a href='gangwars.php'>Gang Wars</a><br />
			<a href='fedjail.php'>Federal Jail</a><br />
			<a href='slotsmachine.php?tresde=$tresder'>Slots Machine</a><br />
			<a href='roulette.php?tresde=$tresder'>Roulette</a><br />
			<a href='lucky.php'>Lucky Boxes</a>";
if ($ir['location'] == 5)
{
    echo "	<br />
			<a href='slotsmachine3.php'>Super Slots</a><br />";
}
echo "	</td>
	</tr>
	<tr height='100'>
		<td valign='top'>
			<u>Statistics Dept</u><br />
			<a href='userlist.php'>User List</a><br />
			<a href='stafflist.php'>{$set['game_name']} Staff</a><br />
			<a href='halloffame.php'>Hall of Fame</a><br />
			<a href='stats.php'>Game Stats</a><br />
			<a href='usersonline.php'>Users Online</a>
		</td>
		<td valign='top'>
			<u>Mysterious</u><br />
			<a href='crystaltemple.php'>Crystal Temple</a><br />
			<a href='battletent.php'>Battle Tent</a><br />
			<a href='polling.php'>Polling Booth</a><br />
		</td>
	</tr>
</table>
<br /><br />
This is your referal link: http://{$domain}/register.php?REF={$userid}<br />
Every signup from this link earns you two valuable crystals!";
$h->endpage();
