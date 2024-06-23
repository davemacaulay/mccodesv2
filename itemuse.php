<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
if (!$_GET['ID'])
{
    echo 'Invalid use of file';
}
else
{
    $i =
            $db->query(
                    "SELECT `effect1`, `effect2`, `effect3`,
                     `effect1_on`, `effect2_on`, `effect3_on`,
                     `itmname`, `inv_itemid`
                     FROM `inventory` AS `iv`
                     INNER JOIN `items` AS `i`
                     ON `iv`.`inv_itemid` = `i`.`itmid`
                     WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND `iv`.`inv_userid` = $userid");
    if ($db->num_rows($i) == 0)
    {
        $db->free_result($i);
        echo 'Invalid item ID';
    }
    else
    {
        $r = $db->fetch_row($i);
        $db->free_result($i);
        if (!$r['effect1_on'] && !$r['effect2_on'] && !$r['effect3_on'])
        {
            echo 'Sorry, this item cannot be used as it has no effect.';
            $h->endpage();
            exit;
        }
        for ($enum = 1; $enum <= 3; $enum++)
        {
            if ($r["effect{$enum}_on"])
            {
                $einfo = unserialize($r["effect{$enum}"]);
                if ($einfo['inc_type'] == 'percent')
                {
                    if (in_array($einfo['stat'],
                            ['energy', 'will', 'brave', 'hp']))
                    {
                        $inc =
                                round(
                                        $ir['max' . $einfo['stat']] / 100
                                                * $einfo['inc_amount']);
                    }
                    else
                    {
                        $inc =
                                round(
                                        $ir[$einfo['stat']] / 100
                                                * $einfo['inc_amount']);
                    }
                }
                else
                {
                    $inc = $einfo['inc_amount'];
                }
                if ($einfo['dir'] == 'pos')
                {
                    if (in_array($einfo['stat'],
                            ['energy', 'will', 'brave', 'hp']))
                    {
                        $ir[$einfo['stat']] =
                                min($ir[$einfo['stat']] + $inc,
                                        $ir['max' . $einfo['stat']]);
                    }
                    else
                    {
                        $ir[$einfo['stat']] += $inc;
                    }
                }
                else
                {
                    $ir[$einfo['stat']] = max($ir[$einfo['stat']] - $inc, 0);
                }
                $upd = $ir[$einfo['stat']];
                if (in_array($einfo['stat'],
                        ['strength', 'agility', 'guard', 'labour', 'IQ']))
                {
                    $db->query(
                            "UPDATE `userstats`
                             SET `{$einfo['stat']}` = '{$upd}'
                             WHERE `userid` = {$userid}");
                }
                else
                {
                    $db->query(
                            "UPDATE `users`
                             SET `{$einfo['stat']}` = '{$upd}'
                             WHERE `userid` = {$userid}");
                }
            }
        }
        echo $r['itmname'] . ' used successfully!';
        item_remove($userid, (int)$r['inv_itemid'], 1);
    }
}
$h->endpage();
