<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

$jobquery = 1;
global $db, $ir, $userid, $h;
require_once('globals.php');
$_GET['interview'] =
        (isset($_GET['interview']) && is_numeric($_GET['interview']))
                ? abs(intval($_GET['interview'])) : '';
if (!$ir['job'])
{
    if (!$_GET['interview'])
    {
        echo '
		You do not yet have a job. A list of jobs is available below.
		<br />
   		';
        $q =
                $db->query(
                    'SELECT `jID`,`jDESC`,`jNAME`
        				 FROM `jobs`');
        while ($r = $db->fetch_row($q))
        {
            echo "
			&gt; {$r['jNAME']} - {$r['jDESC']} - <a href='job.php?interview={$r['jID']}'>Go to interview</a>
			<br />
   			";
        }
        $db->free_result($q);
    }
    else
    {
        $q =
                $db->query(
                        "SELECT `jOWNER`, `jrID`, `jrIQN`, `jrLABOURN`,
                         `jrSTRN`
                         FROM `jobs` AS `j`
                         INNER JOIN `jobranks` AS `jr`
                         ON `j`.`jFIRST` = `jr`.`jrID`
                         WHERE `j`.`jID` = {$_GET['interview']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            print 'Invalid job specified.';
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        echo "
        {$r['jOWNER']}: So {$ir['username']}, you were looking for a job with us?
		<br />
        {$ir['username']}: Yes please!
		<br />
   		";
        if ($ir['strength'] >= $r['jrSTRN']
                && $ir['labour'] >= $r['jrLABOURN']
                && $ir['IQ'] >= $r['jrIQN'])
        {
            $db->query(
                    "UPDATE `users`
                     SET `job` = {$_GET['interview']}, `jobrank` = {$r['jrID']}
                     WHERE `userid` = $userid");
            echo "
            {$r['jOWNER']}: Okay {$ir['username']}, we're good to go, see you tomorrow.
			<br />
            {$ir['username']}: Thanks!
			<br />
			&gt; <a href='index.php'>Go Home</a>
     		";
        }
        else
        {
            echo "
            {$r['jOWNER']}: Sorry {$ir['username']}, you're not far enough in the game to work in this job. You'll need:
   			";
            if ($ir['strength'] < $r['jrSTRN'])
            {
                $s = $r['jrSTRN'] - $ir['strength'];
                echo " $s more strength, ";
            }
            if ($ir['labour'] < $r['jrLABOURN'])
            {
                $s = $r['jrLABOURN'] - $ir['labour'];
                echo " $s more labour, ";
            }
            if ($ir['IQ'] < $r['jrIQN'])
            {
                $s = $r['jrIQN'] - $ir['IQ'];
                echo " $s more IQ, ";
            }
            echo "
			before you'll be able to work here!
			<br />
			&gt; <a href='index.php'>Go Home</a>
   			";
        }
    }
}
else
{
    if (!isset($_GET['action']))
    {
        $_GET['action'] = '';
    }
    switch ($_GET['action'])
    {
    case 'quit':
        quit_job();
        break;
    case 'promote':
        job_promote();
        break;
    default:
        job_index();
        break;
    }
}

/**
 * @return void
 */
function job_index(): void
{
    global $db, $ir;
    echo "
    <h3>Your Job</h3>
    You currently work in the {$ir['jNAME']}! You receive "
            . money_formatter($ir['jrPAY'])
            . " each day at 5pm!
    <br />
    You also receive {$ir['jrIQG']} IQ, {$ir['jrSTRG']} strength, and {$ir['jrLABOURG']} labour!
    <br />
    <table width='50%' cellspacing='1' class='table'>
    		<tr>
    			<td>Strength: {$ir['strength']}</td>
    			<td>IQ: {$ir['IQ']}</td>
    		</tr>
    		<tr>
    			<td>Labour: {$ir['labour']}</td>
    			<td>Job Rank: {$ir['jrNAME']}</td>
    		</tr>
    	</table>
    <b>Job Ranks</b>
    <br />
    <table width='75%' cellspacing='1' class='table'>
		<tr>
			<th>Title</th>
			<th>Pay</th>
			<th>Strength Reqd</th>
			<th>IQ Reqd</th>
			<th>Labour Reqd</th>
		</tr>
   	";
    $q =
            $db->query(
                    "SELECT `jrNAME`, `jrPAY`, `jrSTRN`, `jrIQN`, `jrLABOURN`
                     FROM `jobranks`
                     WHERE `jrJOB` = {$ir['job']}
                     ORDER BY `jrPAY` ASC");
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
			<td>{$r['jrNAME']}</td>
			<td>" . money_formatter($r['jrPAY'])
                . "</td>
			<td>{$r['jrSTRN']}</td>
			<td>{$r['jrIQN']}</td>
			<td>{$r['jrLABOURN']}</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo "
	</table>
	<br />
	&gt; <a href='job.php?action=promote'>Try To Get Promoted</a>
	<br />
	&gt; <a href='job.php?action=quit'>Quit</a>
   	";
}

/**
 * @return void
 */
function job_promote(): void
{
    global $db, $ir, $userid;
    $q =
            $db->query(
                    "SELECT `jrID`,`jrNAME`
                     FROM `jobranks`
                     WHERE `jrPAY` > {$ir['jrPAY']}
                     AND `jrSTRN` <= {$ir['strength']}
                     AND `jrLABOURN` <= {$ir['labour']}
                     AND `jrIQN` <= {$ir['IQ']} AND `jrJOB` = {$ir['job']}
                     ORDER BY `jrPAY` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0)
    {
        echo "
		Sorry, you cannot be promoted at this time.
		<br />
		&gt; <a href='job.php'>Go Back</a>
   		";
    }
    else
    {
        $r = $db->fetch_row($q);
        $db->query(
                "UPDATE `users`
                 SET `jobrank` = {$r['jrID']}
                 WHERE `userid` = $userid");
        echo "
		Congrats, you have been promoted to {$r['jrNAME']}.
		<br />
		&gt; <a href='job.php'>Go Back</a>
   		";
    }
    $db->free_result($q);
}

/**
 * @return void
 */
function quit_job(): void
{
    global $db, $userid;
    $db->query(
            "UPDATE `users`
             SET `job` = 0, `jobrank` = 0
             WHERE `userid` = $userid");
    echo "
	You have quit your job!
	<br />
	&gt; <a href='job.php'>Go Back</a>
   	";
}
$h->endpage();
