<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $h;
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
            echo money_formatter((int)$id['itmbuyprice']);
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
            echo money_formatter((int)$id['itmsellprice'])
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
