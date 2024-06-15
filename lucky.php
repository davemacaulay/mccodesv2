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
 * File: lucky.php
 * Signature: ba86ff196cea4deda7a64cf2b84ed678
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
echo "<h3>Lucky Boxes</h3><hr />";
$box_cost = 1000;
$bc_format = money_formatter($box_cost);
if (isset($_GET['open']) && $_GET['open'])
{
    if ($ir['boxes_opened'] >= 5)
    {
        die(
                "Sorry, you have already opened 5 boxes today. Come back tomorrow.");
    }
    if ($ir['money'] < $box_cost)
    {
        die(
                "Sorry, it costs {$bc_format} to open a box. Come back when you have enough.");
    }
    $num = rand(1, 5);
    $db->query(
            "UPDATE `users`
             SET `boxes_opened` = `boxes_opened` + 1,
             `money` = `money` - {$box_cost}
             WHERE `userid` = $userid");
    $ir['money'] -= 1000;
    switch ($num)
    {
    case 1:
        $tokens = rand(1, 5);
        echo "First outcome here (gained {$tokens} crystals)";
        $db->query(
                "UPDATE `users`
                 SET `crystals` = `crystals` + {$tokens}
                 WHERE `userid` = {$userid}");
        break;
    case 2:
        $money = rand(330, 3300);
        echo "Second outcome here (gained " . money_formatter($money) . ")";
        $db->query(
                "UPDATE `users`
                 SET `money` = `money` + {$money}
                 WHERE `userid` = {$userid}");
        break;
    case 3:
        $stole = min(rand($ir['money'] / 10, $ir['money'] / 5), 5000);
        echo "Third outcome here (lost " . money_formatter($stole) . ")";
        $db->query(
                "UPDATE `users`
                 SET `money` = `money` - {$stole}
                 WHERE `userid` = {$userid}");
        break;
    case 4:
        echo "Fourth outcome here (nothing)";
        break;
    case 5:
        echo "Fifth outcome here (nothing)";
        break;
    }
    echo "<hr />
	<a href='lucky.php?open=1'>Open Another</a><br />
	<a href='explore.php'>Back to Town</a>";
}
else
{
    echo "A man comes up to you and whispers, \"I have magical boxes, I let you open one for {$bc_format}. You can open a maximum of 5 a day. Deal or no deal?<hr />
	<a href='lucky.php?open=1'>Okay, open one.</a><br />
	<a href='explore.php'>No thanks.</a>";
}
$h->endpage();
