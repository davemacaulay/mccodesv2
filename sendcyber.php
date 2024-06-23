<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $userid, $h, $set;
require_once('globals.php');
if (!$set['sendbank_on'])
{
    die('Sorry, the game owner has disabled this feature.');
}
if (!isset($_GET['ID']))
{
    $_GET['ID'] = 0;
}
if (!isset($_POST['xfer']))
{
    $_POST['xfer'] = 0;
}
$_GET['ID'] = abs((int) $_GET['ID']);
$_POST['xfer'] = abs((int) $_POST['xfer']);
if (!((int) $_GET['ID']))
{
    echo 'Invalid User ID';
}
elseif ($_GET['ID'] == $userid)
{
    echo 'Haha, what does sending money to yourself do anyway?';
}
else
{
    $it =
            $db->query(
                    "SELECT `cybermoney`, `lastip`, `username`
                     FROM `users`
                     WHERE `userid` = {$_GET['ID']}");
    if ($db->num_rows($it) == 0)
    {
        $db->free_result($it);
        echo "That user doesn't exist.";
        $h->endpage();
        exit;
    }
    $er = $db->fetch_row($it);
    $db->free_result($it);
    if ($er['cybermoney'] == -1 || $ir['cybermoney'] == -1)
    {
        die(
        'Sorry,you or the person you are sending to does not have a cyber bank account.');
    }
    if ((int) $_POST['xfer'])
    {
        if (!isset($_POST['verf'])
                || !verify_csrf_code("sendcyber_{$_GET['ID']}",
                        stripslashes($_POST['verf'])))
        {
            echo '<h3>Error</h3><hr />
    		This transaction has been blocked for your security.<br />
    		Please send money quickly after you open the form - do not leave it open in tabs.<br />
    		&gt; <a href="sendcyber.php?ID=' . $_GET['ID'] . '">Try Again</a>';
            $h->endpage();
            exit;
        }
        elseif ($_POST['xfer'] > $ir['cybermoney'])
        {
            echo 'Not enough money to send.';
        }
        else
        {
            $db->query(
                    "UPDATE `users`
                     SET `cybermoney` = `cybermoney` - {$_POST['xfer']}
                     WHERE `userid` = $userid");
            $db->query(
                    "UPDATE `users`
                     SET `cybermoney` = `cybermoney` + {$_POST['xfer']}
                     WHERE `userid` = {$_GET['ID']}");
            echo 'You CyberBank Transferred '
                    . money_formatter($_POST['xfer'])
                    . " to {$er['username']} (ID {$_GET['ID']}).";
            event_add($_GET['ID'],
                'You received ' . money_formatter($_POST['xfer'])
                . " into your cyber bank account from {$ir['username']}.");

            $db->query(
                    "INSERT INTO `bankxferlogs`
                     VALUES (NULL, $userid, {$_GET['ID']},
                     {$_POST['xfer']}, " . time()
                            . ", '{$ir['lastip']}',
                     '{$er['lastip']}', 'cyber')");
        }
    }
    else
    {
        $code = request_csrf_code("sendcyber_{$_GET['ID']}");
        echo "<h3>CyberBank Xfer</h3>
		You are sending cyber bank money to <b>{$er['username']}</b> (ID {$_GET['ID']}).
		<br />You have <b>" . money_formatter($ir['cybermoney'])
                . "</b> you can send.
		<form action='sendcyber.php?ID={$_GET['ID']}' method='post'>
			Money: <input type='text' name='xfer' /><br />
			<input type='hidden' name='verf' value='{$code}' />
			<input type='submit' value='Send' />
		</form>";
    }
}
$h->endpage();
