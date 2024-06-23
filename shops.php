<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $h;
require_once('globals.php');
if (!isset($_GET['shop']))
{
    $_GET['shop'] = 0;
}
$_GET['shop'] = abs((int) $_GET['shop']);
if (!$_GET['shop'])
{
    echo 'You begin looking through town and you see a few shops.<br />';
    $q =
            $db->query(
                    "SELECT `shopID`, `shopNAME`, `shopDESCRIPTION`
                     FROM `shops`
                     WHERE `shopLOCATION` = {$ir['location']}");
    echo "<table width='85%' cellspacing='1' class='table'>
    		<tr>
    			<th>Shop</th>
    			<th>Description</th>
    		</tr>";
    while ($r = $db->fetch_row($q))
    {
        echo "<tr>
        		<td>
        			<a href='shops.php?shop={$r['shopID']}'>{$r['shopNAME']}</a>
        		</td>
        		<td>{$r['shopDESCRIPTION']}</td>
        	  </tr>";
    }
    echo '</table>';
    $db->free_result($q);
}
else
{
    $sd =
            $db->query(
                    "SELECT `shopLOCATION`, `shopNAME`
     				 FROM `shops`
     				 WHERE `shopID` = {$_GET['shop']}");
    if ($db->num_rows($sd) > 0)
    {
        $shopdata = $db->fetch_row($sd);
        if ($shopdata['shopLOCATION'] == $ir['location'])
        {
            echo "Browsing items at <b>{$shopdata['shopNAME']}...</b><br />
			<table cellspacing='1' class='table'>
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>Price</th>
					<th>Sell Price</th>
					<th>Buy</th>
				</tr>";
            $qtwo =
                    $db->query(
                            "SELECT `itmtypename`, `itmname`, `itmdesc`,
                             `itmbuyprice`, `itmsellprice`, `sitemID`
                             FROM `shopitems` AS `si`
                             INNER JOIN `items` AS `i`
                             ON `si`.`sitemITEMID` = `i`.`itmid`
                             INNER JOIN `itemtypes` AS `it`
                             ON `i`.`itmtype` = `it`.`itmtypeid`
                             WHERE `si`.`sitemSHOP` = {$_GET['shop']}
                             ORDER BY `itmtype` ASC, `itmbuyprice` ASC,
                             `itmname` ASC");
            $lt = '';
            while ($r = $db->fetch_row($qtwo))
            {
                if ($lt != $r['itmtypename'])
                {
                    $lt = $r['itmtypename'];
                    echo "\n<tr>
                    			<th colspan='5'>{$lt}</th>
                    		</tr>";
                }
                echo "\n<tr>
                			<td>{$r['itmname']}</td>
                			<td>{$r['itmdesc']}</td>
                			<td>" . money_formatter($r['itmbuyprice'])
                        . '</td>
                            <td>' . money_formatter($r['itmsellprice'])
                        . "</td>
                            <td>
                            	<form action='itembuy.php?ID={$r['sitemID']}' method='post'>
                            		Qty: <input type='text' name='qty' value='1' />
                            		<input type='submit' value='Buy' />
                            	</form>
                            </td>
                        </tr>";
            }
            $db->free_result($qtwo);
            echo '</table>';
        }
        else
        {
            echo 'You are trying to access a shop in another city!';
        }
    }
    else
    {
        echo 'You are trying to access an invalid shop!';
    }
    $db->free_result($sd);
}
$h->endpage();
