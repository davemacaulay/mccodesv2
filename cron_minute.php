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
 * File: cron_minute.php
 * Signature: ed4ad301835b277d396a036d67ba6a39
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $_CONFIG;
require_once('globals_nonauth.php');
if ($argc == 2)
{
    if ($argv[1] != $_CONFIG['code'])
    {
        exit;
    }
}
elseif (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
$db->query(
    'UPDATE `users` SET `hospital` = GREATEST(`hospital` - 1, 0), `jail` = GREATEST(`jail` - 1, 0)');
$counts =
        $db->fetch_row(
                $db->query(
                    'SELECT SUM(IF(`hospital` > 0, 1, 0)) AS `hc`, SUM(IF(`jail` > 0, 1, 0)) AS `jc` FROM `users`'));
$db->query(
        "UPDATE `settings` SET `conf_value` = '{$counts['hc']}' WHERE `conf_name` = 'hospital_count'");
$db->query(
        "UPDATE `settings` SET `conf_value` = '{$counts['jc']}' WHERE `conf_name` = 'jail_count'");
