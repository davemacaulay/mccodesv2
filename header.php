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
 * File: header.php
 * Signature: 52c201ce2e8c549ae70d2936473022f0
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

class headers
{

    /**
     * @return void
     */
    public function startheaders(): void
    {
        global $set;
        echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/game.css" type="text/css" rel="stylesheet" />
<title>{$set['game_name']}</title>
</head>
<body>
<center>
<table width="970" border="0" cellpadding="0" cellspacing="0" class="table2">
<tr>
<td class="lgrad"></td>
<td class="center">
EOF;
    }

    /**
     * @param $ir
     * @param $lv
     * @param $fm
     * @param $cm
     * @param int $dosessh
     * @return void
     */
    public function userdata($ir, $lv, $fm, $cm, int $dosessh = 1): void
    {
        global $db, $userid, $set;
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $db->query(
            "UPDATE `users`
                 SET `laston` = {$_SERVER['REQUEST_TIME']}, `lastip` = '$IP'
                 WHERE `userid` = $userid");
        if (!$ir['email']) {
            global $domain;
            die(
            "<body>Your account may be broken. Please mail help@{$domain} stating your username and player ID.");
        }
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            echo 'You lost all your EXP for running from the fight.';
            $db->query(
                "UPDATE `users`
                     SET `exp` = 0, `attacking` = 0
                     WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
        $enperc = min((int)($ir['energy'] / $ir['maxenergy'] * 100), 100);
        $wiperc = min((int)($ir['will'] / $ir['maxwill'] * 100), 100);
        $experc = min((int)($ir['exp'] / $ir['exp_needed'] * 100), 100);
        $brperc = min((int)($ir['brave'] / $ir['maxbrave'] * 100), 100);
        $hpperc = min((int)($ir['hp'] / $ir['maxhp'] * 100), 100);
        $enopp  = 100 - $enperc;
        $wiopp  = 100 - $wiperc;
        $exopp  = 100 - $experc;
        $bropp  = 100 - $brperc;
        $hpopp  = 100 - $hpperc;
        $d      = '';
        $u      = $ir['username'];
        if ($ir['donatordays']) {
            $u = "<span style='color: red;'>{$ir['username']}</span>";
            $d =
                "<img src='donator.gif'
                     alt='Donator: {$ir['donatordays']} Days Left'
                     title='Donator: {$ir['donatordays']} Days Left' />";
        }

        $gn = '';
        $bgcolor = 'FFFFFF';

        print
            <<<OUT
<img src="title.jpg" alt="Mccodes Version 2" /><br />
<!-- Begin Main Content -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="20%" bgcolor="#$bgcolor" valign="top">
<!-- Side Panel -->
<b>Name:</b> $gn{$u} [{$ir['userid']}] $d<br />
<b>Money:</b> {$fm}<br />
<b>Level:</b> {$ir['level']}<br />
<b>Crystals:</b> {$ir['crystals']}<br />
[<a href='logout.php'>Emergency Logout</a>]
<hr />
<b>Energy:</b> {$enperc}%<br />
<img src='greenbar.png' width='$enperc' height='10' /><img src='redbar.png' width='$enopp' height='10' /><br />
<b>Will:</b> {$wiperc}%<br />
<img src='bluebar.png' width='$wiperc' height='10' /><img src='redbar.png' width='$wiopp' height='10' /><br />
<b>Brave:</b> {$ir['brave']}/{$ir['maxbrave']}<br />
<img src='yellowbar.png' width='$brperc' height='10' /><img src='redbar.png' width='$bropp' height='10' /><br />
<b>EXP:</b> {$experc}%<br />
<img src='bluebar.png' width='$experc' height='10' /><img src='redbar.png' width='$exopp' height='10' /><br />
<b>Health:</b> {$hpperc}%<br />
<img src='greenbar.png' width='$hpperc' height='10' /><img src='redbar.png' width='$hpopp' height='10' /><br /><hr />
<!-- Links -->
OUT;
        if ($ir['fedjail'] > 0) {
            $q =
                $db->query(
                    "SELECT *
                             FROM `fedjail`
                             WHERE `fed_userid` = $userid");
            $r = $db->fetch_row($q);
            die(
            "<span style='font-weight: bold; color:red;'>
                    You have been put in the {$set['game_name']} Federal Jail
                     for {$r['fed_days']} day(s).<br />
                    Reason: {$r['fed_reason']}
                    </span></body></html>");
        }
        if (file_exists('ipbans/' . $IP)) {
            die(
            "<span style='font-weight: bold; color:red;'>
                    Your IP has been banned from {$set['game_name']},
                     there is no way around this.
                    </span></body></html>");
        }
    }

    /**
     * @return void
     * @noinspection SpellCheckingInspection
     */
    public function menuarea(): void
    {
        define('JDSF45TJI', true);
        include 'mainmenu.php';
        global $ir, $set;
        $bgcolor = 'FFFFFF';
        print
            '</td><td width="2" class="linegrad" bgcolor="#' . $bgcolor
            . '">&nbsp;</td><td width="80%"  bgcolor="#'
            . $bgcolor . '" valign="top"><br /><center>';
        if ($ir['hospital']) {
            echo "<b>NB:</b> You are currently in hospital for {$ir['hospital']} minutes.<br />";
        }
        if ($ir['jail']) {
            echo "<b>NB:</b> You are currently in jail for {$ir['jail']} minutes.<br />";
        }
        echo "<a href='donator.php'><b>Donate to {$set['game_name']} now for game benefits!</b></a><br />";
    }

    /**
     * @return void
     * @noinspection SpellCheckingInspection
     */
    public function smenuarea(): void
    {
        define('JDSF45TJI', true);
        include 'smenu.php';
        $bgcolor = 'FFFFFF';
        print
            '</td><td width="2" class="linegrad" bgcolor="#' . $bgcolor
            . '">&nbsp;</td><td width="80%"  bgcolor="#'
            . $bgcolor . '" valign="top"><center>';
    }

    /**
     * @return void
     */
    public function endpage(): void
    {
        global $db;
        $query_extra = '';
        if (isset($_GET['mysqldebug']) && check_access('administrator')) {
            $query_extra = '<br />' . implode('<br />', $db->queries);
        }
        print
            <<<OUT
</center>
</td>
</tr>
</table></td>
<td class="rgrad"></td>
</tr>
<tr>
<td colspan="3">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td class="dgradl">&nbsp;</td>
<td class="dgrad">&nbsp;</td>
<td class="dgradr">&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
                {$db->num_queries} queries{$query_extra}</body>
</html>
OUT;
    }
}
