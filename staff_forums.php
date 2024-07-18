<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $ir, $h;
require_once('sglobals.php');
if (!check_access('manage_forums')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'addforum':
    addforum();
    break;
case 'editforum':
    editforum();
    break;
case 'delforum':
    delforum();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function addforum(): void
{
    global $db, $h;
    $name =
            (isset($_POST['name'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['name']))
                    ? $db->escape(strip_tags(stripslashes($_POST['name'])))
                    : '';
    $desc =
            (isset($_POST['desc']))
                    ? $db->escape(strip_tags(stripslashes($_POST['desc'])))
                    : '';
    $auth =
            (isset($_POST['auth'])
                    && in_array($_POST['auth'], ['staff', 'public'], true))
                    ? $_POST['auth'] : 'public';
    if ($auth && $desc && $name)
    {
        staff_csrf_stdverify('staff_addforum',
                'staff_forums.php?action=addforum');
        $q =
                $db->query(
                        "SELECT COUNT(`ff_id`)
                         FROM `forum_forums`
                         WHERE `ff_name` = '{$name}'");
        if ($db->fetch_single($q))
        {
            $db->free_result($q);
            echo 'Forum name already exists, please try another.<br />
            &gt; <a href="staff_forums.php?action=addforum">Go back</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q);
        $db->query(
                "INSERT INTO `forum_forums`
                 (`ff_name`, `ff_desc`, `ff_auth`, `ff_lp_poster_name`,
                 `ff_lp_t_name`)
                 VALUES('$name', '$desc', '$auth', 'N/A', 'N/A')");
        echo 'Forum ' . $name
                . ' added to the game.<br />&gt; <a href="staff.php">Goto Main</a>';
        stafflog_add('Created ' . $auth . ' Forum ' . $name);
    }
    else
    {
        $csrf = request_csrf_html('staff_addforum');
        echo "
        <h3>Add Forum</h3>
        <hr />
        <form action='staff_forums.php?action=addforum' method='post'>
        	Name: <input type='text' name='name' />
        <br />
        	Description: <input type='text' name='desc' />
        <br />
        	Authorization:
        		<input type='radio' name='auth' value='public' checked='checked' /> Public
        		<input type='radio' name='auth' value='staff' /> Staff Only
        <br />
        	{$csrf}
        	<input type='submit' value='Add Forum' />
        </form>
            ";
    }
}

/**
 * @return void
 */
function edit_forum_select(): void
{
    $csrf = request_csrf_html('staff_editforum1');
    echo "
        <h3>Editing a Forum</h3><hr />
        <form action='staff_forums.php?action=editforum' method='post'>
        	<input type='hidden' name='step' value='1' />
        	Forum: " . forum2_dropdown('id')
        . "<br />
            {$csrf}
        	<input type='submit' value='Edit Forum' />
        </form>
           ";
}

/**
 * @return void
 */
function edit_forum_configure(): void
{
    global $db, $h;
    $_POST['id'] =
        (isset($_POST['id']) && is_numeric($_POST['id']))
            ? abs(intval($_POST['id'])) : '';
    if (empty($_POST['id']))
    {
        echo 'Invalid input.<br />
            &gt; <a href="staff_forums.php?action=editforum">Go back</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_editforum1',
        'staff_forums.php?action=editforum');
    $q =
        $db->query(
            "SELECT `ff_auth`, `ff_name`, `ff_desc`
                         FROM `forum_forums`
                         WHERE `ff_id` = {$_POST['id']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Forum id doesn\'t exist.<br />
            &gt; <a href="staff_forums.php?action=editforum">Go back</a>';
        $h->endpage();
        exit;
    }
    $old = $db->fetch_row($q);
    $db->free_result($q);
    $check_p = ($old['ff_auth'] == 'public') ? 'checked' : '';
    $check_s = ($old['ff_auth'] == 'staff') ? 'checked' : '';
    $csrf = request_csrf_html('staff_editforum2');
    echo '
        <h3>Editing a Forum</h3><hr />
        <form action="staff_forums.php?action=editforum" method="post">
        	<input type="hidden" name="step" value="2" />
        	<input type="hidden" name="id" value="' . $_POST['id']
        . '" />
        	Name: <input type="text" name="name" value="' . $old['ff_name']
        . '" />
        <br />
        	Description: <input type="text" name="desc" value="'
        . $old['ff_desc']
        . '" />
        <br />
        Authorization: <input type="radio" name="auth" value="public" '
        . $check_p
        . ' /> Public <input type="radio" name="auth" value="staff" '
        . $check_s . ' /> Staff
        <br />
        	' . $csrf
        . '
        	<input type="submit" value="Edit Forum" />
        </form>
           ';
}

/**
 * @return void
 */
function edit_forum_do(): void
{
    global $db, $h;
    $name =
        (isset($_POST['name'])
            && preg_match(
                "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['name']))
            ? $db->escape(strip_tags(stripslashes($_POST['name'])))
            : '';
    $desc =
        (isset($_POST['desc']))
            ? $db->escape(strip_tags(stripslashes($_POST['desc'])))
            : '';
    $auth =
        (isset($_POST['auth'])
            && in_array($_POST['auth'], ['staff', 'public']))
            ? $_POST['auth'] : 'public';
    $_POST['id'] =
        (isset($_POST['id']) && is_numeric($_POST['id']))
            ? abs(intval($_POST['id'])) : '';
    if (empty($_POST['id']) || empty($name) || empty($desc))
    {
        echo 'Invalid input.<br />
            &gt; <a href="staff_forums.php?action=editforum">Go back</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_editforum2',
        'staff_forums.php?action=editforum');
    $q =
        $db->query(
            "SELECT COUNT(`ff_id`)
                         FROM `forum_forums`
                         WHERE `ff_name` = '{$name}'
                         AND `ff_id` != {$_POST['id']}");
    if ($db->fetch_single($q) > 0)
    {
        $db->free_result($q);
        echo 'Forum name already exists.<br />
            &gt; <a href="staff_forums.php?action=editforum">Go back</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($q);
    $q =
        $db->query(
            "SELECT COUNT(`ff_id`)
                         FROM `forum_forums`
                         WHERE `ff_id` = {$_POST['id']}");
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'Forum id doesn\'t exist.<br />
            &gt; <a href="staff_forums.php?action=editforum">Go back</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($q);
    $db->query(
        "UPDATE `forum_forums`
                 SET `ff_desc` = '$desc', `ff_name` = '$name',
                 `ff_auth` = '$auth'
                 WHERE `ff_id` = {$_POST['id']}");
    echo 'Forum ' . $name
        . ' was edited successfully.<br />
                &gt; <a href="staff.php">Goto Main</a>';
    stafflog_add("Edited forum $name");
}
/**
 * @return void
 */
function editforum(): void
{
    global $db, $h;
    if (!isset($_POST['step']))
    {
        $_POST['step'] = '0';
    }
    switch ($_POST['step'])
    {
    case '2':
        edit_forum_do();
        break;
    case '1':
        edit_forum_configure();
        break;
    default:
        edit_forum_select();
        break;
    }
}

/**
 * @return void
 */
function delforum(): void
{
    global $db, $h;
    $_POST['forum'] =
            (isset($_POST['forum']) && is_numeric($_POST['forum']))
                    ? abs(intval($_POST['forum'])) : '';
    $_POST['forum2'] =
            (isset($_POST['forum2']) && is_numeric($_POST['forum2']))
                    ? abs(intval($_POST['forum2'])) : '';
    if ($_POST['forum'] && $_POST['forum2'])
    {
        staff_csrf_stdverify('staff_delforum',
                'staff_forums.php?action=delforum');
        if ($_POST['forum'] == $_POST['forum2'])
        {
            echo 'Fields are the same.<br />
            &gt; <a href="staff_forums.php?action=delforum">Go back</a>';
            $h->endpage();
            exit;
        }
        $q =
                $db->query(
                        "SELECT COUNT(`ff_id`)
                         FROM `forum_forums`
                         WHERE `ff_id` IN({$_POST['forum']},
                         {$_POST['forum2']})");
        if ($db->fetch_single($q) < 2)
        {
            $db->free_result($q);
            echo 'One of the two forums selected doesn\'t exist.<br />
            &gt; <a href="staff_forums.php?action=delforum">Go back</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q);
        $db->query(
                "UPDATE `forum_posts`
                 SET `fp_forum_id` = {$_POST['forum2']}
                 WHERE `fp_forum_id` = {$_POST['forum']}");
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_forum_id` = {$_POST['forum2']}
                 WHERE `ft_forum_id` = {$_POST['forum']}");
        recache_forum($_POST['forum2']);
        $q =
                $db->query(
                        "SELECT `ff_name`
                         FROM `forum_forums`
                         WHERE `ff_id` = {$_POST['forum']}");
        $old = $db->fetch_single($q);
        $db->free_result($q);
        $db->query(
                "DELETE FROM `forum_forums`
                 WHERE `ff_id` = {$_POST['forum']}");
        echo 'Forum ' . $old
                . ' deleted.<br />
        &gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Deleted forum {$old}");
    }
    else
    {
        $csrf = request_csrf_html('staff_delforum');
        echo "
        <script type='text/javascript'>
        function checkme()
        {
        	if(document.theform.forum.value == document.theform.forum2.value)
        	{
        		alert('You cannot select the same forum to move the posts to.');
        		return false;
        	}
        	return true;
        }
        </script>
        <h3>Delete Forum</h3>
        <hr />
        Deleting a forum is permanent - be sure.
        <form action='staff_forums.php?action=delforum' method='post' name='theform' onsubmit='return checkme();'>
        	Forum: " . forum2_dropdown()
                . '
        <br />
        	Move posts &amp; topics in the deleted forum to: '
                . forum2_dropdown('forum2')
                . "
        <br />
        	{$csrf}
        	<input type='submit' value='Delete Forum' />
        </form>";
    }
}

/**
 * @param $forum
 * @return void
 */
function recache_forum($forum): void
{
    global $db;
    $forum = abs((int) $forum);
    if ($forum <= 0)
    {
        return;
    }
    echo "Recaching forum ID $forum ... ";
    $q =
            $db->query(
                    "SELECT `fp_poster_name`, `fp_time`, `fp_poster_id`,
                     `ft_name`, `ft_id`
                     FROM `forum_posts` AS `p`
                     LEFT JOIN `forum_topics` AS `t`
                     ON `p`.`fp_topic_id` = `t`.`ft_id`
                     WHERE `p`.`fp_forum_id` = {$forum}
                     ORDER BY `p`.`fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        $db->query(
                "UPDATE `forum_forums`
                 SET `ff_lp_time` = 0, `ff_lp_poster_id` = 0,
                 `ff_lp_poster_name` = 'N/A', `ff_lp_t_id` = 0,
                 `ff_lp_t_name` = 'N/A', `ff_posts` = 0, `ff_topics` = 0
                  WHERE `ff_id` = {$forum}");
    }
    else
    {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $tn = $db->escape($r['ft_name']);
        $pn = $db->escape($r['fp_poster_name']);
        $posts_q =
                $db->query(
                        "SELECT COUNT(`fp_id`)
        					   FROM `forum_posts`
        					   WHERE `fp_forum_id` = {$forum}");
        $posts = $db->fetch_single($posts_q);
        $db->free_result($posts_q);
        $topics_q =
                $db->query(
                        "SELECT COUNT(`ft_id`)
        					   FROM `forum_topics`
        					   WHERE `ft_forum_id` = {$forum}");
        $topics = $db->fetch_single($topics_q);
        $db->free_result($topics_q);
        $db->query(
                "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$r['fp_time']},
                 `ff_lp_poster_id` = {$r['fp_poster_id']},
                 `ff_lp_poster_name` = '$pn', `ff_lp_t_id` = {$r['ft_id']},
                 `ff_lp_t_name` = '$tn', `ff_posts` = $posts,
                 `ff_topics` = $topics
                 WHERE `ff_id` = {$forum}");
    }
    echo ' ... Done<br />';
}
$h->endpage();
