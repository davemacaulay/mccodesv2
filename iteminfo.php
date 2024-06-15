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
 * File: iteminfo.php
 * Signature: a022cee84c0995e9600dcb86e30babd3
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
$itmid = $_GET['ID'];
if (!$itmid)
{
    echo 'Invalid item ID';
}
else
{
    $q =
            $db->query(
                    "SELECT `itmname`, `itmdesc`, `itmbuyprice`,
                     `itmsellprice`, `itmtypename`
                     FROM `items` AS `i`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     WHERE `i`.`itmid` = {$itmid}
                     LIMIT 1");
    if ($db->num_rows($q) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $id = $db->fetch_row($q);
        echo "
<table width=75% class='table' cellspacing='1'>
		<tr style='background: gray;'>
	<th colspan=2><b>Looking up info on {$id['itmname']}</b></th>
		</tr>
		<tr style='background: #dfdfdf;'>
	<td colspan=2>The <b>{$id['itmname']}</b> is a/an {$id['itmtypename']} Item - <b>{$id['itmdesc']}</b></th>
		</tr>
	<tr style='background: gray;'>
	<th colspan=2>Item Info</th>
		</tr>
		<tr style='background:gray'>
	<th>Item Buy Price</th>
	<th>Item Sell Price</th>
		</tr>
		<tr>
	<td>
   ";
        if ($id['itmbuyprice'])
        {
            echo money_formatter($id['itmbuyprice']);
        }
        else
        {
            echo 'N/A';
        }
        echo '
	</td>
	<td>
   ';
        if ($id['itmsellprice'])
        {
            echo money_formatter($id['itmsellprice'])
                    . '
	</td>
		</tr>
</table>
   ';
        }
        else
        {
            echo '
N/A</td>
		</tr>
</table>
   ';
        }
    }
    $db->free_result($q);
}
$h->endpage();
