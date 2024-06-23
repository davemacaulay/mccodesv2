<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

$housequery = 1;
global $db, $ir, $userid, $h, $cm, $fm;
require_once('globals.php');
echo '<h3>General Info:</h2>';
$exp = (int) ($ir['exp'] / $ir['exp_needed'] * 100);
echo "<table><tr><td><b>Name:</b> {$ir['username']}</td><td><b>Crystals:</b> {$cm}</td></tr><tr>
<td><b>Level:</b> {$ir['level']}</td>
<td><b>Exp:</b> {$exp}%</td></tr><tr>
<td><b>Money:</b> $fm</td>
<td><b>HP:</b> {$ir['hp']}/{$ir['maxhp']}</td></tr>
<tr><td><b>Property:</b> {$ir['hNAME']}</td></tr></table>";
echo '<hr /><h3>Stats Info:</h3>';
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
<table>
<tr>
<td><b>Strength:</b> {$ir['strength']} [Ranked: {$ir['strank']}]</td>
<td><b>Agility:</b> {$ir['agility']} [Ranked: {$ir['agirank']}]</td>
</tr>
<tr><td><b>Guard:</b> {$ir['guard']} [Ranked: {$ir['guarank']}]</td><td><b>Labour:</b> {$ir['labour']} [Ranked: {$ir['labrank']}]</td></tr>
<tr><td><b>IQ: </b> {$ir['IQ']} [Ranked: {$ir['IQrank']}]</td><td><b>Total stats:</b> {$ts} [Ranked: $tsrank]</td></tr></table>";
$_POST['pn_update'] =
        (isset($_POST['pn_update']))
                ? strip_tags(stripslashes($_POST['pn_update'])) : '';
if (!empty($_POST['pn_update']))
{
    if (strlen($_POST['pn_update']) > 500)
    {
        echo '<hr /><span style="font-weight:bold;">You may only enter 500 or less characters here.</span>';
    }
    else
    {
        $pn_update_db = $db->escape($_POST['pn_update']);
        $db->query(
                "UPDATE `users`
        			SET `user_notepad` = '{$pn_update_db}'
        			WHERE `userid` = {$userid}");
        $ir['user_notepad'] = $_POST['pn_update'];
        echo '<hr /><span style="font-weight:bold;">Personal Notepad Updated!</span>';
    }
}
echo "<hr />Your Personal Notepad:<form action='index.php' method='post'>
<textarea rows='10' cols='50' name='pn_update'>"
        . htmlentities($ir['user_notepad'], ENT_QUOTES, 'ISO-8859-1')
        . "</textarea><br />
<input type='submit' value='Update Notes' /></form>";
$h->endpage();
