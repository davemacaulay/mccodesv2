<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
$tresder = rand(100, 999);
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
echo '<h3>Slots</h3>';
if (isset($_POST['bet']) && is_numeric($_POST['bet']))
{
    $_POST['bet'] = abs((int) $_POST['bet']);
    if ($_POST['bet'] > $ir['money'])
    {
        die(
                "You are trying to bet more than you have.<br />
		<a href='slotsmachine.php?tresde=$tresder'>&gt; Back</a>");
    }
    elseif ($_POST['bet'] > $maxbet)
    {
        die(
                "You have gone over the max bet.<br />
		<a href='slotsmachine.php?tresde=$tresder'>&gt; Back</a>");
    }

    $slot[1] = rand(0, 9);
    $slot[2] = rand(0, 9);
    $slot[3] = rand(0, 9);
    echo 'You place ' . money_formatter($_POST['bet'])
            . " into the slot and pull the pole.<br />
	You see the numbers: <b>$slot[1] $slot[2] $slot[3]</b><br />
	You bet " . money_formatter($_POST['bet']) . ' ';
    if ($slot[1] == $slot[2] && $slot[2] == $slot[3])
    {
        $won = $_POST['bet'] * 26;
        $gain = $_POST['bet'] * 25;
        echo 'and won ' . money_formatter($won)
                . ' by lining up 3 numbers pocketing you '
                . money_formatter($gain) . ' extra.';
    }
    elseif ($slot[1] == $slot[2] || $slot[2] == $slot[3]
            || $slot[1] == $slot[3])
    {
        $won = $_POST['bet'] * 3;
        $gain = $_POST['bet'] * 2;
        echo 'and won ' . money_formatter($won)
                . ' by lining up 2 numbers pocketing you '
                . money_formatter($gain) . ' extra.';
    }
    else
    {
        $won = 0;
        $gain = -$_POST['bet'];
        echo 'and lost it.';
    }
    $db->query(
            "UPDATE `users`
    		 SET `money` = `money` + ({$gain})
    		 WHERE `userid` = $userid");
    $tresder = rand(100, 999);
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
    echo 'Ready to try your luck? Play today!<br />
	The maximum bet for your level is ' . money_formatter($maxbet)
            . ".<br />
	<form action='slotsmachine.php?tresde={$tresder}' method='POST'>
		Bet: \$<input type='text' name='bet' value='5' /><br />
		<input type='submit' value='Play!!' />
	</form>";
}

$h->endpage();
