<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

if (file_exists('./installer.lock'))
{
    exit;
}
const MONO_ON = 1;
session_name('MCCSID');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
require_once('installer_head.php');
require_once('global_func.php');
require_once('lib/installer_error_handler.php');
set_error_handler('error_php');
if (!isset($_GET['code']))
{
    $_GET['code'] = '';
}
switch ($_GET['code'])
{
case 'install':
    install();
    break;
case 'config':
    config();
    break;
default:
    diagnostics();
    break;
}

/**
 * @param $highlight
 * @return void
 */
function menuprint($highlight): void
{
    $items =
            ['diag' => '1. Diagnostics', 'input' => '2. Configuration',
                    'sql' => '3. Installation & Extras',];
    $c = 0;
    echo '<hr />';
    foreach ($items as $k => $v)
    {
        $c++;
        if ($c > 1)
        {
            echo ' >> ';
        }
        if ($k == $highlight)
        {
            echo '<span style="color: black;">' . $v . '</span>';
        }
        else
        {
            echo '<span style="color: gray;">' . $v . '</span>';
        }
    }
    echo '<hr />';
}

/**
 * @return void
 */
function diagnostics(): void
{
    menuprint('diag');
    if (version_compare(phpversion(), '5.2.0') < 0)
    {
        $pv = '<span style="color: red">Failed</span>';
        $pvf = 0;
    }
    else
    {
        $pv = '<span style="color: green">OK</span>';
        $pvf = 1;
    }
    if (is_writable('./'))
    {
        $wv = '<span style="color: green">OK</span>';
        $wvf = 1;
    }
    else
    {
        $wv = '<span style="color: red">Failed</span>';
        $wvf = 0;
    }
    if (function_exists('mysqli_connect'))
    {
        $dv = '<span style="color: green">OK</span>';
        $dvf = 1;
    }
    else
    {
        $dv = '<span style="color: red">Failed</span>';
        $dvf = 0;
    }
    echo "
    <h3>Basic Diagnostic Results:</h3>
    <table width='80%' class='table' cellspacing='1' cellpadding='1' align='center'>
    		<tr>
    			<td>PHP version >= 5.2.0</td>
    			<td>{$pv}</td>
    		</tr>
    		<tr>
    			<td>Game folder writable</td>
    			<td>{$wv}</td>
    		</tr>
    		<tr>
    			<td>MySQL support in PHP present</td>
    			<td>{$dv}</td>
    		</tr>
    		<tr>
    			<td>MCCodes up to date</td>
    			<td>
        			<iframe
        				src='https://www.mccodes.com/update_check.php?version=20503'
        				width='250' height='30'></iframe>
        		</td>
        	</tr>
    </table>
       ";
    if ($pvf + $wvf + $dvf < 3)
    {
        echo "
		<hr />
		<span style='color: red; font-weight: bold;'>
		One of the basic diagnostics failed, so Setup cannot continue.
		Please fix the ones that failed and try again.
		</span>
		<hr />
   		";
    }
    else
    {
        echo "
		<hr />
		&gt; <a href='installer.php?code=config'>Next Step</a>
		<hr />
   		";
    }
}

/**
 * @return void
 */
function config(): void
{
    menuprint('input');
    echo "
    <h3>Configuration:</h3>
    <form action='installer.php?code=install' method='post'>
    <table width='75%' class='table' cellspacing='1' cellpadding='1' align='center'>
    		<tr>
    			<th colspan='2'>Database Config</th>
    		</tr>
    		<tr>
    			<td align='center'>MySQL Driver</td>
    			<td>
    				<input type='text' name='driver' value='mysqli' readonly>
    			</td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Hostname<br />
    				<small>This is usually localhost</small>
    			</td>
    			<td><input type='text' name='hostname' value='localhost' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Username<br />
    				<small>The user must be able to use the database</small>
    			</td>
    			<td><input type='text' name='username' value='' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Password</td>
    			<td><input type='text' name='password' value='' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Database Name<br />
    				<small>The database should not have any other software using it.</small>
    			</td>
    			<td><input type='text' name='database' value='' /></td>
    		</tr>
    		<tr>
    			<th colspan='2'>Game Config</th>
    		</tr>
    		<tr>
    			<td align='center'>Game Name</td>
    			<td><input type='text' name='game_name' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Game Owner<br />
    				<small>This can be your nick, real name, or a company</small>
    			</td>
    			<td><input type='text' name='game_owner' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Game Description<br />
    				<small>This is shown on the login page.</small>
    			</td>
    			<td><textarea rows='6' cols='40' name='game_description'></textarea></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				PayPal Address<br />
    				<small>This is where the payments for game DPs go. Must be at least Premier.</small>
    			</td>
    			<td><input type='text' name='paypal' /></td>
    		</tr>
    		<tr>
    			<th colspan='2'>Admin User</th>
    		</tr>
    		<tr>
    			<td align='center'>Username</td>
    			<td><input type='text' name='a_username' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Password</td>
    			<td><input type='password' name='a_password' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Confirm Password</td>
    			<td><input type='password' name='a_cpassword' /></td>
    		</tr>
    		<tr>
    			<td align='center'>E-Mail</td>
    			<td><input type='text' name='a_email' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Gender</td>
    			<td>
    				<select name='gender' type='dropdown'>
    					<option value='Male'>Male</option>
    					<option value='Female'>Female</option>
    				</select>
    			</td>
    		</tr>
    		<tr>
    			<td colspan='2' align='center'>
    				<input type='submit' value='Install' />
    			</td>
    		</tr>
    </table>
    </form>
       ";
}

/**
 * @param $text
 * @return string
 */
function gpc_cleanup($text): string
{
    return $text;
}

/**
 * @return void
 */
function install(): void
{
    menuprint('sql');
    $paypal =
            (isset($_POST['paypal'])
                    && filter_input(INPUT_POST, 'paypal',
                            FILTER_VALIDATE_EMAIL))
                    ? gpc_cleanup($_POST['paypal']) : '';
    $adm_email =
            (isset($_POST['a_email'])
                    && filter_input(INPUT_POST, 'a_email',
                            FILTER_VALIDATE_EMAIL))
                    ? gpc_cleanup($_POST['a_email']) : '';
    $adm_username =
            (isset($_POST['a_username']) && strlen($_POST['a_username']) > 3)
                    ? gpc_cleanup($_POST['a_username']) : '';
    $adm_gender =
            (isset($_POST['gender'])
                    && in_array($_POST['gender'], ['Male', 'Female'],
                            true)) ? $_POST['gender'] : 'Male';
    $description =
            (isset($_POST['game_description']))
                    ? gpc_cleanup($_POST['game_description']) : '';
    $owner =
            (isset($_POST['game_owner']) && strlen($_POST['game_owner']) > 3)
                    ? gpc_cleanup($_POST['game_owner']) : '';
    $game_name =
            (isset($_POST['game_name'])) ? gpc_cleanup($_POST['game_name'])
                    : '';
    $adm_pswd =
            (isset($_POST['a_password']) && strlen($_POST['a_password']) > 3)
                    ? gpc_cleanup($_POST['a_password']) : '';
    $adm_cpswd =
            isset($_POST['a_cpassword']) ? gpc_cleanup($_POST['a_cpassword'])
                    : '';
    $db_hostname =
            isset($_POST['hostname']) ? gpc_cleanup($_POST['hostname']) : '';
    $db_username =
            isset($_POST['username']) ? gpc_cleanup($_POST['username']) : '';
    $db_password =
            isset($_POST['password']) ? gpc_cleanup($_POST['password']) : '';
    $db_database =
            isset($_POST['database']) ? gpc_cleanup($_POST['database']) : '';
    $db_driver =
            (isset($_POST['driver'])
                    && $_POST['driver'] === 'mysqli') ? $_POST['driver'] : 'mysqli';
    $errors = [];
    if (empty($db_hostname))
    {
        $errors[] = 'No Database hostname specified';
    }
    if (empty($db_username))
    {
        $errors[] = 'No Database username specified';
    }
    if (empty($db_database))
    {
        $errors[] = 'No Database database specified';
    }
    if (!function_exists($db_driver . '_connect'))
    {
        $errors[] = 'Invalid database driver specified';
    }
    if (empty($adm_username)
            || !preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                    $adm_username))
    {
        $errors[] = 'Invalid admin username specified';
    }
    if (empty($adm_pswd))
    {
        $errors[] = 'Invalid admin password specified';
    }
    if ($adm_pswd !== $adm_cpswd)
    {
        $errors[] = 'The admin passwords did not match';
    }
    if (empty($adm_email))
    {
        $errors[] = 'Invalid admin email specified';
    }
    if (empty($owner)
            || !preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                    $owner))
    {
        $errors[] = 'Invalid game owner specified';
    }
    if (empty($game_name))
    {
        $errors[] = 'Invalid game name specified';
    }
    if (empty($description))
    {
        $errors[] = 'Invalid game description specified';
    }
    if (empty($paypal))
    {
        $errors[] = 'Invalid game PayPal specified';
    }
    if (count($errors) > 0)
    {
        echo 'Installation failed.<br />
        There were one or more problems with your input.<br />
        <br />
        <b>Problem(s) encountered:</b>
        <ul>';
        foreach ($errors as $error)
        {
            echo "<li><span style='color: red;'>{$error}</span></li>";
        }
        echo "</ul>
        &gt; <a href='installer.php?code=config'>Go back to config</a>";
        require_once('installer_foot.php');
        exit;
    }
    // Try to establish DB connection first...
    echo 'Attempting DB connection...<br />';
    require_once("class/class_db_{$db_driver}.php");
    $db = new database();
    $db->configure($db_hostname, $db_username, $db_password, $db_database);
    $db->connect();
    $c = $db->connection_id;
    // Done, move on
    echo '... Successful.<br />';
    echo 'Writing game config file...<br />';
    echo 'Write Config...<br />';
    $code = md5((string)rand(1, 100000000000));
    if (file_exists('config.php'))
    {
        unlink('config.php');
    }
    $e_db_hostname = addslashes($db_hostname);
    $e_db_username = addslashes($db_username);
    $e_db_password = addslashes($db_password);
    $e_db_database = addslashes($db_database);
    $lit_config = '$_CONFIG';
    $config_file =
            <<<EOF
<?php
            {$lit_config} = array(
	'hostname' => '{$e_db_hostname}',
	'username' => '{$e_db_username}',
	'password' => '{$e_db_password}',
	'database' => '{$e_db_database}',
	'persistent' => 0,
	'driver' => '{$db_driver}',
	'code' => '{$code}',
);
?>
EOF;
    $f = fopen('config.php', 'w');
    fwrite($f, $config_file);
    fclose($f);
    echo '... file written.<br />';
    echo 'Writing base database schema...<br />';
    $fo = fopen('dbdata.sql', 'r');
    $query = '';
    $lines = explode("\n", fread($fo, 1024768));
    fclose($fo);
    foreach ($lines as $line)
    {
        if (!(str_starts_with($line, '--')) && trim($line) != '')
        {
            $query .= $line;
            if (!(!str_contains($line, ';')))
            {
                $db->query($query);
                $query = '';
            }
        }
    }
    echo '... done.<br />';
    echo 'Writing game configuration...<br />';
    $ins_username =
            $db->escape(htmlentities($adm_username, ENT_QUOTES, 'ISO-8859-1'));
    $salt = generate_pass_salt();
    $e_salt = $db->escape($salt);
    $encpsw = encode_password($adm_pswd, $salt);
    $e_encpsw = $db->escape($encpsw);
    $ins_email = $db->escape($adm_email);
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $ins_game_name =
            $db->escape(htmlentities($game_name, ENT_QUOTES, 'ISO-8859-1'));
    $ins_game_desc =
            $db->escape(htmlentities($description, ENT_QUOTES, 'ISO-8859-1'));
    $ins_paypal = $db->escape($paypal);
    $ins_game_owner =
            $db->escape(htmlentities($owner, ENT_QUOTES, 'ISO-8859-1'));
    $db->query(
            "INSERT INTO `users`
             (`username`, `login_name`, `userpass`, `level`, `money`,
             `crystals`, `donatordays`, `user_level`, `energy`, `maxenergy`,
             `will`, `maxwill`, `brave`, `maxbrave`, `hp`, `maxhp`, `location`,
             `gender`, `signedup`, `email`, `bankmoney`, `lastip`,
             `lastip_signup`, `pass_salt`, , `display_pic`, `staffnotes`, `voted`, `user_notepad`)
             VALUES ('{$ins_username}', '{$ins_username}', '{$e_encpsw}', 1,
             100, 0, 0, 2, 12, 12, 100, 100, 5, 5, 100, 100, 1,
             '{$adm_gender}', " . time()
                    . ", '{$ins_email}', -1, '$IP', '$IP',
             '{$e_salt}', '', '', '', '')");
    $i = $db->insert_id();
    $db->query(
            "INSERT INTO `userstats`
    		 VALUES($i, 10, 10, 10, 10, 10)");
    $db->query(
            "INSERT INTO `settings`
             VALUES(NULL, 'game_name', '{$ins_game_name}', 'string')");
    $db->query(
            "INSERT INTO `settings`
             VALUES(NULL, 'game_owner', '{$ins_game_owner}', 'string')");
    $db->query(
            "INSERT INTO `settings`
             VALUES(NULL, 'paypal', '{$ins_paypal}', 'string')");
    $db->query(
            "INSERT INTO `settings`
             VALUES(NULL, 'game_description', '{$ins_game_desc}', 'string')");
    echo '... Done.<br />';
    $path = dirname($_SERVER['SCRIPT_FILENAME']);
    echo "
    <h2>Installation Complete!</h2>
    <hr />
    <h3>Cron Info</h3>
    <br />
    This is the cron info you need for section <b>1.2 Cronjobs</b> of the installation instructions.<br />
    <pre>
    * * * * * php $path/crons/CronHandler.php cron=minute-1 code=$code
    */5 * * * * php $path/crons/CronHandler.php cron=minute-5 code=$code
    0 * * * * php $path/crons/CronHandler.php cron=hour-1 code=$code
    0 0 * * * php $path/crons/CronHandler.php cron=day-1 code=$code
    </pre><br>
    Alternatively, you can toggle the \"Use Timestamps Instead of Cron Jobs\" option in the Basic Settings on the Staff Panel to use timestamps instead.<br><br>
    Note: You <em>must</em> use one <strong>or</strong> the other. Using neither will mean no ticks/refills, etc., and using both will mean double updates. 
       ";
    echo '<h3>Installer Security</h3>
    Attempting to remove installer... ';
    @unlink('./installer.php');
    $success = !file_exists('./installer.php');
    echo "<span style='color: "
            . ($success ? "green;'>Succeeded" : "red;'>Failed")
            . '</span><br />';
    if (!$success)
    {
        echo 'Attempting to lock installer... ';
        @touch('./installer.lock');
        $success2 = file_exists('installer.lock');
        echo "<span style='color: "
                . ($success2 ? "green;'>Succeeded" : "red;'>Failed")
                . '</span><br />';
        if ($success2)
        {
            echo "<span style='font-weight: bold;'>"
                    . 'You should now remove installer.php from your server.'
                    . '</span>';
        }
        else
        {
            echo "<span style='font-weight: bold; font-size: 20pt;'>"
                    . 'YOU MUST REMOVE installer.php '
                    . 'from your server.<br />'
                    . 'Failing to do so will allow other people '
                    . 'to run the installer again and potentially '
                    . 'mess up your game entirely.' . '</span>';
        }
    }
    else
    {
        require_once('installer_foot.php');
        @unlink('./installer_head.php');
        @unlink('./installer_foot.php');
        exit;
    }
}
require_once('installer_foot.php');
