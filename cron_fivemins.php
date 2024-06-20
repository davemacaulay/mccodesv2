<?php
declare(strict_types=1);
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
 * File: cron_fivemins.php
 * Signature: 79887e7ca14a99487ded5a18e0b27e89
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */
global $db, $set, $_CONFIG;
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
// do we need to reset verification?
$ver_reset = false;
if ($set['validate_period'] == 5 && $set['validate_on'])
{
    $ver_reset = true;
}
if ($set['validate_period'] == 15 && $set['validate_on']
        && in_array(date('i'), ['00', '15', '30', '45']))
{
    $ver_reset = true;
}
// update for all users
$allusers_query =
        'UPDATE `users`
        SET `brave` = LEAST(`brave` + ((`maxbrave` / 10) + 0.5), `maxbrave`),
        `hp` = LEAST(`hp` + (`maxhp` / 3), `maxhp`),
        `will` = LEAST(`will` + 10, `maxwill`),
        `energy` = IF(`donatordays` > 0,
                   LEAST(`energy` + (`maxenergy` / 6), `maxenergy`),
                   LEAST(`energy` + (`maxenergy` / 12.5), `maxenergy`))'
                . ($ver_reset ? ', `verified` = 0' : '');
$db->query($allusers_query);
