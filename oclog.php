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
 * File: oclog.php
 * Signature: 76ded8b56b6ddfb5c2de0e01f6f42a24
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $h;
require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : 0;
if (empty($_GET['ID']))
{
    echo 'Invalid command.<br />
    &gt; <a href="index.php">Go Home</a>';
    die($h->endpage());
}
$q =
        $db->query(
                'SELECT `ocCRIMEN`, `ocTIME`, `oclLOG`, `oclRESULT`, `oclMONEY`
                 FROM `oclogs`
                 WHERE `oclID` = ' . $_GET['ID']);
if ($db->num_rows($q) == 0)
{
    $db->free_result($q);
    echo 'Invalid OC.<br />
    &gt; <a href="index.php">Go Home</a>';
    die($h->endpage());
}
$r = $db->fetch_row($q);
$db->free_result($q);
echo "
Here is the detailed view on this crime.
<br />
<b>Crime:</b> {$r['ocCRIMEN']}
<br />
<b>Time Executed:</b> " . date('F j, Y, g:i:s a', $r['ocTIME'])
        . "
<br />
        {$r['oclLOG']}
<br />
<br />
<b>Result:</b> {$r['oclRESULT']}
<br />
<b>Money Made:</b> " . money_formatter($r['oclMONEY']) . '
   ';
$h->endpage();
