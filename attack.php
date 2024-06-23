<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

$menuhide = 1;
$atkpage = 1;
global $db, $ir, $userid, $h;
require_once('globals.php');

$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
if (!$_GET['ID'])
{
    echo 'Invalid ID<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($_GET['ID'] == $userid)
{
    echo 'you can\'t attack yourself.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($ir['hp'] <= 1)
{
    echo 'You\'re unconcious therefore you can\'t attack.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] == 1)
{
    $_SESSION['attacklost'] = 0;
    echo 'Only the losers of all their EXP attack when they\'ve already lost.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
$youdata = $ir;
$odata_sql =
        <<<SQL
	SELECT `u`.`userid`, `hp`, `hospital`, `jail`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `gang`, `location`, `maxhp`,
	       `guard`, `agility`, `strength`, `gender`
	FROM `users` AS `u`
	INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
	WHERE `u`.`userid` = {$_GET['ID']}
	LIMIT 1
SQL;
$q = $db->query($odata_sql);
if ($db->num_rows($q) == 0)
{
    echo 'That user doesn&#39;t exist<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
$odata = $db->fetch_row($q);
$db->free_result($q);
$myabbr = ($ir['gender'] == 'Male') ? 'his' : 'her';
$oabbr = ($odata['gender'] == 'Male') ? 'his' : 'her';
if ($ir['attacking'] && $ir['attacking'] != $_GET['ID'])
{
    $_SESSION['attacklost'] = 0;
    echo 'Something went wrong.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
$endattk_sql =
        <<<SQL
	UPDATE `users`
	SET `attacking` = 0
	WHERE `userid` = {$userid}
SQL;
if ($odata['hp'] == 1)
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query($endattk_sql);
    echo 'This player is unconscious.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($odata['hospital'])
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query($endattk_sql);
    echo 'This player is in hospital.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($ir['hospital'])
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query($endattk_sql);
    echo 'While in hospital you can\'t attack.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($odata['jail'])
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query($endattk_sql);
    echo 'This player is in jail.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($ir['jail'])
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query($endattk_sql);
    echo 'While in jail you can\'t attack.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
echo '
<table width="100%">
		<tr>
	<td colspan="2" align="center">
   ';
$_GET['wepid'] =
        (isset($_GET['wepid']) && is_numeric($_GET['wepid']))
                ? abs(intval($_GET['wepid'])) : '';
if ($_GET['wepid'])
{
    $_GET['nextstep'] =
            (isset($_GET['nextstep']) && is_numeric($_GET['nextstep']))
                    ? abs(intval($_GET['nextstep'])) : 1;
    if (!$_GET['nextstep'])
    {
        $_GET['nextstep'] = 1;
    }
    if ($_SESSION['attacking'] == 0 && $ir['attacking'] == 0)
    {
        if ($youdata['energy'] >= $youdata['maxenergy'] / 2)
        {
            $youdata['energy'] -= floor($youdata['maxenergy'] / 2);
            $cost = floor($youdata['maxenergy'] / 2);
            $db->query(
                    "UPDATE `users` SET `energy` = `energy` - {$cost} "
                            . "WHERE `userid` = {$userid}");
            $_SESSION['attacklog'] = '';
            $_SESSION['attackdmg'] = 0;
        }
        else
        {
            echo 'You can only attack someone when you have 50% energy.<br />&gt; <a href="index.php">Go Home</a>';
            $h->endpage();
            exit;
        }
    }
    $_SESSION['attacking'] = 1;
    $ir['attacking'] = $odata['userid'];
    $attackstatus_sql =
            <<<SQL
   		UPDATE `users`
    	SET `attacking` = {$ir['attacking']}
    	WHERE `userid` = {$userid}
SQL;
    $db->query($attackstatus_sql);
    if ($_GET['wepid'] != $ir['equip_primary']
            && $_GET['wepid'] != $ir['equip_secondary'])
    {
        $abuse_sql =
                <<<SQL
        	UPDATE `users`
        	SET `exp` = 0
        	WHERE `userid` = {$userid}
SQL;
        $db->query($abuse_sql);
        echo 'Stop trying to abuse a game bug. You can lose all your EXP for that.<br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    $winfo_sql =
            <<<SQL
    	SELECT `itmname`, `weapon`
    	FROM `items`
    	WHERE `itmid` = {$_GET['wepid']}
    	LIMIT 1
SQL;
    $qo = $db->query($winfo_sql);
    if ($db->num_rows($qo) == 0)
    {
        echo 'That weapon doesn&#39;t exist...';
        $h->endpage();
        exit;
    }
    $r1 = $db->fetch_row($qo);
    $db->free_result($qo);
    $mydamage =
            (int) (($r1['weapon'] * $youdata['strength']
                    / ($odata['guard'] / 1.5)) * (rand(8000, 12000) / 10000));
    $hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
    if (rand(1, 100) <= $hitratio)
    {
        if ($odata['equip_armor'] > 0)
        {
            $armorinfo_sql =
                    <<<SQL
            	SELECT `armor`
            	FROM `items`
            	WHERE `itmid` = {$odata['equip_armor']}
            	LIMIT 1
SQL;
            $q3 = $db->query($armorinfo_sql);
            if ($db->num_rows($q3) > 0)
            {
                $mydamage -= $db->fetch_single($q3);
            }
            $db->free_result($q3);
        }
        if ($mydamage < -100000)
        {
            $mydamage = abs($mydamage);
        }
        elseif ($mydamage < 1)
        {
            $mydamage = 1;
        }
        $crit = rand(1, 40);
        if ($crit == 17)
        {
            $mydamage *= rand(20, 40) / 10;
        }
        elseif ($crit == 25 OR $crit == 8)
        {
            $mydamage /= (rand(20, 40) / 10);
        }
        $mydamage = round($mydamage);
        $odata['hp'] -= $mydamage;
        if ($odata['hp'] == 1)
        {
            $odata['hp'] = 0;
            $mydamage += 1;
        }
        $db->query(
                "UPDATE `users` SET `hp` = `hp` - $mydamage WHERE `userid` = {$_GET['ID']}");
        echo "<font color=red>{$_GET['nextstep']}. Using your {$r1['itmname']} you hit {$odata['username']} doing $mydamage damage ({$odata['hp']})</font><br />\n";
        $_SESSION['attackdmg'] += $mydamage;
        $_SESSION['attacklog'] .=
                "<font color=red>{$_GET['nextstep']}. Using {$myabbr} {$r1['itmname']} {$ir['username']} hit {$odata['username']} doing $mydamage damage ({$odata['hp']})</font><br />\n";
    }
    else
    {
        echo "<font color=red>{$_GET['nextstep']}. You tried to hit {$odata['username']} but missed ({$odata['hp']})</font><br />\n";
        $_SESSION['attacklog'] .=
                "<font color=red>{$_GET['nextstep']}. {$ir['username']} tried to hit {$odata['username']} but missed ({$odata['hp']})</font><br />\n";
    }
    if ($odata['hp'] <= 0)
    {
        $odata['hp'] = 0;
        $_SESSION['attackwon'] = $_GET['ID'];
        $db->query(
                "UPDATE `users` SET `hp` = 0 WHERE `userid` = {$_GET['ID']}");
        echo "
<br />
<b>What do you want to do with {$odata['username']} now?</b><br />
<form action='attackwon.php?ID={$_GET['ID']}' method='post'><input type='submit' value='Mug Them' /></form>
<form action='attackbeat.php?ID={$_GET['ID']}' method='post'><input type='submit' value='Hospitalize Them' /></form>
<form action='attacktake.php?ID={$_GET['ID']}' method='post'><input type='submit' value='Leave Them' /></form>
   ";
    }
    else
    {
        $enweps = [];

        $eq =
                $db->query(
                        "SELECT `itmname`,`weapon` FROM  `items` WHERE `itmid` IN({$odata['equip_primary']}, {$odata['equip_secondary']})");
        if ($db->num_rows($eq) == 0)
        {
            $wep = 'Fists';
            $dam =
                    (int) ((((int) ($odata['strength'] / $ir['guard'] / 100))
                            + 1) * (rand(8000, 12000) / 10000));
        }
        else
        {
            $cnt = 0;
            while ($r = $db->fetch_row($eq))
            {
                $enweps[] = $r;
                $cnt++;
            }
            $db->free_result($eq);
            $weptouse = rand(0, $cnt - 1);
            $wep = $enweps[$weptouse]['itmname'];
            $dam =
                    (int) (($enweps[$weptouse]['weapon'] * $odata['strength']
                            / ($youdata['guard'] / 1.5))
                            * (rand(8000, 12000) / 10000));
        }
        $hitratio =
                max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
        if (rand(1, 100) <= $hitratio)
        {
            if ($ir['equip_armor'] > 0)
            {
                $q3 =
                        $db->query(
                                "SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']} LIMIT 1");
                if ($db->num_rows($q3) > 0)
                {
                    $dam -= $db->fetch_single($q3);
                }
                $db->free_result($q3);
            }
            if ($dam < -100000)
            {
                $dam = abs($dam);
            }
            elseif ($dam < 1)
            {
                $dam = 1;
            }
            $crit = rand(1, 40);
            if ($crit == 17)
            {
                $dam *= rand(20, 40) / 10;
            }
            elseif ($crit == 25 OR $crit == 8)
            {
                $dam /= (rand(20, 40) / 10);
            }
            $dam = round($dam);
            $youdata['hp'] -= $dam;
            if ($youdata['hp'] == 1)
            {
                $dam += 1;
                $youdata['hp'] = 0;
            }
            $db->query(
                    "UPDATE `users` SET `hp` = `hp` - $dam WHERE `userid` = $userid");
            $ns = $_GET['nextstep'] + 1;
            echo "<font color=blue>{$ns}. Using $oabbr $wep {$odata['username']} hit you doing $dam damage ({$youdata['hp']})</font><br />\n";
            $_SESSION['attacklog'] .=
                    "<font color=blue>{$ns}. Using $oabbr $wep {$odata['username']} hit {$ir['username']} doing $dam damage ({$youdata['hp']})</font><br />\n";
        }
        else
        {
            $ns = $_GET['nextstep'] + 1;
            echo "<font color=red>{$ns}. {$odata['username']} tried to hit you but missed ({$youdata['hp']})</font><br />\n";
            $_SESSION['attacklog'] .=
                    "<font color=blue>{$ns}. {$odata['username']} tried to hit {$ir['username']} but missed ({$youdata['hp']})</font><br />\n";
        }
        if ($youdata['hp'] <= 0)
        {
            $youdata['hp'] = 0;
            $_SESSION['attacklost'] = 1;
            $db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = $userid");
            echo "<form action='attacklost.php?ID={$_GET['ID']}' method='post'><input type='submit' value='Continue' />";
        }
    }
}
elseif ($odata['hp'] < 5)
{
    echo 'You can only attack those who have health.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($ir['gang'] == $odata['gang'] && $ir['gang'] > 0)
{
    echo 'You are in the same gang as ' . $odata['username']
            . '! What are you smoking today dude!<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($youdata['energy'] < $youdata['maxenergy'] / 2)
{
    echo 'You can only attack someone when you have 50% energy.<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
elseif ($youdata['location'] != $odata['location'])
{
    echo 'You can only attack someone in the same location!<br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
echo '
	</td>
		</tr>
   ';
if ($youdata['hp'] <= 0 OR $odata['hp'] <= 0)
{
    echo '</table>';
}
else
{
    $vars['hpperc'] = round($youdata['hp'] / $youdata['maxhp'] * 100);
    $vars['hpopp'] = 100 - $vars['hpperc'];
    $vars2['hpperc'] = round($odata['hp'] / $odata['maxhp'] * 100);
    $vars2['hpopp'] = 100 - $vars2['hpperc'];
    $mw =
            $db->query(
                    "SELECT `itmid`,`itmname` FROM  `items`  WHERE `itmid` IN({$ir['equip_primary']}, {$ir['equip_secondary']})");
    echo '
		<tr>
	<td colspan="2" align="center">Attack with:<br />
   ';
    if ($db->num_rows($mw) > 0)
    {
        while ($r = $db->fetch_row($mw))
        {
            if (!isset($_GET['nextstep']))
            {
                $ns = 1;
            }
            else
            {
                $ns = $_GET['nextstep'] + 2;
            }
            if ($r['itmid'] == $ir['equip_primary'])
            {
                echo '<b>Primary Weapon:</b> ';
            }
            if ($r['itmid'] == $ir['equip_secondary'])
            {
                echo '<b>Secondary Weapon:</b> ';
            }
            echo "<a href='attack.php?nextstep=$ns&amp;ID={$_GET['ID']}&amp;wepid={$r['itmid']}'>{$r['itmname']}</a><br />";
        }
    }
    else
    {
        echo 'You have nothing to fight with.';
    }
    $db->free_result($mw);
    echo '</table>';
    echo "<table width='50%' align='center'><tr><td align=right>Your Health: </td><td><img src=greenbar.png width={$vars['hpperc']} height=10><img src=redbar.png width={$vars['hpopp']} height=10></td><tr><td align=right>Opponents Health:  </td><td><img src=greenbar.png width={$vars2['hpperc']} height=10><img src=redbar.png width={$vars2['hpopp']} height=10></td></tr></table>";
}
$h->endpage();
