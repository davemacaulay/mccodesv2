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
if (!$set['sendcrys_on'])
{
    die('Sorry, the game owner has disabled this feature.');
}
if (!isset($_GET['ID']))
{
    $_GET['ID'] = 0;
}
if (!isset($_POST['crystals']))
{
    $_POST['crystals'] = 0;
}
$_GET['ID'] = abs((int) $_GET['ID']);
$_POST['crystals'] = abs((int) $_POST['crystals']);
if (!((int) $_GET['ID']))
{
    echo 'Invalid User ID';
}
elseif ($_GET['ID'] == $userid)
{
    echo 'Haha, what does sending crystals to yourself do anyway?';
}
else
{
    $it =
            $db->query(
                    "SELECT `lastip`, `username`
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
    if ((int) $_POST['crystals'])
    {
        if (!isset($_POST['verf'])
                || !verify_csrf_code("sendcrys_{$_GET['ID']}",
                        stripslashes($_POST['verf'])))
        {
            echo '<h3>Error</h3><hr />
    		This transaction has been blocked for your security.<br />
    		Please send money quickly after you open the form - do not leave it open in tabs.<br />
    		&gt; <a href="sendcrys.php?ID=' . $_GET['ID'] . '">Try Again</a>';
            $h->endpage();
            exit;
        }
        elseif ($_POST['crystals'] > $ir['crystals'])
        {
            echo 'Not enough crystals to send.';
        }
        else
        {
            $db->query(
                    "UPDATE `users`
                     SET `crystals` = `crystals` - {$_POST['crystals']}
                     WHERE `userid` = $userid");
            $db->query(
                    "UPDATE `users`
                     SET `crystals` = `crystals` + {$_POST['crystals']}
                     WHERE `userid` = {$_GET['ID']}");
            echo "You sent {$_POST['crystals']} crystals to {$er['username']} (ID {$_GET['ID']}).";
            event_add($_GET['ID'],
                "You received {$_POST['crystals']} crystals from {$ir['username']}.");
            $db->query(
                    "INSERT INTO `crystalxferlogs`
                     VALUES (NULL, $userid, {$_GET['ID']},
                     {$_POST['crystals']}, " . time()
                            . ", '{$ir['lastip']}', '{$er['lastip']}')");
        }
    }
    else
    {
        $code = request_csrf_code("sendcrys_{$_GET['ID']}");
        echo "<h3> Sending Crystals</h3>
		You are sending crystals to <b>{$er['username']}</b> (ID {$_GET['ID']}).
		<br />You have <b>" . number_format($ir['crystals'])
                . "</b> crystals you can send.
		<form action='sendcrys.php?ID={$_GET['ID']}' method='post'>
			Crystals: <input type='text' name='crystals' /><br />
			<input type='hidden' name='verf' value='{$code}' />
			<input type='submit' value='Send' />
		</form>";
        echo "<h3>Latest 5 Transfers</h3>
		<table width='75%' cellspacing='1' class='table'>
			<tr>
				<th>Time</th>
				<th>User From</th>
				<th>User To</th>
				<th>Amount</th>
			</tr>";
        $q =
                $db->query(
                        "SELECT `cxTO`, `cxTIME`, `cxAMOUNT`,
                         `u`.`username` AS `recipient`
                         FROM `crystalxferlogs` AS `cx`
                         INNER JOIN `users` AS `u`
                         ON `cx`.`cxTO` = `u`.`userid`
                         WHERE `cxFROM` = {$userid}
                         ORDER BY `cxTIME` DESC
                         LIMIT 5");
        while ($r = $db->fetch_row($q))
        {
            echo '<tr>
            		<td>' . date('F j, Y, g:i:s a', (int)$r['cxTIME'])
                    . "</td>
                    <td>{$ir['username']} [{$ir['userid']}] </td>
                    <td>{$r['recipient']} [{$r['cxTO']}] </td>
                    <td> " . number_format((int)$r['cxAMOUNT'])
                    . ' crystals</td>
                  </tr>';
        }
        $db->free_result($q);
        echo '</table>';
    }
}
$h->endpage();
