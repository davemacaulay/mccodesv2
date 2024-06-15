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
 * File: slotsmachine.php
 * Signature: 23c6fb4c06bb49bf0b19d587cf81c1b8
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$tresder = (int) (rand(100, 999));
$maxbet = $ir['level'] * 250;
$_GET['tresde'] =
        (isset($_GET['tresde']) && is_numeric($_GET['tresde']))
                ? abs(intval($_GET['tresde'])) : 0;
if (!isset($_SESSION['tresde']))
{
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    die(
            "Error, you cannot refresh or go back on the slots,
            	please use a side link to go somewhere else.<br />
			<a href='slotsmachine.php?tresde=$tresder'>&gt; Back</a>");
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>Slots</h3>";
if (isset($_POST['bet']) && is_numeric($_POST['bet']))
{
    $_POST['bet'] = abs((int) $_POST['bet']);
    if ($_POST['bet'] > $ir['money'])
    {
        die(
                "You are trying to bet more than you have.<br />
		<a href='slotsmachine.php?tresde=$tresder'>&gt; Back</a>");
    }
    else if ($_POST['bet'] > $maxbet)
    {
        die(
                "You have gone over the max bet.<br />
		<a href='slotsmachine.php?tresde=$tresder'>&gt; Back</a>");
    }

    $slot[1] = (int) rand(0, 9);
    $slot[2] = (int) rand(0, 9);
    $slot[3] = (int) rand(0, 9);
    echo "You place " . money_formatter($_POST['bet'])
            . " into the slot and pull the pole.<br />
	You see the numbers: <b>$slot[1] $slot[2] $slot[3]</b><br />
	You bet " . money_formatter($_GET['bet']) . " ";
    if ($slot[1] == $slot[2] && $slot[2] == $slot[3])
    {
        $won = $_POST['bet'] * 26;
        $gain = $_POST['bet'] * 25;
        echo "and won " . money_formatter($won)
                . " by lining up 3 numbers pocketing you "
                . money_formatter($gain) . " extra.";
    }
    else if ($slot[1] == $slot[2] || $slot[2] == $slot[3]
            || $slot[1] == $slot[3])
    {
        $won = $_POST['bet'] * 3;
        $gain = $_POST['bet'] * 2;
        echo "and won " . money_formatter($won)
                . " by lining up 2 numbers pocketing you "
                . money_formatter($gain) . " extra.";
    }
    else
    {
        $won = 0;
        $gain = -$_POST['bet'];
        echo "and lost it.";
    }
    $db->query(
            "UPDATE `users`
    		 SET `money` = `money` + ({$gain})
    		 WHERE `userid` = $userid");
    $tresder = (int) (rand(100, 999));
    echo "<br />
    <form action='slotsmachine.php?tresde={$tresder}' method='post'>
    	<input type='hidden' name='bet' value='{$_POST['bet']}' />
    	<input type='submit' value='Another time, same bet.' />
    </form>
	&gt; <a href='slotsmachine.php?tresde=$tresder'>I'll continue, but I'm changing my bet.</a><br />
	&gt; <a href='explore.php'>Enough's enough, I'm off.</a>";
}
else
{
    echo "Ready to try your luck? Play today!<br />
	The maximum bet for your level is " . money_formatter($maxbet)
            . ".<br />
	<form action='slotsmachine.php?tresde={$tresder}' method='POST'>
		Bet: \$<input type='text' name='bet' value='5' /><br />
		<input type='submit' value='Play!!' />
	</form>";
}

$h->endpage();
