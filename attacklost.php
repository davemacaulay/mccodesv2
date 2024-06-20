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
 * File: attacklost.php
 * Signature: 95b6a74ae7c7a6cf4e9ed998f3b574c8
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

$atkpage = 1;
require_once('globals.php');

$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs((int) $_GET['ID']) : 0;
$_SESSION['attacking'] = 0;
$_SESSION['attacklost'] = 0;
$od =
        $db->query(
                "SELECT `username`, `level`, `gang` FROM `users` WHERE `userid` = {$_GET['ID']}");
if ($db->num_rows($od) > 0)
{
    $r = $db->fetch_row($od);
    $db->free_result($od);
    echo "You lost to {$r['username']}";
    $expgain = abs(($ir['level'] - $r['level']) ^ 3);
    $expgainp = $expgain / $ir['exp_needed'] * 100;
    echo " and lost $expgainp% EXP!";
    // Figure out their EXP, 0 or decreased?
    $newexp = max($ir['exp'] - $expgain, 0);
    $db->query(
            "UPDATE `users` SET `exp` = {$newexp}, `attacking` = 0 WHERE `userid` = $userid");
    event_add($r['userid'],
        "<a href='viewuser.php?u=$userid'>{$ir['username']}</a> attacked you and lost.");
    $atklog = $db->escape($_SESSION['attacklog']);
    $db->query(
            "INSERT INTO `attacklogs` VALUES(NULL, $userid, {$_GET['ID']},
                    'lost', " . time() . ", 0, '$atklog')");
    if ($ir['gang'] > 0 && $r['gang'] > 0)
    {
        $warq =
                $db->query(
                        "SELECT * FROM `gangwars`
                            WHERE (`warDECLARER` = {$ir['gang']} AND `warDECLARED` = {$r['gang']})
                            OR (`warDECLARED` = {$ir['gang']} AND `warDECLARER` = {$r['gang']})");
        if ($db->num_rows($warq) > 0)
        {
            $war = $db->fetch_row($warq);
            $db->query(
                    "UPDATE `gangs` SET `gangRESPECT` = `gangRESPECT` + 1 WHERE `gangID` = {$r['gang']}");
            $db->query(
                    "UPDATE `gangs` SET `gangRESPECT` = `gangRESPECT` - 1 WHERE `gangID` = {$ir['gang']}");
            echo "<br />You lost 1 respect for your gang!";
        }
        $db->free_result($warq);
    }
}
else
{
    $db->free_result($od);
    echo "You lost to Mr. Non-existant! =O";
}
$h->endpage();
