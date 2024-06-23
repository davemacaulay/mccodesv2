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
$maxbet = $ir['level'] * 150;
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
			<a href='roulette.php?tresde=$tresder'>&gt; Back</a>");
}
$_SESSION['tresde'] = $_GET['tresde'];

echo '<h3>Roulette: Pick a number between 0 - 36</h3>';
if (isset($_POST['bet']) && is_numeric($_POST['bet']))
{
    $_POST['bet'] = abs((int) $_POST['bet']);
    if (!isset($_POST['number']))
    {
        $_POST['number'] = 0;
    }
    $_POST['number'] = abs((int) $_POST['number']);
    if ($_POST['bet'] > $ir['money'])
    {
        die(
                "You are trying to bet more than you have.<br />
		<a href='roulette.php?tresde=$tresder'>&gt; Back</a>");
    }
    elseif ($_POST['bet'] > $maxbet)
    {
        die(
                "You have gone over the max bet.<br />
		<a href='roulette.php?tresde=$tresder'>&gt; Back</a>");
    }
    elseif ($_POST['number'] > 36 or $_POST['number'] < 0
            or $_POST['bet'] < 0)
    {
        die(
                "The Numbers are only 0 - 36.<br />
		<a href='roulette.php?tresde=$tresder'>&gt; Back</a>");
    }
    $slot = [];
    $slot[1] = rand(0, 36);
    echo 'You place ' . money_formatter($_POST['bet'])
            . " into the slot and pull the pole.<br />
	You see the number: <b>$slot[1]</b><br />
	You bet " . money_formatter($_POST['bet']) . ' ';
    if ($slot[1] == $_POST['number'])
    {
        $won = $_POST['bet'] * 37;
        $gain = $_POST['bet'] * 36;
        echo 'and won ' . money_formatter($won)
                . ' by matching the number you bet pocketing you '
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
	<form action='roulette.php?tresde={$tresder}' method='post'>
    	<input type='hidden' name='bet' value='{$_POST['bet']}' />
    	<input type='hidden' name='number' value='{$_POST['number']}' />
    	<input type='submit' value='Another time, same bet.' />
    </form>
	&gt; <a href='roulette.php?tresde=$tresder'>I'll continue, but I'm changing my bet.</a><br />
	&gt; <a href='explore.php'>Enough's enough, I'm off.</a>";
}
else
{
    echo 'Ready to try your luck? Play today!<br />
	The maximum bet for your level is ' . money_formatter($maxbet)
            . ".<br />
	<form action='roulette.php?tresde={$tresder}' method='POST'>
		Bet: \$<input type='text' name='bet' value='5' /><br />
		Pick (0-36): <input type='text' name='number' value='18' /><br />
		<input type='submit' value='Play!!' />
	</form>";
}

$h->endpage();
