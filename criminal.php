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
 * File: criminal.php
 * Signature: 33e9c157f5a36bc2c507c4a5a43ae989
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

$macropage = 'criminal.php';
global $db, $ir, $h;
require_once('globals.php');
if ($ir['jail'] || $ir['hospital'])
{
    die('This page cannot be accessed while in jail or hospital.');
}
$crimes = [];
$q2 =
        $db->query(
            'SELECT `crimeGROUP`, `crimeNAME`, `crimeBRAVE`, `crimeID`
                         FROM `crimes`
                         ORDER BY `crimeBRAVE` ASC');
while ($r2 = $db->fetch_row($q2))
{
    $crimes[] = $r2;
}
$db->free_result($q2);
$q =
        $db->query(
            'SELECT `cgID`, `cgNAME` FROM `crimegroups` ORDER BY `cgORDER` ASC');
echo "<h3>Criminal Centre</h3><br />
<table width='75%' cellspacing='1' class='table'><tr><th>Crime</th><th>Cost</th><th>Do</th></tr>";
while ($r = $db->fetch_row($q))
{
    echo "<tr><td colspan='3' class='h'>{$r['cgNAME']}</td></tr>";
    foreach ($crimes as $v)
    {
        if ($v['crimeGROUP'] == $r['cgID'])
        {
            echo "<tr><td>{$v['crimeNAME']}</td><td>{$v['crimeBRAVE']} Brave</td><td><a href='docrime.php?c={$v['crimeID']}'>Do</a></td></tr>";
        }
    }
}
$db->free_result($q);
echo '</table>';
$h->endpage();
