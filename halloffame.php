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
$non_don = '';
$is_don = '';
$all_us = '';
$filters =
        ['nodon' => 'AND `donatordays` = 0',
                'don' => 'AND `donatordays` > 0', 'all' => ''];
$hofheads =
        ['level', 'money', 'crystals', 'respect', 'total', 'strength',
                'agility', 'guard', 'labour', 'iq'];
$_GET['action'] =
        (isset($_GET['action']) && in_array($_GET['action'], $hofheads))
                ? $_GET['action'] : 'level';
$filter =
        (isset($_GET['filter']) && isset($filters[$_GET['filter']]))
                ? $_GET['filter'] : 'all';
$myf = $filters[$filter];
$hofqone = ['level', 'money', 'crystals'];
if (in_array($_GET['action'], $hofqone))
{
    $q =
            $db->query(
                    "SELECT `userid`, `laston`, `gender`, `donatordays`,
                     `username`, `level`, `money`, `crystals`, `gangPREF`
                     FROM `users` AS `u`
                     LEFT JOIN `gangs` AS `g`
                     ON `g`.`gangID` = `u`.`gang`
                     WHERE `u`.`user_level` != 0
                     $myf
                     ORDER BY `{$_GET['action']}` DESC, `userid` ASC
                     LIMIT 20");
}
$hofqtwo = ['total', 'strength', 'agility', 'guard', 'labour', 'iq'];
if (in_array($_GET['action'], $hofqtwo))
{
    if ($_GET['action'] == 'total')
    {
        $us = '(`strength` + `agility` + `guard` + `labour` + `IQ`)';
    }
    else
    {
        $us = '`' . $_GET['action'] . '`';
    }
    $q =
            $db->query(
                    "SELECT u.`userid`, `laston`, `gender`, `donatordays`,
                     `level`, `money`, `crystals`, `username`, `gangPREF`,
                     `strength`, `agility`, `guard`, `labour`, `IQ`
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid` = `us`.`userid`
                     LEFT JOIN `gangs` AS `g`
                     ON `g`.`gangID` = `u`.`gang`
                     WHERE `u`.`user_level` != 0
                     $myf
                     ORDER BY {$us} DESC, `u`.`userid` ASC
                     LIMIT 20");
}
if ($_GET['action'] != 'respect')
{
    $non_don =
            (($filter == 'nodon') ? '<b>' : '')
                    . '<a href="halloffame.php?action=' . $_GET['action']
                    . '&filter=nodon">Non-Donators</a>'
                    . (($filter == 'nodon') ? '</b>' : '');
    $is_don =
            (($filter == 'don') ? '<b>' : '')
                    . '<a href="halloffame.php?action=' . $_GET['action']
                    . '&filter=don">Donators</a>'
                    . (($filter == 'don') ? '</b>' : '');
    $all_us =
            (($filter == 'all') ? '<b>' : '')
                    . '<a href="halloffame.php?action=' . $_GET['action']
                    . '&filter=all">All Users</a>'
                    . (($filter == 'all') ? '</b>' : '');
}
echo '
<h3>Hall Of Fame</h3>
'
        . (($_GET['action'] != 'respect')
                ? '<hr />Filter: [' . $non_don . ' | ' . $is_don . ' | '
                        . $all_us . ']<hr />' : '')
        . "

<table width='75%' cellpadding='1' cellspacing='1' class='table'>
		<tr>
	<td><a href='halloffame.php?action=level&filter={$filter}'>LEVEL</a></td>
	<td><a href='halloffame.php?action=money&filter={$filter}'>MONEY</a></td>
	<td><a href='halloffame.php?action=crystals&filter={$filter}'>CRYSTALS</a></td>
	<td><a href='halloffame.php?action=respect&filter={$filter}'>RESPECT</a></td>
	<td><a href='halloffame.php?action=total&filter={$filter}'>TOTAL STATS</a></td>
		</tr>
		<tr>
	<td><a href='halloffame.php?action=strength&filter={$filter}'>STRENGTH</a></td>
	<td><a href='halloffame.php?action=agility&filter={$filter}'>AGILITY</a></td>
	<td><a href='halloffame.php?action=guard&filter={$filter}'>GUARD</a></td>
	<td><a href='halloffame.php?action=labour&filter={$filter}'>LABOUR</a></td>
	<td><a href='halloffame.php?action=iq&filter={$filter}'>IQ</a></td>
		</tr>
</table>
   ";
switch ($_GET['action'])
{
case 'level':
    hof_level();
    break;
case 'money':
    hof_money();
    break;
case 'crystals':
    hof_crystals();
    break;
case 'respect':
    hof_respect();
    break;
case 'total':
    hof_total();
    break;
case 'strength':
    hof_strength();
    break;
case 'agility':
    hof_agility();
    break;
case 'guard':
    hof_guard();
    break;
case 'labour':
    hof_labour();
    break;
case 'iq':
    hof_iq();
    break;
}

/**
 * @return void
 */
function hof_level(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest levels
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
			<th>Level</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
	<td>' . $r['level'] . '</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_money(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest amount of money
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
			<th>Money</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
	<td>' . money_formatter((int)$r['money']) . '</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_crystals(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest amount of crystals
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
			<th>Crystals</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
	<td>' . money_formatter((int)$r['crystals'], '') . '</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_respect(): void
{
    global $db, $ir;
    echo "
Showing the 20 gangs with the highest amount of respect
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>Gang</th>
			<th>Respect</th>
		</tr>
   ";
    $q =
            $db->query(
                'SELECT `gangID`, `gangNAME`, `gangRESPECT`
                     FROM `gangs`
                     ORDER BY `gangRESPECT` DESC, `gangID` ASC
                     LIMIT 20');
    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['gangID'] == $ir['gang']) ? ' style="font-weight: bold;"'
                        : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangNAME'] . ' [' . $r['gangID'] . ']</td>
	<td>' . money_formatter((int)$r['gangRESPECT'], '') . '</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_total(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest total stats
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_strength(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest strength
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_agility(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest agility
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_guard(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest guard
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_labour(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest labour
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function hof_iq(): void
{
    global $db, $userid, $q;
    echo "
Showing the 20 users with the highest IQ
<br />
<table width='75%' cellspacing='1' class='table'>
		<tr style='background:gray'>
			<th>Pos</th>
			<th>User</th>
		</tr>
   ";

    $p = 0;
    while ($r = $db->fetch_row($q))
    {
        $p++;
        $bold_hof =
                ($r['userid'] == $userid) ? ' style="font-weight: bold;"' : '';
        echo '
		<tr ' . $bold_hof . '>
	<td>' . $p . '</td>
	<td>' . $r['gangPREF'] . ' ' . $r['username'] . ' [' . $r['userid']
                . ']</td>
		</tr>
   ';
    }
    $db->free_result($q);
    echo '</table>';
}
$h->endpage();
