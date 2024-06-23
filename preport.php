<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $userid, $h;
require_once('globals.php');
echo '<h3>Player Report</h3>';
$_POST['report'] =
        (isset($_POST['report']) && is_string($_POST['report']))
                ? $db->escape(strip_tags(stripslashes($_POST['report']))) : '';
$_POST['player'] =
        (isset($_POST['player']) && is_numeric($_POST['player']))
                ? abs(intval($_POST['player'])) : '';
if ($_POST['report'] && $_POST['player'])
{
    if (strlen($_POST['report']) > 500)
    {
        echo 'You may only enter 500 characters or less here.
        <br />&gt;<a href="preport.php?ID=' . $_GET['player']
                . '">Go Back</a>';
        $h->endpage();
        exit;
    }
    if (!isset($_POST['verf'])
            || !verify_csrf_code('preport_send', stripslashes($_POST['verf'])))
    {
        echo '<h3>Error</h3><hr />
   			This action has been blocked for your security.<br />
    		Please try again.<br />
    		&gt; <a href="preport.php">Try Again</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = ' . $_POST['player']);
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'User doesn\'t exist.<br />
        &gt;<a href="preport.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($q);
    $db->query(
            "INSERT INTO `preports`
             VALUES(NULL, $userid, {$_POST['player']}, '{$_POST['report']}')");
    echo 'Report processed!<br />
    &gt; <a href="index.php">Home</a>';
}
else
{
    $_GET['report'] =
            (isset($_GET['report']) && is_string($_GET['report']))
                    ? htmlentities(strip_tags(stripslashes($_GET['report'])),
                            ENT_QUOTES, 'ISO-8859-1') : '';
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    $preport_csrf = request_csrf_code('preport_send');
    echo "
	Know of a player that's breaking the rules?
	Don't hesitate to report them.
	Reports are kept confidential.
	<br />
	<form action='preport.php' method='post'>
		<input type='hidden' name='verf' value='{$preport_csrf}' />
    	Player's ID: <input type='text' name='player' value='{$_GET['ID']}' /><br />
    	What they've done: <br />
    	<textarea rows='7' cols='40' name='report'>{$_GET['report']}</textarea><br />
    	<input type='submit' value='Send Report' />
	</form>
   ";
}

$h->endpage();
