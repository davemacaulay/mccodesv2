<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $ir, $h;
require_once('globals.php');
if ($ir['donatordays'] == 0)
{
    echo 'This feature is for donators only.';
    $h->endpage();
    exit;
}
echo '<h3>Friends List</h3>';
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'add':
    add_friend();
    break;
case 'remove':
    remove_friend();
    break;
case 'ccomment':
    change_comment();
    break;
default:
    friends_list();
    break;
}

/**
 * @return void
 */
function friends_list(): void
{
    global $db, $ir, $userid;
    echo "
<a href='friendslist.php?action=add'>&gt; Add an friend</a><br />
These are the people on your friends list.
<br />
    {$ir['friend_count']} people have added you to their list.
<br />
Most hated: [";
    $q2r =
            $db->query(
                'SELECT `username`, `userid`
                     FROM `users`
                     ORDER BY `friend_count` DESC
                     LIMIT 5');
    $r = 0;
    while ($r2r = $db->fetch_row($q2r))
    {
        $r++;
        if ($r > 1)
        {
            echo ' | ';
        }
        echo "<a href='viewuser.php?u={$r2r['userid']}'>{$r2r['username']}</a>";
    }
    $db->free_result($q2r);
    echo ']
<table width="90%" class="table" cellspacing="1">
		<tr style="background:gray">
			<th>ID</th>
			<th>Name</th>
			<th>Mail</th>
			<th>Attack</th>
			<th>Remove</th>
			<th>Comment</th>
			<th>Change Comment</th>
			<th>Online?</th>
		</tr>
   ';
    $q =
            $db->query(
                    "SELECT `fl_COMMENT`, `fl_ID`, `laston`, `donatordays`,
                     `username`, `userid`
                     FROM `friendslist` AS `fl`
                     LEFT JOIN `users` AS `u` ON `fl`.`fl_ADDED` = `u`.`userid`
                     WHERE `fl`.`fl_ADDER` = $userid
                     ORDER BY `u`.`username` ASC");
    while ($r = $db->fetch_row($q))
    {
        $on =
                ($r['laston'] >= (($_SERVER['REQUEST_TIME'] - 15) * 60))
                        ? '<span style="color: green; font-weight: bold;">Online</font>'
                        : '<span style="color: red; font-weight: bold;">Offline</font>';
        $d = '';
        if ($r['donatordays'] > 0)
        {
            $r['username'] =
                    "<span style='color: red;'>{$r['username']}</span>";
            $d =
                    "<img src='donator.gif' alt='Donator: {$r['donatordays']} Days Left'
                    	 title='Donator: {$r['donatordays']} Days Left' />";
        }
        if (!$r['fl_COMMENT'])
        {
            $r['fl_COMMENT'] = 'N/A';
        }
        echo "
		<tr>
			<td>{$r['userid']}</td>
			<td><a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> $d</td>
			<td><a href='mailbox.php?action=compose&amp;ID={$r['userid']}'>Mail</a></td>
			<td><a href='attack.php?ID={$r['userid']}'>Attack</a></td>
			<td><a href='friendslist.php?action=remove&amp;f={$r['fl_ID']}'>Remove</a></td>
			<td>" . strip_tags($r['fl_COMMENT'])
                . "</td>
            <td><a href='friendslist.php?action=ccomment&f={$r['fl_ID']}'>Change</a></td>
			<td>$on</td>
		</tr>
   ";
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function add_friend(): void
{
    global $db, $userid;
    $_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : '';
    $_POST['comment'] =
            (isset($_POST['comment']) && is_string($_POST['comment']))
                    ? $db->escape(strip_tags(stripslashes($_POST['comment'])))
                    : '';

    if ($_POST['ID'])
    {
        $qc =
                $db->query(
                        "SELECT COUNT(`fl_ADDER`)
                         FROM `friendslist`
                         WHERE `fl_ADDER` = $userid
                         AND `fl_ADDED` = {$_POST['ID']}");
        $dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
        $q =
                $db->query(
                        "SELECT `username`
                         FROM `users`
                         WHERE `userid` = {$_POST['ID']}");
        if ($dupe_count > 0)
        {
            echo 'You cannot add the same person twice.';
        }
        elseif ($userid == $_POST['ID'])
        {
            echo 'You cannot be so lonely that you have to try and add yourself.';
        }
        elseif ($db->num_rows($q) == 0)
        {
            echo "Oh no, you're trying to add a ghost.";
        }
        else
        {
            $db->query(
                    "INSERT INTO `friendslist`
                     VALUES(NULL, $userid, {$_POST['ID']}, '{$_POST['comment']}')");
            $r = $db->fetch_row($q);
            $db->query(
                    "UPDATE `users`
                     SET `friend_count` = `friend_count` + 1
                     WHERE `userid` = {$_POST['ID']}");
            echo "{$r['username']} was added to your friends list.<br />
					<a href='friendslist.php'>&gt; Back</a>";
        }
        $db->free_result($q);
    }
    else
    {
        $_GET['ID'] =
                (isset($_GET['ID']) && is_numeric($_GET['ID']))
                        ? abs(intval($_GET['ID'])) : '';
        echo "
Adding an friend!
<form action='friendslist.php?action=add' method='post'>
	Friend's ID: <input type='text' name='ID' value='{$_GET['ID']}' /><br />
	Comment (optional): <br />
	<textarea name='comment' rows='7' cols='40'></textarea><br />
	<input type='submit' value='Add Friend' />
</form>
        ";
    }

}

/**
 * @return void
 */
function remove_friend(): void
{
    global $db, $userid, $h;
    $_GET['f'] =
            (isset($_GET['f']) && is_numeric($_GET['f']))
                    ? abs(intval($_GET['f'])) : '';
    if (empty($_GET['f']))
    {
        echo '
You didn\'t select a real friend.<br />
&gt; <a href="friendslist.php">Back</a>
   ';
        $h->endpage();
        exit;
    }

    $q =
            $db->query(
                    "SELECT `fl_ADDED`
                     FROM `friendslist`
                     WHERE `fl_ID` = {$_GET['f']} AND `fl_ADDER` = $userid");
    if ($db->num_rows($q) == 0)
    {
        echo 'Listing doesn\'t exist.';
        $h->endpage();
        exit;
    }
    $r = $db->fetch_row($q);
    $db->query(
            "DELETE FROM `friendslist`
            WHERE `fl_ID` = {$_GET['f']} AND `fl_ADDER` = $userid");
    $db->query(
            "UPDATE `users`
             SET `friend_count` = `friend_count` - 1
             WHERE `userid` = {$r['fl_ADDED']}");
    echo "
Friends list entry removed!<br />
<a href='friendslist.php'>&gt; Back</a>
   ";
}

/**
 * @return void
 */
function change_comment(): void
{
    global $db, $userid, $h;
    $_POST['f'] =
            (isset($_POST['f']) && is_numeric($_POST['f']))
                    ? abs(intval($_POST['f'])) : '';
    $_POST['comment'] =
            $db->escape(strip_tags(stripslashes($_POST['comment'])));
    if ($_POST['comment'] && $_POST['f'])
    {
        $q =
                $db->query(
                        "SELECT COUNT(`fl_ID`)
                     FROM `friendslist`
                     WHERE `fl_ID` = {$_GET['f']} AND `fl_ADDER` = $userid");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Listing doesn\'t exist.';
            $h->endpage();
            exit;
        }
        $db->free_result($q);
        $db->query(
                "UPDATE `friendslist`
                 SET `fl_COMMENT` = '{$_POST['comment']}'
                 WHERE `fl_ID` = {$_POST['f']} AND `fl_ADDER` = $userid");
        echo "
Comment for friend changed!<br />
<a href='friendslist.php'>&gt; Back</a>
   ";
    }
    else
    {
        $_GET['f'] =
                (isset($_GET['f']) && is_numeric($_GET['f']))
                        ? abs(intval($_GET['f'])) : '';
        if (empty($_GET['f']))
        {
            echo "
Invalid friend.<br />
<a href='friendslist.php'>&gt; Back</a>
   ";
            $h->endpage();
            exit;
        }
        $q =
                $db->query(
                        "SELECT `fl_COMMENT`
                         FROM `friendslist`
                         WHERE `fl_ID` = {$_GET['f']}
                         AND `fl_ADDER` = $userid");
        if ($db->num_rows($q))
        {
            $r = $db->fetch_row($q);
            $comment =
                    stripslashes(
                            htmlentities($r['fl_COMMENT'], ENT_QUOTES,
                                    'ISO-8859-1'));
            echo "
Changing a comment.
<form action='friendslist.php?action=ccomment' method='post'>
		<input type='hidden' name='f' value='{$_GET['f']}' /><br />
		Comment: <br />
		<textarea rows='7' cols='40' name='comment'>$comment</textarea><br />
		<input type='submit' value='Change Comment' />
</form>
    ";
        }
        else
        {
            echo 'It would be impossible to edit something which isn\'t yours.<br /> &gt; <a href="index.php">Go Home</a>';
        }
    }
}
$h->endpage();
