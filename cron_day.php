<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */
global $db, $_CONFIG;
require_once('globals_nonauth.php');
if (isset($argc) && $argc == 2)
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
$db->query('UPDATE `fedjail` SET `fed_days` = `fed_days` - 1');
$q = $db->query('SELECT * FROM `fedjail` WHERE `fed_days` <= 0');
$ids = [];
while ($r = $db->fetch_row($q))
{
    $ids[] = $r['fed_userid'];
}
$db->free_result($q);
if (count($ids) > 0)
{
    $db->query(
            'UPDATE `users` SET `fedjail` = 0 WHERE `userid` IN('
                    . implode(',', $ids) . ')');
}
$db->query('DELETE FROM `fedjail` WHERE `fed_days` <= 0');
$user_update_query =
    'UPDATE `users` SET 
         `daysingang` = `daysingang` + IF(`gang` > 0, 1, 0),
         `daysold` = `daysold` + 1, `boxes_opened` = 0,
         `mailban` = `mailban` - IF(`mailban` > 0, 1, 0),
         `donatordays` = `donatordays` - IF(`donatordays` > 0, 1, 0),
         `cdays` = `cdays` - IF(`course` > 0, 1, 0),
         `bankmoney` = `bankmoney` + IF(`bankmoney` > 0, `bankmoney` / 50, 0),
         `cybermoney` = `cybermoney` + IF(`cybermoney` > 0, `cybermoney` / 100 * 7, 0)';
$db->query($user_update_query);
$q =
        $db->query(
            'SELECT `userid`, `course` FROM `users` WHERE `cdays` <= 0 AND `course` > 0');
$course_cache = [];
while ($r = $db->fetch_row($q))
{
    if (!array_key_exists($r['course'], $course_cache))
    {
        $cd =
                $db->query(
                        "SELECT `crSTR`, `crGUARD`, `crLABOUR`, `crAGIL`, `crIQ`, `crNAME`
     				     FROM `courses`
                         WHERE `crID` = {$r['course']}");
        $coud = $db->fetch_row($cd);
        $db->free_result($cd);
        $course_cache[$r['course']] = $coud;
    }
    else
    {
        $coud = $course_cache[$r['course']];
    }
    $userid = $r['userid'];
    $db->query(
            "INSERT INTO `coursesdone` VALUES({$r['userid']}, {$r['course']})");
    $upd = '';
    $ev = '';
    if ($coud['crSTR'] > 0)
    {
        $upd .= ", us.strength = us.strength + {$coud['crSTR']}";
        $ev .= ", {$coud['crSTR']} strength";
    }
    if ($coud['crGUARD'] > 0)
    {
        $upd .= ", us.guard = us.guard + {$coud['crGUARD']}";
        $ev .= ", {$coud['crGUARD']} guard";
    }
    if ($coud['crLABOUR'] > 0)
    {
        $upd .= ", us.labour = us.labour + {$coud['crLABOUR']}";
        $ev .= ", {$coud['crLABOUR']} labour";
    }
    if ($coud['crAGIL'] > 0)
    {
        $upd .= ", us.agility = us.agility + {$coud['crAGIL']}";
        $ev .= ", {$coud['crAGIL']} agility";
    }
    if ($coud['crIQ'] > 0)
    {
        $upd .= ", us.IQ = us.IQ + {$coud['crIQ']}";
        $ev .= ", {$coud['crIQ']} IQ";
    }
    $ev = substr($ev, 1);
    $db->query(
            "UPDATE `users` AS `u`
                INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
                SET `u`.`course` = 0{$upd}
                WHERE `u`.`userid` = {$userid}");
    event_add($userid,
        "Congratulations, you completed the {$coud['crNAME']} and gained {$ev}!");
}
$db->free_result($q);
$db->query('TRUNCATE TABLE `votes`');
