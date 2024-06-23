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
if (!isset($_GET['ID']))
{
    $_GET['ID'] = 0;
}
if (!isset($_POST['money']))
{
    $_POST['money'] = 0;
}
$_GET['ID'] = abs((int) $_GET['ID']);
$_POST['money'] = abs((int) $_POST['money']);
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
                    "SELECT `lastip`, `username` FROM `users` WHERE `userid` = {$_GET['ID']}");
    if ($db->num_rows($it) == 0)
    {
        $db->free_result($it);
        echo "That user doesn't exist.";
        $h->endpage();
        exit;
    }
    $er = $db->fetch_row($it);
    $db->free_result($it);
    if ((int) $_POST['money'])
    {
        if (!isset($_POST['verf'])
                || !verify_csrf_code("sendcash_{$_GET['ID']}",
                        stripslashes($_POST['verf'])))
        {
            echo '<h3>Error</h3><hr />
    		This transaction has been blocked for your security.<br />
    		Please send money quickly after you open the form - do not leave it open in tabs.<br />
    		&gt; <a href="sendcash.php?ID=' . $_GET['ID'] . '">Try Again</a>';
            $h->endpage();
            exit;
        }
        elseif ($_POST['money'] > $ir['money'])
        {
            echo 'Not enough money to send.';
        }
        else
        {
            $db->query(
                    "UPDATE `users`
                     SET `money` = `money` - {$_POST['money']}
                     WHERE `userid` = $userid");
            $db->query(
                    "UPDATE `users`
                     SET `money` = `money` + {$_POST['money']}
                     WHERE `userid` = {$_GET['ID']}");
            echo 'You sent ' . money_formatter($_POST['money'])
                    . " to {$er['username']} (ID {$_GET['ID']}).";
            event_add($_GET['ID'],
                'You received ' . money_formatter($_POST['money'])
                . " from {$ir['username']}.");
            $db->query(
                    "INSERT INTO `cashxferlogs`
                    VALUES (NULL, $userid, {$_GET['ID']}, {$_POST['money']},
                     " . time() . ", '{$ir['lastip']}', '{$er['lastip']}')");
        }
    }
    else
    {
        $code = request_csrf_code("sendcash_{$_GET['ID']}");
        echo "<h3> Sending Money</h3>
		You are sending money to <b>{$er['username']}</b> (ID {$_GET['ID']}).
		<br />You have <b>" . money_formatter($ir['money'])
                . "</b> you can send.
        <form action='sendcash.php?ID={$_GET['ID']}' method='post'>
        		Amnt: <input type='text' name='money' /><br />
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
                         FROM `cashxferlogs` AS `cx`
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
                    <td> " . money_formatter((int)$r['cxAMOUNT'])
                    . '</td>
                  </tr>';
        }
        $db->free_result($q);
        echo '</table>';
    }
}
$h->endpage();
