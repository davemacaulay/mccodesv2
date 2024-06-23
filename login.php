<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $set;
require_once('globals_nonauth.php');
$login_csrf = request_csrf_code('login');
print
        <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$set['game_name']}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="js/login.js"></script>
<link href="css/login.css" type="text/css" rel="stylesheet" />
</head>
<body onload="getme();">
<center>
<table width="970" border="0" cellpadding="0" cellspacing="0" class="table2">
<tr>
<td class="lgrad"></td>
<td class="center"><img src="title.jpg" alt="Mccodes Version 2" /><br />
<!-- Begin Main Content -->
EOF;
$IP = str_replace(['/', '\\', '\0'], '', $_SERVER['REMOTE_ADDR']);
if (file_exists('ipbans/' . $IP))
{
    die(
            "<span style='font-weight: bold; color:red;'>
            Your IP has been banned, there is no way around this.
            </span></body></html>");
}
$year = date('Y');
echo "<h3>&gt; {$set['game_name']} Log-In</h3>
<table width='80%'>
<tr>
<td width='50%'>
<fieldset>
<legend>About {$set['game_name']}</legend>
" . nl2br($set['game_description']) . '
</fieldset>
</td>
<td>';
echo <<<EOF
<fieldset>
<legend>Login</legend>
<form action='authenticate.php' method='POST' name='login' onsubmit='return saveme();'>
Username: <input type='text' name='username' /><br />
Password: <input type='password' name='password' /><br />
Remember me?<br />
<input type='radio' value='ON' name='save' /> Yes
<input type='radio' value='OFF' name='save' /> No<br />
<input type='hidden' name='verf' value='{$login_csrf}' />
<input type='submit' value='Submit'>
</form>
</fieldset>
EOF;
echo "</td></tr></table><br />
<h3><a href='register.php'>REGISTER NOW!</a></h3><br />
<i><center>Powered by codes made by Dabomstew (&copy {$year}). Game Copyright &copy;{$year} {$set['game_owner']}.</center></i>";
print
        <<<OUT
</td>
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
</body>
</html>
OUT;
