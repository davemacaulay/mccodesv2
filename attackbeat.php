<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

$atkpage = 1;
global $db, $ir, $userid, $h;
require_once('globals.php');

$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs((int) $_GET['ID']) : 0;
$_SESSION['attacking'] = 0;
$ir['attacking'] = 0;
$db->query("UPDATE `users` SET `attacking` = 0 WHERE `userid` = $userid");
$od =
        $db->query(
                "SELECT * FROM `users` WHERE `userid` = {$_GET['ID']} LIMIT 1");
if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
{
    die("Cheaters don't get anywhere.");
}
if ($db->num_rows($od) > 0)
{
    $r = $db->fetch_row($od);
    $db->free_result($od);
    if ($r['hp'] == 1)
    {
        echo 'What a cheater you are.';
    }
    else
    {
        echo "You beat {$r['username']}!!<br />
You beat {$r['username']} severely on the ground. When there is lots of blood showing, you head up to the nearest 10-story building's roof and drop him over the edge. You run home silently and carefully.";
        $hosptime = rand(50, 150) + floor($ir['level'] / 2);
        $hospreason =
                $db->escape(
                        "Hospitalized by <a href='viewuser.php?u={$userid}'>{$ir['username']}</a>");
        $db->query(
                "UPDATE `users` SET `hp` = 1, `hospital` = $hosptime,
                        `hospreason` = '{$hospreason}'
                        WHERE `userid` = {$r['userid']}");
        event_add($r['userid'],
            "<a href='viewuser.php?u=$userid'>{$ir['username']}</a> beat you up.");
        $atklog = $db->escape($_SESSION['attacklog']);
        $db->query(
                "INSERT INTO `attacklogs` VALUES(NULL, $userid, {$_GET['ID']},
                        'won', " . time() . ", -1, '$atklog')");
        $_SESSION['attackwon'] = 0;
        if ($ir['gang'] > 0 && $r['gang'] > 0)
        {
            $gq =
                    $db->query(
                            "SELECT `gangRESPECT`, `gangID` FROM `gangs` WHERE `gangID` = {$r['gang']} LIMIT 1");
            if ($db->num_rows($gq) > 0)
            {
                $ga = $db->fetch_row($gq);
                $warq =
                        $db->query(
                                "SELECT COUNT(`warDECLARER`) FROM `gangwars`
                                    WHERE (`warDECLARER` = {$ir['gang']} AND `warDECLARED` = {$r['gang']})
                                    OR (`warDECLARED` = {$ir['gang']} AND `warDECLARER` = {$r['gang']})");
                if ($db->fetch_single($warq) > 0)
                {
                    $db->query(
                            "UPDATE `gangs` SET `gangRESPECT` = `gangRESPECT` - 3 WHERE `gangID` = {$r['gang']}");
                    $ga['gangRESPECT'] -= 3;
                    $db->query(
                            "UPDATE `gangs` SET `gangRESPECT` = `gangRESPECT` + 3 WHERE `gangID` = {$ir['gang']}");
                    echo '<br />You earnt 3 respect for your gang!';

                }
                // Gang Kill
                if ($ga['gangRESPECT'] <= 0 && $r['gang'])
                {
                    $db->query(
                            "UPDATE `users` SET `gang` = 0 WHERE `gang` = {$r['gang']}");

                    $db->query('DELETE FROM `gangs` WHERE `gangRESPECT` <= 0');
                    $db->query(
                            "DELETE FROM `gangwars`
                                WHERE `warDECLARER` = {$ga['gangID']} OR `warDECLARED` = {$ga['gangID']}");
                }
            }
            $db->free_result($gq);
        }
        if ($r['user_level'] == 0)
        {
            $q =
                    $db->query(
                            "SELECT `cb_money` FROM `challengebots` WHERE `cb_npcid` = {$r['userid']}");
            if ($db->num_rows($q) > 0)
            {
                $cb = $db->fetch_row($q);
                $qk =
                        $db->query(
                                "SELECT COUNT(`npcid`) FROM `challengesbeaten`
                                        WHERE `userid` = $userid AND `npcid` = {$r['userid']}");
                if ($db->fetch_single($qk) > 0)
                {
                    $m = (int)$cb['cb_money'];
                    $db->query(
                            "UPDATE `users` SET `money` = `money` + $m WHERE `userid` = $userid");
                    echo '<br /> You gained ' . money_formatter($m)
                            . " for beating the challenge bot {$r['username']}";
                    $db->query(
                            "INSERT INTO `challengesbeaten` VALUES($userid, {$r['userid']})");
                }
                $db->free_result($qk);
            }
            $db->free_result($q);
        }

    }
}
else
{
    $db->free_result($od);
    echo 'You beat Mr. non-existent!';
}

$h->endpage();
