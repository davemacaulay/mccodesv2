<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

$housequery = 1;
global $db, $ir, $userid, $h, $set, $lv, $cm, $fm;
require_once('globals.php');
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/installer.php')
        && check_access('administrator'))
{
    echo '
	<span style="font-weight: bold; font-size: 42px; color: red;">
	WARNING: you have not deleted installer.php from the server.
	<br />
	We suggest you do this immediately.
	</span>
   	';
}
echo '
<b>Welcome,  ' . $ir['username'] . '!</b>
<br />
<em>Your last visit was: ' . $lv . '.</em>
   ';

$exp = (int) ($ir['exp'] / $ir['exp_needed'] * 100);
if ($ir['hospital'])
{
    $geninf =
            '<tr><td colspan="2"><font color="red">You are in Hospital for '
                    . $ir['hospital'] . ' minute(s)</font></td></tr>';
}
elseif ($ir['jail'])
{
    $geninf =
            '<tr><td colspan="2"><font color="red">You are in Jail for '
                    . $ir['jail'] . ' minute(s)</font></td></tr>';
}
else
{
    $geninf = '';
}
echo "
<table cellspacing='1' cellpadding='3' class='table' width='70%'>
		<tr>
			<td colspan='2' class='h'><b>General Information</b></td>
		</tr>
{$geninf}
		<tr>
			<td><b>Name:</b> {$ir['username']}</td>
			<td><b>Crystals:</b> {$cm}</td>
		</tr>
		<tr>
			<td><b>Level:</b> {$ir['level']}</td>
			<td><b>Exp:</b> {$exp}%</td>
		</tr>
		<tr>
			<td><b>Money:</b> $fm</td>
			<td><b>HP:</b> {$ir['hp']}/{$ir['maxhp']}</td>
		</tr>
		<tr>
			<td><b>Property:</b> {$ir['hNAME']}</td>
			<td><b>Days Old:</b> {$ir['daysold']}</td>
		</tr>
   ";
$ts =
        $ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labour']
                + $ir['IQ'];
$ir['strank'] = get_rank($ir['strength'], 'strength');
$ir['agirank'] = get_rank($ir['agility'], 'agility');
$ir['guarank'] = get_rank($ir['guard'], 'guard');
$ir['labrank'] = get_rank($ir['labour'], 'labour');
$ir['IQrank'] = get_rank($ir['IQ'], 'IQ');
$tsrank = get_rank($ts, 'strength+agility+guard+labour+IQ');
$ir['strength'] = number_format($ir['strength']);
$ir['agility'] = number_format($ir['agility']);
$ir['guard'] = number_format($ir['guard']);
$ir['labour'] = number_format($ir['labour']);
$ir['IQ'] = number_format($ir['IQ']);
$ts = number_format($ts);
echo "
		<tr>
			<td class='h' colspan='2'><b>Stats Info</b></td></tr>
		<tr>
			<td><b>Strength:</b> {$ir['strength']} [Ranked: {$ir['strank']}]</td>
			<td><b>Agility:</b> {$ir['agility']} [Ranked: {$ir['agirank']}]</td>
		</tr>
		<tr>
			<td><b>Guard:</b> {$ir['guard']} [Ranked: {$ir['guarank']}]</td>
			<td><b>Labour:</b> {$ir['labour']} [Ranked: {$ir['labrank']}]</td>
		</tr>
		<tr>
			<td><b>IQ: </b> {$ir['IQ']} [Ranked: {$ir['IQrank']}]</td>
			<td><b>Total stats:</b> {$ts} [Ranked: $tsrank]</td>
		</tr>
</table>
   ";
$q = $db->query('SELECT `content` FROM `papercontent`');
$news = $db->fetch_single($q);
$db->free_result($q);
echo $set['game_name'] . ' Latest News:
<br />
' . nl2br($news) . '
<br />
   ';
$h->endpage();
