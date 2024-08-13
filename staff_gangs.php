<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */
global $h;
require_once('sglobals.php');
if (!check_access('manage_gangs')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
//This contains gang stuffs
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'grecord':
    admin_gang_record();
    break;
case 'gcredit':
    admin_gang_credit();
    break;
case 'gwar':
    admin_gang_wars();
    break;
case 'gwardelete':
    admin_gang_wardelete();
    break;
case 'gedit':
    admin_gang_edit_begin();
    break;
case 'gedit_name':
    admin_gang_edit_name();
    break;
case 'gedit_prefix':
    admin_gang_edit_prefix();
    break;
case 'gedit_finances':
    admin_gang_edit_finances();
    break;
case 'gedit_staff':
    admin_gang_edit_staff();
    break;
case 'gedit_capacity':
    admin_gang_edit_capacity();
    break;
case 'gedit_crime':
    admin_gang_edit_crime();
    break;
case 'gedit_ament':
    admin_gang_edit_ament();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function admin_gang_record(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_POST['gang']) && is_numeric($_POST['gang']))
                    ? abs(intval($_POST['gang'])) : '';
    $_POST['reason'] =
            (isset($_POST['reason'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    if ($gang)
    {
        staff_csrf_stdverify('staff_gangs_record',
                'staff_gangs.php?action=grecord');
        $q =
                $db->query(
                        "SELECT `gangNAME`, `gangDESC`, `gangPREF`,
                         `gangMONEY`, `gangCRYSTALS`, `gangRESPECT`,
                         `gangPRESIDENT`, `gangVICEPRES`, `gangCAPACITY`,
                         `gangCRIME`, `gangCHOURS`, `gangAMENT`, `gangID`
                         FROM `gangs`
                         WHERE `gangID`  = $gang");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            $_POST['gang'] = 0;
            admin_gang_record();
        }
        elseif (!$_POST['reason'])
        {
            $_POST['gang'] = 0;
            admin_gang_record();
        }
        else
        {
            $r = $db->fetch_row($q);
            $db->free_result($q);
            echo "
            <table width='100%' border='1'>
            		<tr>
            	<td>
            Gang Name: {$r['gangNAME']}
            <br />
            Gang Description: {$r['gangDESC']}
            <br />
            Prefix: {$r['gangPREF']}
            <br />
            Money: {$r['gangMONEY']}
            <br />
            Crystals: {$r['gangCRYSTALS']}
            <br />
            Respect: {$r['gangRESPECT']}
            <br />
            President: {$r['gangPRESIDENT']}
            <br />
            Vice-President: {$r['gangVICEPRES']}
            <br />
            Capacity: {$r['gangCAPACITY']}
            <br />
            Crime: {$r['gangCRIME']}
            <br />
            Hours Left: {$r['gangCHOURS']}
            <br />
            Annnouncement: {$r['gangAMENT']}
            	</td>
            		</tr>
            </table>
   			";
            stafflog_add(
                    $ir['username'] . ' looked at gang id ' . $r['gangID']
                            . ' (' . $r['gangNAME']
                            . ')\'s record. with the reason '
                            . $_POST['reason']);
        }
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_record');
        echo "
		<form action='staff_gangs.php?action=grecord' method='post'>
		<h4>Gang Record</h4>
			Enter a gang ID to view the record of: <input type='text' name='gang' value='1' /><br />
			Reason for viewing: <input type='text' name='reason' value='' /><br />
	        {$csrf}
			<input type='submit' value='Go' />
		</form>
  		 ";
    }
}

/**
 * @return void
 */
function admin_gang_credit(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_POST['gang']) && is_numeric($_POST['gang']))
                    ? abs(intval($_POST['gang'])) : '';
    $money =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 0;
    $crystals =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    $reason =
            (isset($_POST['reason'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    if (($money != 0 || $crystals != 0) && ($gang && $reason))
    {
        $q =
                $db->query(
                        "SELECT `gangNAME`
                         FROM `gangs`
                         WHERE `gangID` = $gang");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid gang.';
            $h->endpage();
            exit;
        }
        staff_csrf_stdverify('staff_gangs_credit2',
                'staff_gangs.php?action=gcredit');
        $gangname = $db->fetch_single($q);
        $db->free_result($q);
        $db->query(
                "UPDATE `gangs`
                 SET `gangMONEY` = `gangMONEY` + $money,
                 `gangCRYSTALS` = `gangCRYSTALS` + $crystals
                 WHERE `gangID` = $gang");
        echo "The gang {$gangname} was successfully credited.";
        stafflog_add(
                "{$ir['username']} credited {$gangname} (gang ID {$gang})
                 with {$money} money and/or {$crystals} crystals
                 with the reason {$reason}");
    }
    elseif ($gang && ($money != 0 || $crystals != 0))
    {
        staff_csrf_stdverify('staff_gangs_credit1',
                'staff_gangs.php?action=gcredit');
        $q =
                $db->query(
                        "SELECT `gangNAME`
                         FROM `gangs`
                         WHERE `gangID` = $gang");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid gang.';
            $h->endpage();
            exit;
        }
        $csrf = request_csrf_html('staff_gangs_credit2');
        echo '
        You are crediting ' . $db->fetch_single($q) . ' with '
                . money_formatter($money)
                . " and/or $crystals crystals.
        <br />
        <form action='staff_gangs.php?action=gcredit' method='post'>
        	<input type='hidden' name='gang' value='$gang' />
        	<input type='hidden' name='money' value='$money' />
        	<input type='hidden' name='crystals' value='$crystals' />
        	{$csrf}
        	Reason: <input type='text' name='reason' />
        <br />
        	<input type='submit' value='Credit' />
        </form>
           ";
        $db->free_result($q);
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_credit1');
        echo "
        <h3>Credit Gang</h3>
        <form action='staff_gangs.php?action=gcredit' method='post'>
        <table border='1' width='50%'>
        		<tr>
        			<td align='right'>Gang's ID:</td>
        			<td align='left'>
        				<input type='text' name='gang' value='1' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Money:</td>
        			<td align='left'>
        				<input type='text' name='money' value='1000' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Crystals:</td>
        			<td align='left'>
        				<input type='text' name='crystals' value='10' />
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Credit' />
                    </td>
        		</tr>
        </table>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_wars(): void
{
    global $db, $ir, $h;
    echo '
	<h3>Manage Gang Wars</h3>
	<table width="75%" border="2">
   	';
    $q =
            $db->query(
                'SELECT `warID`, `warDECLARED`, `warDECLARER`,
                     `g1`.`gangNAME` AS `declarer`,
                     `g1`.`gangRESPECT` AS `drespect`,
                     `g2`.`gangNAME` AS `defender`,
                     `g2`.`gangRESPECT` AS `frespect`
                     FROM `gangwars` AS `w`
                     LEFT JOIN `gangs` AS `g1`
                     ON `w`.`warDECLARER` = `g1`.`gangID`
                     LEFT JOIN `gangs` AS `g2`
                     ON `w`.`warDECLARED` = `g2`.`gangID`');
    while ($r = $db->fetch_row($q))
    {
        $csrf = request_csrf_html("staff_gangs_wardelete{$r['warID']}");
        echo "
		<tr>
			<td width='40%'>
				<a href='gangs.php?action=view&ID={$r['warDECLARER']}'>
                    {$r['declarer']}
                </a>
				[{$r['drespect']} respect]
			</td>
			<td width='10%'>vs.</td>
			<td width='40%'>
				<a href='gangs.php?action=view&ID={$r['warDECLARED']}'>
                    {$r['defender']}
                </a>
                [{$r['frespect']} respect]
            </td>
			<td>
				<form action='staff_gangs.php?action=gwardelete&amp;war={$r['warID']}' method='post'>
			        {$csrf}
					<input type='submit' value='Delete' />
				</form>
			</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function admin_gang_wardelete(): void
{
    global $db, $ir, $h;
    $_GET['war'] =
            (isset($_GET['war']) && is_numeric($_GET['war']))
                    ? abs(intval($_GET['war'])) : 0;
    staff_csrf_stdverify("staff_gangs_wardelete{$_GET['war']}",
            'staff_gangs.php?action=gwar');
    $q =
            $db->query(
                    "SELECT `warDECLARED`, `warDECLARER`,
                     `g1`.`gangNAME` AS `declarer`,
                     `g1`.`gangRESPECT` AS `drespect`,
                     `g2`.`gangNAME` AS `defender`,
                     `g2`.`gangRESPECT` AS `frespect`
                     FROM `gangwars` AS `w`
                     LEFT JOIN `gangs` AS `g1`
                     ON `w`.`warDECLARER` = `g1`.`gangID`
                     LEFT JOIN `gangs` AS `g2`
                     ON `w`.`warDECLARED` = `g2`.`gangID`
                     WHERE `w`.`warID` = {$_GET['war']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid war.<br />
        &gt; <a href="staff_gangs.php?action=gwar">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    $db->query("DELETE FROM `gangwars`
    			WHERE `warID` = {$_GET['war']}");
    echo 'War cleared.<br />
    &gt; <a href="staff_gangs.php?action=gwar">Go Back</a>';
    stafflog_add(
            "{$ir['username']} deleted war ID {$_GET['war']}
             (<a href='gangs.php?action=view&amp;ID={$r['warDECLARER']}'>{$r['declarer']}</a>
             	[{$r['drespect']} respect]
             	vs.
              <a href='gangs.php?action=view&amp;ID={$r['warDECLARED']}'>{$r['defender']}</a>
              	[{$r['frespect']} respect])");
}

/**
 * @return void
 */
function admin_gang_edit_begin(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_POST['gang']) && is_numeric($_POST['gang']))
                    ? abs(intval($_POST['gang'])) : '';
    if ($gang)
    {
        $q =
                $db->query(
                        "SELECT `gangNAME`
                         FROM `gangs`
                         WHERE `gangID` = $gang");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid gang.';
            $h->endpage();
            exit;
        }
        $theirname = $db->fetch_single($q);
        $edits =
                [1 => ['Name And Description', 'gedit_name', '4'],
                        2 => ['Prefix', 'gedit_prefix', '4'],
                        3 => ['Finances + Respect', 'gedit_finances', '4'],
                        4 => ['Staff', 'gedit_staff', '4'],
                        5 => ['Capacity', 'gedit_capacity', '4'],
                        6 => ['Organised Crime', 'gedit_crime', '4'],
                        7 => ['Announcement', 'gedit_ament', '4']];
        echo "
        <h3>Manage Gang</h3>
        You are managing the gang: $theirname
        <br />
        Choose an edit to perform.
        <br />
        <table width='80%' class='table' cellspacing='1'>
        		<tr>
        			<th>Edit Type</th>
        			<th>Available For Use</th>
        			<th>Use</th>
        		</tr>
   		";
        foreach ($edits as $k => $v)
        {
            if ($v[2] >= $ir['user_level'])
            {
                $a = "green'>Yes";
                $l =
                        "<a href='staff_gangs.php?action=$v[1]&amp;gang=$gang'>Go</a>";
            }
            else
            {
                $a = "red'>No";
                $l = 'N/A';
            }
            echo "
			<tr>
				<td>$v[0]</td>
				<td><span style='font-weight: bold; color: $a</span></td>
				<td>$l</td>
			</tr>
   			";
        }
        echo '</table>';
    }
    else
    {
        echo "
		<form action='staff_gangs.php?action=gedit' method='post'>
			<h4>Gang Management</h4>
			Enter a gang ID to manage: <input type='text' name='gang' value='1' />
			<br />
			<input type='submit' value='Go' />
		</form>
   		";
    }
}

/**
 * @return void
 */
function admin_gang_edit_name(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : 0;
    $_POST['gangNAME'] =
            (isset($_POST['gangNAME'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['gangNAME']))
                    ? $db->escape(strip_tags(stripslashes($_POST['gangNAME'])))
                    : '';
    $_POST['gangDESC'] =
            (isset($_POST['gangDESC']))
                    ? $db->escape(strip_tags(stripslashes($_POST['gangDESC'])))
                    : '';
    $q =
            $db->query(
                    "SELECT `gangNAME`,`gangDESC`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $_POST['gangNAME'] && $_POST['gangDESC'])
    {
        staff_csrf_stdverify('staff_gangs_edit_name',
                "staff_gangs.php?action=gedit_name&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangNAME` = '{$_POST['gangNAME']}',
                 `gangDESC` = '{$_POST['gangDESC']}'
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add("{$ir['username']} edited gang ID $gang's name and/or description");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_name');
        echo "
        <h3>Gang Management: Name/Description</h3>
        Editing the gang: {$r['gangNAME']}
        <br />
        <form action='staff_gangs.php?action=gedit_name&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>Name:</td>
        			<td align='left'>
        				<input type='text' name='gangNAME' value='{$r['gangNAME']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Description:</td>
        			<td align='left'>
        				<textarea rows='7' cols='40' name='gangDESC'>{$r['gangDESC']}</textarea>
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_edit_prefix(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : 0;
    $_POST['gangPREF'] =
            (isset($_POST['gangPREF']) && strlen($_POST['gangPREF']) <= 5)
                    ? $db->escape(strip_tags(stripslashes($_POST['gangPREF'])))
                    : '';
    $q =
            $db->query(
                    "SELECT `gangNAME`, `gangPREF`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $_POST['gangPREF'])
    {
        staff_csrf_stdverify('staff_gangs_edit_prefix',
                "staff_gangs.php?action=gedit_prefix&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangPREF` = '{$_POST['gangPREF']}'
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add("{$ir['username']} edited gang ID $gang's prefix");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_prefix');
        echo "
        <h3>Gang Management: Prefix</h3>
        Editing the gang: {$r['gangNAME']}<br />
        <form action='staff_gangs.php?action=gedit_prefix&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>Prefix:</td>
        			<td align='left'>
        				<input type='text' name='gangPREF' value='{$r['gangPREF']}' maxlength='5' />
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_edit_finances(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : 0;
    $money =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 0;
    $crystals =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    $reason =
            (isset($_POST['reason'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $respect =
            (isset($_POST['respect']) && is_numeric($_POST['respect']))
                    ? abs(intval($_POST['respect'])) : 0;
    $q =
            $db->query(
                    "SELECT `gangNAME`, `gangMONEY`, `gangCRYSTALS`,
                     `gangRESPECT`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $reason)
    {
        staff_csrf_stdverify('staff_gangs_edit_finances',
                "staff_gangs.php?action=gedit_finances&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangMONEY` = $money, `gangCRYSTALS` = $crystals,
                 `gangRESPECT` = $respect
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add(
                "{$ir['username']} edited gang ID $gang's finances with the reason $reason");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_finances');
        echo "
        <h3>Gang Management: Financial Details</h3>
        Editing the gang: {$r['gangNAME']}<br />
        <form action='staff_gangs.php?action=gedit_finances&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>Money:</td>
        			<td align='left'>
        				<input type='text' name='money' value='{$r['gangMONEY']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Crystals:</td>
        			<td align='left'>
        				<input type='text' name='crystals' value='{$r['gangCRYSTALS']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Respect:</td>
        			<td align='left'>
        				<input type='text' name='respect' value='{$r['gangRESPECT']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Reason for editing:</td>
        			<td align='left'>
        				<input type='text' name='reason' value='' />
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_edit_staff(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : 0;
    $president =
            (isset($_POST['president']) && is_numeric($_POST['president']))
                    ? abs(intval($_POST['president'])) : '';
    $vicepres =
            (isset($_POST['vicepres']) && is_numeric($_POST['vicepres']))
                    ? abs(intval($_POST['vicepres'])) : '';
    $reason =
            (isset($_POST['reason'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $q =
            $db->query(
                    "SELECT `gangNAME`, `gangPRESIDENT`, `gangVICEPRES`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $reason && $president && $vicepres)
    {
        staff_csrf_stdverify('staff_gangs_edit_staff',
                "staff_gangs.php?action=gedit_staff&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangPRESIDENT` = $president,
                 `gangVICEPRES` = $vicepres
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add(
                "{$ir['username']} edited gang ID $gang's staff with the reason $reason");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_staff');
        echo "
        <h3>Gang Management: Staff</h3>
        Editing the gang: {$r['gangNAME']}<br />
        <form action='staff_gangs.php?action=gedit_staff&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>President:</td>
        			<td align='left'>
        				<input type='text' name='president' value='{$r['gangPRESIDENT']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Vice-President:</td>
        			<td align='left'>
        				<input type='text' name='vicepres' value='{$r['gangVICEPRES']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Reason for editing:</td>
        			<td align='left'>
        				<input type='text' name='reason' value='' />
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_edit_capacity(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : 0;
    $capacity =
            (isset($_POST['capacity']) && is_numeric($_POST['capacity']))
                    ? abs(intval($_POST['capacity'])) : '';
    $reason =
            (isset($_POST['reason'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $q =
            $db->query(
                    "SELECT `gangNAME`, `gangCAPACITY`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $reason && $capacity)
    {
        staff_csrf_stdverify('staff_gangs_edit_capacity',
                "staff_gangs.php?action=gedit_capacity&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangCAPACITY` = $capacity
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add(
                "{$ir['username']} edited gang ID $gang's capacity with the reason $reason");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_capacity');
        echo "
        <h3>Gang Management: Capacity</h3>
        Editing the gang: {$r['gangNAME']}<br />
        <form action='staff_gangs.php?action=gedit_capacity&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>Capacity:</td>
        			<td align='left'>
        				<input type='text' name='capacity' value='{$r['gangCAPACITY']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Reason for editing:</td>
        			<td align='left'>
        				<input type='text' name='reason' value='' />
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_edit_crime(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : 0;
    $crime =
            (isset($_POST['crime']) && is_numeric($_POST['crime']))
                    ? abs(intval($_POST['crime'])) : '';
    $chours =
            (isset($_POST['chours']) && is_numeric($_POST['chours']))
                    ? abs(intval($_POST['chours'])) : '';
    $reason =
            (isset($_POST['reason'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $q =
            $db->query(
                    "SELECT `gangNAME`, `gangCRIME`, `gangCHOURS`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $reason && $crime && $chours)
    {
        staff_csrf_stdverify('staff_gangs_edit_crime',
                "staff_gangs.php?action=gedit_crime&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangCRIME` = $crime, `gangCHOURS` = $chours
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add(
                "{$ir['username']} edited gang ID $gang's organised crime with the reason $reason");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_crime');
        echo "
        <h3>Gang Management: Organised Crimes</h3>
        Editing the gang: {$r['gangNAME']}<br />
        <form action='staff_gangs.php?action=gedit_crime&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>Crime ID:</td>
        			<td align='left'>
        				<input type='text' name='crime' value='{$r['gangCRIME']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Crime Hours Left:</td>
        			<td align='left'>
        				<input type='text' name='chours' value='{$r['gangCHOURS']}' />
        			</td>
        		</tr>
        		<tr>
        			<td align='right'>Reason for editing:</td>
        			<td align='left'>
        				<input type='text' name='reason' value='' />
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}

/**
 * @return void
 */
function admin_gang_edit_ament(): void
{
    global $db, $ir, $h;
    $gang =
            (isset($_GET['gang']) && is_numeric($_GET['gang']))
                    ? abs(intval($_GET['gang'])) : '';
    $_POST['gangAMENT'] =
            (isset($_POST['gangAMENT']))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['gangAMENT']))) : '';
    $q =
            $db->query(
                    "SELECT `gangNAME`, `gangAMENT`
                     FROM `gangs`
                     WHERE `gangID` = $gang");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid gang.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($gang && $_POST['gangAMENT'])
    {
        staff_csrf_stdverify('staff_gangs_edit_ament',
                "staff_gangs.php?action=gedit_ament&amp;gang={$gang}");
        $db->query(
                "UPDATE `gangs`
                 SET `gangAMENT` = '{$_POST['gangAMENT']}'
                 WHERE `gangID` = $gang");
        echo 'Gang has been successfully modified.<br />
        &gt; <a href="staff_gangs.php?action=gedit">Go Back</a>';
        stafflog_add("{$ir['username']} edited gang ID $gang's announcement");
        $h->endpage();
        exit;
    }
    else
    {
        $csrf = request_csrf_html('staff_gangs_edit_ament');
        echo "
        <h3>Gang Management: Announcement</h3>
        Editing the gang: {$r['gangNAME']}<br />
        <form action='staff_gangs.php?action=gedit_ament&amp;gang=$gang' method='post'>
        <table width='50%' cellspacing='1' class='table'>
        		<tr>
        			<td align='right'>Announcement:</td>
        			<td align='left'>
        				<textarea rows='7' cols='40' name='gangAMENT'>{$r['gangAMENT']}</textarea>
        			</td>
        		</tr>
        		<tr>
        			<td align='center' colspan='2'>
                        {$csrf}
                        <input type='submit' value='Edit' />
                    </td>
        		</tr>
        </table>
        </form>
           ";
    }
}
$h->endpage();
