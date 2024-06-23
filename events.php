<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
if (!isset($_GET['delete']))
{
    $_GET['delete'] = 0;
}
$_GET['delete'] = abs((int) $_GET['delete']);
if ($_GET['delete'] > 0)
{
    $d_c =
            $db->query(
                    "SELECT COUNT(`evUSER`)
                     FROM `events`
                     WHERE `evID` = {$_GET['delete']}
                     AND `evUSER` = {$userid}");
    if ($db->fetch_single($d_c) == 0)
    {
        echo '<span style="font-weight:bold;">Event doesn\'t exist</span><br />';
    }
    else
    {
        $db->query(
                "DELETE FROM `events`
                 WHERE `evID` = {$_GET['delete']}
                 AND `evUSER` = {$userid}");
        echo '<span style="font-weight:bold;">Event Deleted</span><br />';
    }
    $db->free_result($d_c);
}
if (isset($_GET['delall']) && $_GET['delall'])
{
    $delall_verf = request_csrf_code('events_delall');
    echo "
	This will delete all your events.<br />
	There is <b>NO</b> undo, so be sure.<br />
	&gt; <a href='events.php?delall2=1&amp;verf={$delall_verf}'>Yes,
		delete all my events</a><br />
	&gt; <a href='events.php'>No, go back</a><br />
   	";
    $h->endpage();
    exit;
}
if (isset($_GET['delall2']) && $_GET['delall2'])
{
    if (!isset($_GET['verf'])
            || !verify_csrf_code('events_delall', stripslashes($_GET['verf'])))
    {
        echo '<h3>Error</h3><hr />
    This action has been blocked for your security.<br />
    You should submit this action fast,
    	to ensure that it is really you doing it.<br />
    &gt; <a href="events.php?delall=1">Try Again</a>';
        $h->endpage();
        exit;
    }
    $am =
            $db->fetch_single(
                    $db->query(
                            "SELECT COUNT(`evID`)
                             FROM `events`
                             WHERE `evUSER` = $userid"));
    if ($am == 0)
    {
        echo 'You have no events to delete.<br />
        	  &gt; <a href="events.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $db->query("DELETE FROM `events`
    			WHERE `evUSER` = $userid");
    echo "
All <b>{$am}</b> events you had were deleted.<br />
<br />&gt; <a href='events.php'>Go Back</a>
   ";
    $h->endpage();
    exit;
}
echo "
<b>Latest 10 events</b>
<hr />
&gt; <a href='events.php?delall=1'>Delete All Events</a>
<hr />
   ";
$q =
        $db->query(
                "SELECT `evTIME`, `evREAD`, `evTEXT`, `evID`
                FROM `events`
                WHERE `evUSER` = $userid
        		ORDER BY `evTIME` DESC
        		LIMIT 10");
echo "
<table width=75% cellspacing=1 class='table'>
		<tr style='background:gray;'>
	<th>Time</th>
	<th>Event</th>
	<th>Links</th>
		</tr>
   ";
while ($r = $db->fetch_row($q))
{
    echo '<tr>
			<td>' . date('F j Y, g:i:s a', (int)$r['evTIME']);
    if (!$r['evREAD'])
    {
        echo '<br /><b>New!</b>';
    }
    echo "	</td>
			<td>{$r['evTEXT']}</td>
			<td><a href='events.php?delete={$r['evID']}'>Delete</a></td>
		</tr>";
}
echo '</table>';
$db->free_result($q);
if ($ir['new_events'] > 0)
{
    $db->query(
            "UPDATE `events`
    		 SET `evREAD` = 1
    		 WHERE `evUSER` = $userid");
    $db->query(
            "UPDATE `users`
    		 SET `new_events` = 0
    		 WHERE `userid` = $userid");
}
$h->endpage();
