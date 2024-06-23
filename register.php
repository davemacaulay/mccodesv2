<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $set;
require_once('globals_nonauth.php');
//thx to http://www.phpit.net/code/valid-email/ for valid_email

/**
 * @param $email
 * @return bool
 */
function valid_email($email): bool
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email);
}
print
        <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$set['game_name']}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="{$set['jquery_location']}"></script>
<script type="text/javascript" src="js/register.js"></script>
<link href="css/register.css" type="text/css" rel="stylesheet" />
</head>
<body>
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
$username =
        (isset($_POST['username'])
                && preg_match(
                        "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                        $_POST['username'])
                && ((strlen($_POST['username']) < 32)
                        && (strlen($_POST['username']) >= 3)))
                ? stripslashes($_POST['username']) : '';
if (!empty($username))
{
    if ($set['regcap_on'])
    {
        if (!$_SESSION['captcha'] || !isset($_POST['captcha'])
                || $_SESSION['captcha'] != $_POST['captcha'])
        {
            unset($_SESSION['captcha']);
            echo "Captcha Test Failed<br />
			&gt; <a href='register.php'>Back</a>";
            register_footer();
        }
        unset($_SESSION['captcha']);
    }
    if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email'])))
    {
        echo "Sorry, the email is invalid.<br />
		&gt; <a href='register.php'>Back</a>";
        register_footer();
    }
    // Check Gender
    if (!isset($_POST['gender'])
            || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female'))
    {
        echo "Sorry, the gender is invalid.<br />
			&gt; <a href='register.php'>Back</a>";
        register_footer();
    }
    $e_gender = $db->escape(stripslashes($_POST['gender']));
    $sm = 100;
    if (isset($_POST['promo']) && $_POST['promo'] == 'Your Promo Code Here')
    {
        $sm += 100;
    }
    $e_username = $db->escape($username);
    $e_email = $db->escape(stripslashes($_POST['email']));
    $q =
            $db->query(
                    "SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `username` = '{$e_username}'
                     OR `login_name` = '{$e_username}'");
    $q2 =
            $db->query(
                    "SELECT COUNT(`userid`)
    				 FROM `users`
    				 WHERE `email` = '{$e_email}'");
    $u_check = $db->fetch_single($q);
    $e_check = $db->fetch_single($q2);
    $db->free_result($q);
    $db->free_result($q2);
    $base_pw =
            (isset($_POST['password']) && is_string($_POST['password']))
                    ? stripslashes($_POST['password']) : '';
    $check_pw =
            (isset($_POST['cpassword']) && is_string($_POST['cpassword']))
                    ? stripslashes($_POST['cpassword']) : '';
    if ($u_check > 0)
    {
        echo "Username already in use. Choose another.<br />
		&gt; <a href='register.php'>Back</a>";
    }
    elseif ($e_check > 0)
    {
        echo "E-Mail already in use. Choose another.<br />
		&gt; <a href='register.php'>Back</a>";
    }
    elseif (empty($base_pw) || empty($check_pw))
    {
        echo "You must specify your password and confirm it.<br />
		&gt; <a href='register.php'>Back</a>";
    }
    elseif ($base_pw != $check_pw)
    {
        echo "The passwords did not match, go back and try again.<br />
		&gt; <a href='register.php'>Back</a>";
    }
    else
    {
        $rem_IP = '';
        $_POST['ref'] =
                (isset($_POST['ref']) && is_numeric($_POST['ref']))
                        ? abs(intval($_POST['ref'])) : '';
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        if ($_POST['ref'])
        {
            $q =
                    $db->query(
                            "SELECT `lastip`
                             FROM `users`
                             WHERE `userid` = {$_POST['ref']}");
            if ($db->num_rows($q) == 0)
            {
                $db->free_result($q);
                echo "Referrer does not exist.<br />
				&gt; <a href='register.php'>Back</a>";
                register_footer();
            }
            $rem_IP = $db->fetch_single($q);
            $db->free_result($q);
            if ($rem_IP == $_SERVER['REMOTE_ADDR'])
            {
                echo "No creating referral multies.<br />
				&gt; <a href='register.php'>Back</a>";
                register_footer();
            }
        }
        $salt = generate_pass_salt();
        $e_salt = $db->escape($salt);
        $encpsw = encode_password($base_pw, $salt);
        $e_encpsw = $db->escape($encpsw);
        $db->query(
                "INSERT INTO `users`
                 (`username`, `login_name`, `userpass`, `level`,
                 `money`, `crystals`, `donatordays`, `user_level`,
                 `energy`, `maxenergy`, `will`, `maxwill`, `brave`,
                 `maxbrave`, `hp`, `maxhp`, `location`, `gender`,
                 `signedup`, `email`, `bankmoney`, `lastip`,
                 `lastip_signup`, `pass_salt`)
                 VALUES('{$e_username}', '{$e_username}', '{$e_encpsw}', 1,
                 $sm, 0, 0, 1, 12, 12, 100, 100, 5, 5, 100, 100, 1,
                 '{$e_gender}', " . time()
                        . ",'{$e_email}', -1, '$IP',
                 '$IP', '{$e_salt}')");
        $i = $db->insert_id();
        $db->query(
                "INSERT INTO `userstats`
        	     VALUES($i, 10, 10, 10, 10, 10)");

        if ($_POST['ref'])
        {
            $db->query(
                    "UPDATE `users`
                     SET `crystals` = `crystals` + 2
                     WHERE `userid` = {$_POST['ref']}");
            event_add($_POST['ref'],
                "For referring $username to the game, you have earned 2 valuable crystals!");
            $e_rip = $db->escape($rem_IP);
            $db->query(
                    "INSERT INTO `referals`
                     VALUES(NULL, {$_POST['ref']}, $i, " . time()
                            . ", '{$e_rip}', '$IP')");
        }
        echo "You have signed up, enjoy the game.<br />
		&gt; <a href='login.php'>Login</a>";
    }
}
else
{
    if ($set['regcap_on'])
    {
        /** @noinspection SpellCheckingInspection */
        $chars               =
                "123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?\\/%^";
        $len                 = strlen($chars);
        $_SESSION['captcha'] = '';
        for ($i = 0; $i < 6; $i++)
            $_SESSION['captcha'] .= $chars[rand(0, $len - 1)];
    }

    echo "<h3>{$set['game_name']} Registration</h3>";
    echo "<form action=register.php method=post>
            <table width='75%' class='table' cellspacing='1'>
                <tr>
                    <td width='30%'>Username</td>
                    <td width='40%'>
                    	<input type='text' name='username'
                    	 onkeyup='CheckUsername(this.value);' />
                    </td>
                    <td width='30%'><div id='usernameresult'></div></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td>
                    	<input type='password' id='pw1' name='password'
                    	 onkeyup='CheckPasswords(this.value);PasswordMatch();' />
                    </td>
                    <td><div id='passwordresult'></div></td>
                </tr>
                <tr>
                    <td>Confirm Password</td>
                    <td>
                    	<input type='password' name='cpassword' id='pw2'
                    	 onkeyup='PasswordMatch();' />
                    </td>
                    <td><div id='cpasswordresult'></div></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                    	<input type='text' name='email'
                    	 onkeyup='CheckEmail(this.value);' />
                    </td>
                    <td><div id='emailresult'></div></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td colspan='2'>
                    	<select name='gender' type='dropdown'>
                    	<option value='Male'>Male</option>
                    	<option value='Female'>Female</option>
                    	</select>
                    </td>
                </tr>
                <tr>
                    <td>Promo Code</td>
                    <td colspan='2'><input type='text' name='promo' /></td>
                </tr>

                <input type='hidden' name='ref' value='";
    if (!isset($_GET['REF']))
    {
        $_GET['REF'] = 0;
    }
    $_GET['REF'] = abs((int) $_GET['REF']);
    if ($_GET['REF'])
    {
        print $_GET['REF'];
    }
    echo "' />";
    if ($set['regcap_on'])
    {
        echo "<tr>
				<td colspan='3'>
					<img src='captcha_verify.php?bgcolor=C3C3C3' /><br />
					<input type='text' name='captcha' />
				</td>
			  </tr>";
    }
    echo "
			<tr>
				<td colspan='3' align='center'>
					<input type='submit' value='Submit' />
				</td>
			</tr>
	</table>
	</form><br />
	&gt; <a href='login.php'>Go Back</a>";
}
register_footer();

/**
 * @return void
 */
function register_footer(): void
{
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
</center>
</body>
</html>
OUT;
    exit;
}
