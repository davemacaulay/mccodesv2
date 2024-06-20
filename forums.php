<?php
/**
 * MCCodes Version 2.0.5b
 * Copyright (C) 2005-2012 Dabomstew
 * All rights reserved.
 *
 * Redistribution of this code in any form is prohibited, except in
 * the specific cases set out in the MCCodes Customer License.
 *
 * This code license may be used to run one (1) game.
 * A game is defined as the set of users and other game database data,
 * so you are permitted to create alternative clients for your game.
 *
 * If you did not obtain this code from MCCodes.com, you are in all likelihood
 * using it illegally. Please contact MCCodes to discuss licensing options
 * in this case.
 *
 * File: forums.php
 * Signature: 0e9352a7d04335e904df2f66bf571d11
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

$forums = 1;
require_once('globals.php');

class bbcode
{
    var $engine = "";

    function bbcode()
    {
        require "bbcode_engine.php";
        $this->engine = new bbcode_engine;
        $this->engine->cust_tag("/</", "&lt;");
        $this->engine->cust_tag("/>/", "&gt;");
        $this->engine->cust_tag("/\r\n/", "\n");
        $this->engine->cust_tag("/\r/", "\n");
        $this->engine->cust_tag("/\n/", "&nbrlb;");
        $this->engine->simple_bbcode_tag("b");
        $this->engine->simple_bbcode_tag("i");
        $this->engine->simple_bbcode_tag("u");
        $this->engine->simple_bbcode_tag("s");
        $this->engine->simple_bbcode_tag("sub");
        $this->engine->simple_bbcode_tag("sup");
        $this->engine->simple_bbcode_tag("big");
        $this->engine->simple_bbcode_tag("small");
        $this->engine->cust_tag("/\[ul\](.+?)\[\/ul\]/is",
                "<table><tr><td align='left'><ul>\\1</ul></td></tr></table>");
        $this->engine->cust_tag("/\[ol\](.+?)\[\/ol\]/is",
                "<table><tr><td align='left'><ol>\\1</ol></td></tr></table>");
        $this->engine->cust_tag("/\[list\](.+?)\[\/list\]/is",
                "<table><tr><td align='left'><ul>\\1</ul></td></tr></table>");
        $this->engine->cust_tag("/\[olist\](.+?)\[\/olist\]/is",
                "<table><tr><td align='left'><ol>\\1</ol></td></tr></table>");
        $this->engine->adv_bbcode_tag("item", "li");
        $this->engine->adv_option_tag("font", "font", "face");
        $this->engine->adv_option_tag("size", "font", "size");
        $this->engine->adv_option_tag("url", "a", "href");
        $this->engine->adv_option_tag("color", "font", "color");
        $this->engine->adv_option_tag("style", "span", "style");
        $this->engine->cust_tag("/\(c\)/", "&copy;");
        $this->engine->cust_tag("/\(tm\)/", "&#153;");
        $this->engine->cust_tag("/\(r\)/", "&reg;");
        $this->engine->adv_option_tag_em("email", "a", "href");
        $this->engine->adv_bbcode_att_em("email", "a", "href");
        $this->engine->cust_tag("/\[left\](.+?)\[\/left\]/i",
                "<div align='left'>\\1</div>");
        $this->engine->cust_tag("/\[center\](.+?)\[\/center\]/i",
                "<div align='center'>\\1</div>");
        $this->engine->cust_tag("/\[right\](.+?)\[\/right\]/i",
                "<div align='right'>\\1</div>");
        $this->engine->cust_tag("/\[quote=(.+?)\]/i",
                "<div class='quotetop'>QUOTE (\\1)</div><div class='quotemain'>");
        $this->engine->cust_tag("/\[quote\]/i",
                "<div class='quotetop'>QUOTE</div><div class='quotemain'>");
        $this->engine->cust_tag("/\[\/quote\]/i", "</div>");
        $this->engine->cust_tag("/\[code\](.+?)\[\/code\]/i",
                "<div class='codetop'>CODE</div><div class='codemain'><code>\\1</code></div>");
        $this->engine->cust_tag("/\[codebox\](.+?)\[\/codebox\]/i",
                "<div class='codetop'>CODE</div><div class='codemain' style='height:200px;white-space:pre;overflow:auto'>\\1</div>");
        $this->engine->cust_tag("/\[img=(.+?)\]/ie", "check_image('\\1')");
        $this->engine->cust_tag("/\[img](.+?)\[\/img\]/ie",
                "check_image('\\1')");
        $this->engine->cust_tag("/&nbrlb;/", "<br />");
        $this->engine->cust_tag("/\[userbox\]([0-9]+)\[\/userbox\]/ie",
                "userBox(\\1)");
        $this->engine->cust_tag("/\[hr\]/is", "<hr />");
        $this->engine->cust_tag("/\[\*\]/", "<li>");
    }

    function bbcode_parse($html)
    {
        $html =
                str_ireplace(
                        array("javascript:", "document.", "onClick",
                                "onDblClick", "onLoad", "onMouseOver",
                                "onBlur", "onChange", "onFocus", "onkeydown",
                                "onkeypress", "onkeyup", "onmousedown",
                                "onmouseup", 'onmouseout', 'onmousemove',
                                'onresize', 'onscroll'), "", $html);
        $html =
                str_replace(array('"', "'"), array("&quot;", "&#39;"), $html);
        $mf = $this->engine->parse_bbcode($this->quote_corrector($html));
        return $mf;
    }

    function quote_corrector($in)
    {
        $quotes = substr_count($in, "[/quote]");
        $quote_starts = substr_count($in, "[quote");
        if ($quote_starts > $quotes)
        {
            return $in . str_repeat("[/quote]", $quote_starts - $quotes);
        }
        elseif ($quotes > $quote_starts)
        {
            $so = 0;
            $poss = array();
            for ($i = 0; $i < $quotes; $i++)
            {
                $kx = strpos($in, "[/quote]", $so);
                $so = $kx;
                $poss[] = $kx;
            }
            while ($quotes > $quote_starts)
            {
                $num = $quotes - 1;
                $in =
                        substr($in, 0, $poss[$num])
                                . ($poss[$num] + 8 >= strlen($in) ? ""
                                        : substr($in, $poss[$num] + 8));
                $quotes--;
            }
            return $in;
        }
        else
        {
            return $in;
        }
    }
}

function check_image($src)
{
    if (strpos($src, ".php") !== false || strpos($src, ".asp") !== false
            || strpos($src, ".aspx") !== false
            || strpos($src, ".htm") !== false)
    {
        return "invalid image";
    }
    if (strpos($src, ".gif") === false && strpos($src, ".jpg") === false
            && strpos($src, ".png") === false
            && strpos($src, ".jpeg") === false)
    {
        return "invalid image";
    }

    if (strpos($src, "http://") !== 0)
    {
        $src = "http://" . $src;
    }
    $image = (@getimagesize($src));
    if (!is_array($image))
    {
        return 'Invalid Image.';
    }

    $alt_title = explode("/", $src);
    $the_title = $alt_title[count($alt_title) - 1];
    return "<img src='{$src}' title='{$the_title}' alt='{$alt_title}' />";
}

function forums_rank($tp)
{
    $new_rank = '#0 Inactive';
    $f_ranks =
            array(3 => '#1 Absolute Newbie', 7 => '#2 Newbie',
                    12 => '#3 Beginner', 18 => '#4 Not Experienced',
                    25 => '#5 Rookie', 50 => '#6 Average', 100 => '#7 Good',
                    200 => '#8 Very Good', 350 => '#9 Greater Than Average',
                    500 => '#10 Experienced', 750 => '#11 Highly Experienced',
                    1200 => '#12 Honoured', 1800 => '#13 Highly Hounoured',
                    2500 => '#14 Respect King', 5000 => '#15 True Champion');
    foreach ($f_ranks AS $fr_key => $fr_value)
    {
        if ($tp >= $fr_key)
        {
            $new_rank = $fr_value;
        }
    }
    return $new_rank;
}

$bbc = new bbcode;
echo "<h3>Forums</h3><hr />";
if ($ir['forumban'] > 0)
{
    echo "
<font color='red'><h3>! ERROR</h3>
You have been forum banned for {$ir['forumban']} days.<br />
<br />
<b>Reason: {$ir['fb_reason']}</font></b>
   ";
    die($h->endpage());
}
if (!isset($_GET['act']))
{
    $_GET['act'] = '';
}
if (isset($_GET['viewtopic']) && $_GET['act'] != 'quote')
{
    $_GET['act'] = 'viewtopic';
}
if (isset($_GET['viewforum']))
{
    $_GET['act'] = 'viewforum';
}
if (isset($_GET['reply']))
{
    $_GET['act'] = 'reply';
}
if (isset($_GET['empty']) && $_GET['empty'] == 1 && isset($_GET['code'])
        && $_GET['code'] === 'kill' && isset($_SESSION['owner'])
        && $_SESSION['owner'] > 0)
{
    emptyallforums();
}
switch ($_GET['act'])
{
case 'viewforum':
    viewforum();
    break;
case 'viewtopic':
    viewtopic();
    break;
case 'reply':
    reply();
    break;
case 'newtopicform':
    newtopicform();
    break;
case 'newtopic':
    newtopic();
    break;
case 'quote':
    quote();
    break;
case 'edit':
    edit();
    break;
case 'move':
    move();
    break;
case 'editsub':
    editsub();
    break;
case 'lock':
    lock();
    break;
case 'delepost':
    delepost();
    break;
case 'deletopic':
    deletopic();
    break;
case 'pin':
    pin();
    break;
case 'recache':
    if (isset($_GET['forum']))
    {
        recache_forum($_GET['forum']);
    }
    break;
default:
    idx();
    break;
}

function idx()
{
    global $ir, $db;
    $q =
            $db->query(
                    "SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
                     `ff_posts`, `ff_topics`, `ff_lp_t_id`, `ff_lp_t_name`,
                     `ff_lp_poster_id`, `ff_lp_poster_name`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'public'
                     ORDER BY `ff_id` ASC");
    echo "<table class='table' width='100%' border='0' cellspacing='1'>
    		<tr>
    			<th>Forum</th>
    			<th>Posts</th>
    			<th>Topics</th>
    			<th>Last Post</th>
    		</tr>\n";
    while ($r = $db->fetch_row($q))
    {
        $t = date('F j Y, g:i:s a', $r['ff_lp_time']);
        echo "<tr>
        		<td align='center'>
        			<a href='forums.php?viewforum={$r['ff_id']}'
        				style='font-weight: 800;'>{$r['ff_name']}</a>
        			<br /><small>{$r['ff_desc']}</small>
        		</td>
        		<td align='center'>{$r['ff_posts']}</td>
        		<td align='center'>{$r['ff_topics']}</td>
        		<td align='center'>$t<br />
					In: <a href='forums.php?viewtopic={$r['ff_lp_t_id']}&amp;lastpost=1'
						style='font-weight: 800;'>{$r['ff_lp_t_name']}</a><br />
					By: <a href='viewuser.php?u={$r['ff_lp_poster_id']}'>
                        {$r['ff_lp_poster_name']}</a>
                </td>
              </tr>\n";
    }
    echo "\n</table>";
    $db->free_result($q);
    if ($ir['user_level'] > 1)
    {
        echo "<hr /><a name='staff'><h3>Staff-Only Forums</h3></a><hr />";
        $q =
                $db->query(
                        "SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
                         `ff_lp_poster_name`, `ff_lp_poster_id`,
                         `ff_lp_t_name`, `ff_lp_t_id`, `ff_topics`, `ff_posts`
                         FROM `forum_forums`
                         WHERE `ff_auth` = 'staff'
                         ORDER BY `ff_id` ASC");
        echo "<table cellspacing='1' class='table' width='100%' border='0'>
        		<tr>
        			<th>Forum</th>
        			<th>Posts</th>
        			<th>Topics</th>
        			<th>Last Post</th>
        		</tr>\n";
        while ($r = $db->fetch_row($q))
        {
            $t = date('F j Y, g:i:s a', $r['ff_lp_time']);
            echo "<tr>
        			<td align='center'>
        			<a href='forums.php?viewforum={$r['ff_id']}'
        				style='font-weight: 800;'>{$r['ff_name']}</a>
        			<br /><small>{$r['ff_desc']}</small>
        			</td>
        			<td align='center'>{$r['ff_posts']}</td>
        			<td align='center'>{$r['ff_topics']}</td>
        			<td align='center'>$t<br />
					In: <a href='forums.php?viewtopic={$r['ff_lp_t_id']}&amp;lastpost=1'
						style='font-weight: 800;'>{$r['ff_lp_t_name']}</a><br />
					By: <a href='viewuser.php?u={$r['ff_lp_poster_id']}'>
                        {$r['ff_lp_poster_name']}</a>
                	</td>
              	  </tr>\n";
        }
        echo "\n</table>";
        $db->free_result($q);
    }
}

function viewforum()
{
    global $ir, $h, $db;
    $_GET['viewforum'] =
            (isset($_GET['viewforum']) && is_numeric($_GET['viewforum']))
                    ? abs(intval($_GET['viewforum'])) : '';
    if (empty($_GET['viewforum']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$_GET['viewforum']}'");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if (($r['ff_auth'] == 'gang' && $ir['gang'] != $r['ff_owner']
            && $ir["user_level"] < 2)
            || ($r['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
You have no permission to view this forum.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    if ($_GET['viewforum'] != 1 OR $ir['user_level'] == 2)
    {
        $ntl =
                "&nbsp;[<a href='forums.php?act=newtopicform&amp;forum={$_GET['viewforum']}'>New Topic</a>]";
    }
    else
    {
        $ntl = "";
    }
    echo "<big>
    	   <a href='forums.php'>Forums Home</a>
    	   &gt;&gt; <a href='forums.php?viewforum={$_GET['viewforum']}'>{$r['ff_name']}</a>$ntl
    	  </big><br /><br />
		  <table cellspacing='1' class='table' width='100%' border='0'>
		  	<tr>
		  		<th>Topic</th>
		  		<th>Posts</th>
		  		<th>Started</th>
		  		<th>Last Post</th>
		  	</tr>\n";
    $q =
            $db->query(
                    "SELECT `ft_start_time`, `ft_last_time`, `ft_pinned`,
                     `ft_locked`, `ft_id`, `ft_name`, `ft_desc`, `ft_posts`,
                     `ft_owner_id`, `ft_owner_name`, `ft_last_id`, `ft_last_name`
                     FROM `forum_topics`
                     WHERE `ft_forum_id` = {$_GET['viewforum']}
                     ORDER BY `ft_pinned` DESC, `ft_last_time` DESC");
    while ($r2 = $db->fetch_row($q))
    {
        $t1 = date('F j Y, g:i:s a', $r2['ft_start_time']);
        $t2 = date('F j Y, g:i:s a', $r2['ft_last_time']);
        if ($r2['ft_pinned'])
        {
            $pt = "<b>Pinned:</b>&nbsp;";
        }
        else
        {
            $pt = "";
        }
        if ($r2['ft_locked'])
        {
            $lt = "&nbsp;<b>(Locked)</b>";
        }
        else
        {
            $lt = "";
        }
        echo "<tr>
        		<td align='center'>
                    $pt<a href='forums.php?viewtopic={$r2['ft_id']}&lastpost=1'>{$r2['ft_name']}</a>$lt<br />
					<small>{$r2['ft_desc']}</small>
				</td>
				<td align='center'>{$r2['ft_posts']}</td>
				<td align='center'>
                    $t1<br />
                    By: <a href='viewuser.php?u={$r2['ft_owner_id']}'>{$r2['ft_owner_name']}</a>
                </td>
                <td align='center'>
                    $t2<br />
                    By: <a href='viewuser.php?u={$r2['ft_last_id']}'>{$r2['ft_last_name']}</a>
                </td>
              </tr>\n";
    }
    echo "</table>";
    $db->free_result($q);
}

function viewtopic()
{
    global $ir, $h, $bbc, $db;
    $precache = array();
    $_GET['viewtopic'] =
            (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic']))
                    ? abs(intval($_GET['viewtopic'])) : '';
    if (empty($_GET['viewtopic']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_name`, `ft_posts`, `ft_id`,
                    `ft_locked`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`, `ff_id`, `ff_name`
                    FROM `forum_forums`
                    WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if (($forum['ff_auth'] == 'gang' && $ir['gang'] != $forum['ff_owner']
            && $ir["user_level"] < 2)
            || ($forum['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
You have no permission to view this forum.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    echo "<big>
    		<a href='forums.php'>Forums Home</a>
    		&gt;&gt; <a href='forums.php?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a>
    		&gt;&gt; <a href='forums.php?viewtopic={$_GET['viewtopic']}'>{$topic['ft_name']}</a>
    	  </big>
    	  <br /><br />";
    $posts_per_page = 20;
    $posts_topic = $topic['ft_posts'];
    $pages = ceil($posts_topic / $posts_per_page);
    $st =
            (isset($_GET['st']) && is_numeric($_GET['st']))
                    ? abs((int) $_GET['st']) : 0;
    if (isset($_GET['lastpost']))
    {
        $st = ($pages - 1) * 20;
    }
    $pst = -20;
    echo "Pages: ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $pst += 20;
        echo "<a href='forums.php?viewtopic={$topic['ft_id']}&st=$pst'>";
        if ($pst == $st)
        {
            echo "<b>";
        }
        echo $i;
        if ($pst == $st)
        {
            echo "</b>";
        }
        echo "</a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br />";
        }
    }
    echo "<br />";
    if ($ir['user_level'] > 1)
    {
        echo "
	<form action='forums.php?act=move&amp;topic={$_GET['viewtopic']}' method='post'>
    <b>Move topic to:</b> " . forum_dropdown('forum', -1)
                . "
	<input type='submit' value='Move' />
	</form>
	<br />
	<a href='forums.php?act=pin&amp;topic={$_GET['viewtopic']}'>
		<img src='sticky.jpg' alt='Pin/Unpin Topic' title='Pin/Unpin Topic' />
	</a>
	<a href='forums.php?act=lock&amp;topic={$_GET['viewtopic']}'>
		<img src='lock.jpg' alt='Lock/Unlock Topic' title='Lock/Unlock Topic' />
	</a>
	<a href='forums.php?act=deletopic&amp;topic={$_GET['viewtopic']}'>
		<img src='delete.gif' alt='Delete Topic' title='Delete Topic' />
	</a><br />
            ";
    }
    echo "<table cellspacing='1' class='table' width='100%'>\n";
    $q3 =
            $db->query(
                    "SELECT `fp_poster_name`, `fp_editor_time`,
                     `fp_editor_name`, `fp_editor_id`, `fp_edit_count`,
                     `fp_time`, `fp_id`, `fp_poster_id`, `fp_text`,
                     `fp_subject`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic['ft_id']}
                     ORDER BY `fp_time` ASC
                     LIMIT {$st}, 20");
    $no = $st;
    while ($r = $db->fetch_row($q3))
    {
        $qlink =
                "[<a href='forums.php?act=quote&amp;viewtopic={$_GET['viewtopic']}&amp;quotename="
                        . urlencode(
                                htmlentities($r['fp_poster_name'], ENT_QUOTES,
                                        'ISO-8859-1')) . "&amp;quotetext="
                        . urlencode(
                                htmlentities($r['fp_text'], ENT_QUOTES,
                                        'ISO-8859-1')) . "'>Quote Post</a>]";
        if ($ir['user_level'] > 1 || $ir['userid'] == $r['fp_poster_id'])
        {
            $elink =
                    "[<a href='forums.php?act=edit&amp;post={$r['fp_id']}&amp;topic={$_GET['viewtopic']}'>Edit Post</a>]";
        }
        else
        {
            $elink = "";
        }
        $no++;
        if ($no > 1 and $ir['user_level'] > 1)
        {
            $dlink =
                    "[<a href='forums.php?act=delepost&amp;post={$r['fp_id']}'>Delete Post</a>]";
        }
        else
        {
            $dlink = "";
        }
        $t = date('F j Y, g:i:s a', $r['fp_time']);
        if ($r['fp_edit_count'] > 0)
        {
            $edittext =
                    "\n<br /><i>Last edited by <a href='viewuser.php?u={$r['fp_editor_id']}'>{$r['fp_editor_name']}</a> at "
                            . date('F j Y, g:i:s a', $r['fp_editor_time'])
                            . ", edited <b>{$r['fp_edit_count']}</b> times in total.</i>";
        }
        else
        {
            $edittext = "";
        }
        if (!isset($precache[$r['fp_poster_id']]))
        {
            $membq =
                    $db->query(
                            "SELECT `userid`, `posts`, `forums_avatar`,
                            `forums_signature`, `level`
                             FROM `users`
                             WHERE `userid` = {$r['fp_poster_id']}");
            if ($db->num_rows($membq) == 0)
            {
                $memb = array('userid' => 0, 'forums_signature' => '');
            }
            else
            {
                $memb = $db->fetch_row($membq);
            }
            $db->free_result($membq);
            $precache[$memb['userid']] = $memb;
        }
        else
        {
            $memb = $precache[$r['fp_poster_id']];
        }
        if ($memb['userid'] > 0)
        {
            $rank = forums_rank($memb['posts']);
            $av =
                    ($memb['forums_avatar'])
                            ? '<img src="' . $memb['forums_avatar']
                                    . '" width="150px" height="150px" />'
                            : '<img src="noav.gif" />';
            $memb['forums_signature'] =
                    ($memb['forums_signature'])
                            ? $bbc->bbcode_parse($memb['forums_signature'])
                            : 'No Signature';
        }
        $r['fp_text'] = $bbc->bbcode_parse($r['fp_text']);
        echo "<tr>
				<th align='center'>Post #{$no}</th>
				<th align='center'>
					Subject: {$r['fp_subject']}<br />
					Posted at: $t $qlink$elink$dlink
				</th>
			 </tr>
			 <tr>
				<td valign='top'>";
        if ($memb['userid'] > 0)
        {
            print
                    "<a href='viewuser.php?u={$r['fp_poster_id']}'>{$r['fp_poster_name']}</a>
                    	[{$r['fp_poster_id']}]<br />
                     $av<br />
                     $rank<br />
					 Level: {$memb['level']}";
        }
        else
        {
            print "<b>Deleted User</b>";
        }
        print
                "</td>
			   	 <td valign='top'>
                    {$r['fp_text']}
                    {$edittext}<br />
					-------------------<br />
                    {$memb['forums_signature']}
                 </td>
		</tr>";
    }
    $db->free_result($q3);
    echo "</table>";
    $pst = -20;
    echo "Pages: ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $pst += 20;
        echo "<a href='forums.php?viewtopic={$topic['ft_id']}&amp;st=$pst'>";
        if ($pst == $st)
        {
            echo "<b>";
        }
        echo $i;
        if ($pst == $st)
        {
            echo "</b>";
        }
        echo "</a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br />";
        }
    }
    if ($topic['ft_locked'] == 0)
    {
        $reply_csrf = request_csrf_code("forums_reply_{$topic['ft_id']}");
        echo <<<EOF
<br /><br />
<b>Post a reply to this topic:</b><br />
<form action='forums.php?reply={$topic['ft_id']}' method='post'>
<input type='hidden' name='verf' value='{$reply_csrf}' />
<table cellspacing='1' class='table' width='80%' border='0'>
<tr>
<td align='right'>Subject:</td>
<td align='left'><input type='text' name='fp_subject' /></td>
</tr>
<tr>
<td align='right'>Post:</td>
<td align='left'><textarea rows='7' cols='40' name='fp_text'></textarea></td>
</tr>
<tr>
<th colspan='2'><input type='submit' value='Post Reply'></th>
</tr>
</table>
</form>
EOF;
    }
    else
    {
        echo "<br /><br />
<i>This topic has been locked, you cannot reply to it.</i>";
    }
}

function reply()
{
    global $ir, $userid, $h, $db;
    $_GET['reply'] =
            (isset($_GET['reply']) && is_numeric($_GET['reply']))
                    ? abs(intval($_GET['reply'])) : '';
    if (empty($_GET['reply']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_locked`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['reply']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`, `ff_id`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if (($forum['ff_auth'] == 'gang' && $ir['gang'] != $forum['ff_owner'])
            || ($forum['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
        You have no permission to reply to this topic.<br />
        &gt; <a href="forums.php">Back</a>
           ';
        die($h->endpage());
    }
    if (!isset($_POST['verf'])
            || !verify_csrf_code("forums_reply_{$topic['ft_id']}",
                    stripslashes($_POST['verf'])))
    {
        echo '
        Your request to reply to this topic has expired. Please post replies quickly.<br />
        &gt; <a href="forums.php">Back</a>
           ';
        die($h->endpage());
    }
    if ($topic['ft_locked'] == 0)
    {
        $u = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
        if ($ir['donatordays'] > 0)
        {
            $u =
                    '<span style="color: red;">'
                            . htmlentities($ir['username'], ENT_QUOTES,
                                    'ISO-8859-1') . '</span>';
        }
        $u = $db->escape($u);
        $_POST['fp_subject'] =
                $db->escape(strip_tags(stripslashes($_POST['fp_subject'])));
        if ((strlen($_POST['fp_subject']) > 150))
        {
            echo 'You can only submit a max of 150 characters.<br />&gt; <a href="forums.php">Go Back</a>';
            die($h->endpage());
        }
        $_POST['fp_text'] = $db->escape(stripslashes($_POST['fp_text']));
        if ((strlen($_POST['fp_text']) > 65535))
        {
            echo 'You can only submit a max of 65535 characters.<br />&gt; <a href="forums.php">Go Back</a>';
            die($h->endpage());
        }
        $post_time = time();
        $db->query(
                "INSERT INTO `forum_posts`
                 VALUES(NULL, {$_GET['reply']}, {$forum['ff_id']}, $userid,
                 '$u', {$post_time}, '{$_POST['fp_subject']}',
                 '{$_POST['fp_text']}', 0, '', 0, 0)");
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_last_id` = $userid, `ft_last_name` = '$u',
                 `ft_last_time` = {$post_time}, `ft_posts` = `ft_posts` + 1
                 WHERE `ft_id` = {$_GET['reply']}");
        $last_name = $db->escape($topic['ft_name']);
        $db->query(
                "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$post_time}, `ff_posts` = `ff_posts` + 1,
                 `ff_lp_poster_id` = $userid, `ff_lp_poster_name` = '$u',
                 `ff_lp_t_id` = {$_GET['reply']},
                 `ff_lp_t_name` = '{$last_name}'
                 WHERE `ff_id` = {$forum['ff_id']}");
        $db->query(
                "UPDATE `users`
        		    SET `posts` = `posts` + 1
        		    WHERE `userid` = {$userid}");
        echo "<b>Reply Posted!</b><hr /><br />";
        $_GET['lastpost'] = 1;
        $_GET['viewtopic'] = $_GET['reply'];
        viewtopic();
    }
    else
    {
        echo "
<i>This topic has been locked, you cannot reply to it.</i><br />
<a href='forums.php?viewtopic={$_GET['reply']}'>Back</a>";
    }
}

function newtopicform()
{
    global $ir, $h, $db;
    $_GET['forum'] =
            (isset($_GET['forum']) && is_numeric($_GET['forum']))
                    ? abs(intval($_GET['forum'])) : '';
    if (empty($_GET['forum']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$_GET['forum']}'");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if (($r['ff_auth'] == 'gang' && $ir['gang'] != $r['ff_owner'])
            || ($r['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
        You have no permission to view this forum.<br />
        &gt; <a href="forums.php">Back</a>
           ';
        die($h->endpage());
    }
    $nt_csrf = request_csrf_code("forums_newtopic_{$_GET['forum']}");
    echo <<<EOF
<big>
	<a href='forums.php'>Forums Home</a>
	&gt;&gt; <a href='forums.php?viewforum={$_GET['forum']}'>{$r['ff_name']}</a>
	&gt;&gt; New Topic Form
</big>
<form action='forums.php?act=newtopic&amp;forum={$_GET['forum']}' method='post'>
	<input type='hidden' name='verf' value='{$nt_csrf}' />
    <table cellspacing='1' class='table' width='80%'>
        <tr>
        	<td align='right'>Topic Name:</td>
        	<td align='left'><input type='text' name='ft_name' value='' /></td>
        </tr>
        <tr>
        	<td align='right'>Topic Description:</td>
        	<td align='left'><input type='text' name='ft_desc' value='' /></td>
        </tr>
        <tr>
        	<td align='right'>Topic Text:</td>
        	<td align='left'>
        		<textarea rows='8' cols='45' name='fp_text'></textarea>
        	</td>
        </tr>
        <tr>
        	<th colspan='2'><input type='submit' value='Post Topic' /></th>
        </tr>
    </table>
EOF;
}

function newtopic()
{
    global $ir, $userid, $h, $db;
    $_GET['forum'] =
            (isset($_GET['forum']) && is_numeric($_GET['forum']))
                    ? abs(intval($_GET['forum'])) : '';
    if (empty($_GET['forum']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`, `ff_id`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$_GET['forum']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if (($r['ff_auth'] == 'gang' && $ir['gang'] != $r['ff_owner'])
            || ($r['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
You have no permission to view this forum.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    if (!isset($_POST['verf'])
            || !verify_csrf_code("forums_newtopic_{$_GET['forum']}",
                    stripslashes($_POST['verf'])))
    {
        echo '
        Your request to create this topic has expired. Please post topics quickly.<br />
        &gt; <a href="forums.php">Back</a>
           ';
        die($h->endpage());
    }
    $u = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
    if ($ir['donatordays'] > 0)
    {
        $u =
                '<span style="color: red;">'
                        . htmlentities($ir['username'], ENT_QUOTES,
                                'ISO-8859-1') . '</span>';
    }
    $u = $db->escape($u);
    $_POST['ft_name'] =
            $db->escape(strip_tags(stripslashes($_POST['ft_name'])));
    if ((strlen($_POST['ft_name']) > 255))
    {
        echo 'You can only submit a max of 255 characters.<br />&gt; <a href="forums.php">Go Back</a>';
        die($h->endpage());
    }
    $_POST['ft_desc'] =
            $db->escape(strip_tags(stripslashes($_POST['ft_desc'])));
    if ((strlen($_POST['ft_desc']) > 255))
    {
        echo 'You can only submit a max of 255 characters.<br />&gt; <a href="forums.php">Go Back</a>';
        die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(stripslashes($_POST['fp_text']));
    if ((strlen($_POST['fp_text']) > 65535))
    {
        echo 'You can only submit a max of 65535 characters.<br />&gt; <a href="forums.php">Go Back</a>';
        die($h->endpage());
    }
    $post_time = time();
    $db->query(
            "INSERT INTO `forum_topics`
             VALUES(NULL, {$_GET['forum']}, '{$_POST['ft_name']}',
             '{$_POST['ft_desc']}', 0, $userid, '$u', {$post_time}, 0, '', 0,
             0, 0)");
    $i = $db->insert_id();
    $db->query(
            "INSERT INTO `forum_posts`
             VALUES(NULL, {$i}, {$r['ff_id']}, $userid, '$u', {$post_time},
             '{$_POST['ft_desc']}', '{$_POST['fp_text']}', 0, '', 0, 0)");
    $db->query(
            "UPDATE `forum_topics`
             SET `ft_last_id` = $userid, `ft_last_name` =  '$u',
             `ft_last_time` = {$post_time}, `ft_posts` = `ft_posts` + 1
             WHERE `ft_id` = {$i}");
    $db->query(
            "UPDATE `forum_forums`
             SET `ff_lp_time` = {$post_time}, `ff_posts` = `ff_posts` + 1,
             `ff_topics` = `ff_topics` + 1, `ff_lp_poster_id` = $userid,
             `ff_lp_poster_name` = '$u', `ff_lp_t_id` = {$i},
             `ff_lp_t_name` = '{$_POST['ft_name']}'
             WHERE `ff_id` = {$r['ff_id']}");
    $db->query(
            "UPDATE `users`
             SET `posts` = `posts` + 1
             WHERE `userid` = $userid");
    echo '
<b>Topic Posted!</b>
<hr />
<br />
   ';
    $_GET['viewtopic'] = $i;
    viewtopic();
}

function emptyallforums()
{
    global $db;
    $db->query(
            "UPDATE `forum_forums`
             SET `ff_lp_time` = 0, `ff_lp_poster_id` = 0,
             `ff_lp_poster_name` = 'N/A', `ff_lp_t_id` = 0,
             `ff_lp_t_name` = 'N/A', `ff_posts` = 0, `ff_topics` = 0");
    $db->query('TRUNCATE `forum_topics`');
    $db->query('TRUNCATE `forum_posts`');
}

function quote()
{
    global $ir, $h, $db;
    $_GET['viewtopic'] =
            (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic']))
                    ? abs(intval($_GET['viewtopic'])) : '';
    if (empty($_GET['viewtopic']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    if (!isset($_GET['quotename']) || !isset($_GET['quotetext']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_name`, `ft_locked`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner` ,`ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if (($forum['ff_auth'] == 'gang' && $ir['gang'] != $forum['ff_owner'])
            || ($forum['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
You have no permission to reply to this topic.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    echo "<big>
    		<a href='forums.php'>Forums Home</a>
    		&gt;&gt; <a href='forums.php?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a>
    		&gt;&gt; <a href='forums.php?viewtopic={$_GET['viewtopic']}'>{$topic['ft_name']}</a>
    		&gt;&gt; Quoting a Post
    	  </big>
		  <br />
		  <br />
    ";
    if ($topic['ft_locked'] == 0)
    {
        $_GET['quotename'] =
                htmlentities(strip_tags(stripslashes($_GET['quotename'])),
                        ENT_QUOTES, 'ISO-8859-1');
        $_GET['quotetext'] =
                htmlentities(stripslashes($_GET['quotetext']), ENT_QUOTES,
                        'ISO-8859-1');
        $quote_csrf = request_csrf_code("forums_reply_{$topic['ft_id']}");
        echo <<<EOF
<br /><br />
<b>Post a reply to this topic:</b><br />
<form action='forums.php?reply={$topic['ft_id']}' method='post'>
<input type='hidden' name='verf' value='{$quote_csrf}' />
    <table cellspacing='1' class='table' width='80%'>
        <tr>
        	<td align='right'>Subject:</td>
        	<td align='left'><input type='text' name='fp_subject' /></td>
        </tr>
        <tr>
        	<td align='right'>Post:</td>
        	<td align='left'>
        		<textarea rows='7' cols='40' name='fp_text'>
        		[quote={$_GET['quotename']}]{$_GET['quotetext']}[/quote]
        		</textarea>
        	</td>
        </tr>
        <tr>
        	<th colspan='2'><input type='submit' value='Post Reply' /></th>
        </tr>
    </table>
</form>
EOF;
    }
    else
    {
        echo "
<i>This topic has been locked, you cannot reply to it.</i><br />
<a href='forums.php?viewtopic={$_GET['viewtopic']}'>Back</a>
        ";
    }
}

function edit()
{
    global $ir, $h, $db;
    $_GET['topic'] =
            (isset($_GET['topic']) && is_numeric($_GET['topic']))
                    ? abs(intval($_GET['topic'])) : '';
    if (empty($_GET['topic']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`, `ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if (($forum['ff_auth'] == 'gang' && $ir['gang'] != $forum['ff_owner'])
            || ($forum['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
You have no permission to view this forum.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    $_GET['post'] =
            (isset($_GET['post']) && is_numeric($_GET['post']))
                    ? abs(intval($_GET['post'])) : '';
    if (empty($_GET['post']))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q3 =
            $db->query(
                    "SELECT `fp_poster_id`, `fp_subject`, `fp_text`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0)
    {
        $db->free_result($q3);
        echo 'Post doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!($ir['user_level'] > 1 || $ir['userid'] == $post['fp_poster_id']))
    {
        echo '
You have no permission to edit this post.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    echo "<big>
    		<a href='forums.php'>Forums Home</a>
    		&gt;&gt; <a href='forums.php?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a>
    		&gt;&gt; <a href='forums.php?viewtopic={$_GET['topic']}'>{$topic['ft_name']}</a>
    		&gt;&gt; Editing a Post
    	  </big><br /><br />
    ";
    $edit_csrf = request_csrf_code("forums_editpost_{$_GET['post']}");
    $fp_text = htmlentities($post['fp_text'], ENT_QUOTES, 'ISO-8859-1');
    echo <<<EOF
<form action='forums.php?act=editsub&topic={$topic['ft_id']}&post={$_GET['post']}' method='post'>
<input type='hidden' name='verf' value='{$edit_csrf}' />
    <table cellspacing='1' class='table' width='80%'>
        <tr>
        	<td align='right'>Subject:</td>
        	<td align='left'><input type='text' name='fp_subject' value='{$post['fp_subject']}' /></td>
        </tr>
        <tr>
        	<td align='right'>Post:</td>
        	<td align='left'>
        		<textarea rows='7' cols='40' name='fp_text'>{$fp_text}</textarea>
        	</td>
        </tr>
        <tr>
        	<th colspan='2'><input type='submit' value='Edit Post'></th>
        </tr>
    </table>
</form>
EOF;
}

function editsub()
{
    global $ir, $userid, $h, $db;
    $_GET['post'] =
            (isset($_GET['post']) && is_numeric($_GET['post']))
                    ? abs(intval($_GET['post'])) : '';
    $_GET['topic'] =
            (isset($_GET['topic']) && is_numeric($_GET['topic']))
                    ? abs(intval($_GET['topic'])) : '';
    if ((empty($_GET['post']) || empty($_GET['topic'])))
    {
        echo 'Something went wrong.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_owner`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        echo 'Forum doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if (($forum['ff_auth'] == 'gang' && $ir['gang'] != $forum['ff_owner'])
            || ($forum['ff_auth'] == 'staff' && $ir['user_level'] < 2))
    {
        echo '
You have no permission to view this forum.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    $q3 =
            $db->query(
                    "SELECT `fp_poster_id`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0)
    {
        $db->free_result($q3);
        echo 'Post doesn\'t exist.<br />
        	  &gt; <a href="forums.php" title="Go Back">Go Back</a>';
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!($ir['user_level'] > 1 || $ir['userid'] == $post['fp_poster_id']))
    {
        echo '
You have no permission to edit this post.<br />
&gt; <a href="forums.php">Back</a>
   ';
        die($h->endpage());
    }
    $_POST['fp_subject'] =
            $db->escape(strip_tags(stripslashes($_POST['fp_subject'])));
    if ((strlen($_POST['fp_subject']) > 150))
    {
        echo 'You can only submit a max of 150 characters.
        <br />&gt; <a href="forums.php">Go Back</a>';
        die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(stripslashes($_POST['fp_text']));
    if ((strlen($_POST['fp_text']) > 65535))
    {
        echo 'You can only submit a max of 65535 characters.
        <br />&gt; <a href="forums.php">Go Back</a>';
        die($h->endpage());
    }
    $db->query(
            "UPDATE `forum_posts`
             SET `fp_subject` = '{$_POST['fp_subject']}',
             `fp_text` = '{$_POST['fp_text']}', `fp_editor_id` = $userid,
             `fp_editor_name` = '{$ir['username']}',
             `fp_editor_time` = " . time()
                    . ",
             `fp_edit_count` = `fp_edit_count` + 1
             WHERE `fp_id` = {$_GET['post']}");
    echo '
<b>Post Edited!</b>
<hr />
<br />
   ';
    $_GET['viewtopic'] = $_GET['topic'];
    viewtopic();

}

function recache_forum($forum)
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
        echo " ... Done<br />";
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
        echo " ... Done<br />";
    }
}

function recache_topic($topic)
{
    global $db;
    $topic = abs((int) $topic);
    if ($topic <= 0)
    {
        return;
    }
    echo "Recaching topic ID $topic ... ";
    $q =
            $db->query(
                    "SELECT `fp_poster_id`, `fp_poster_name`, `fp_time`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic}
                     ORDER BY `fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_last_id` = 0, `ft_last_time` = 0,
                 `ft_last_name` = 'N/A', `ft_posts` = 0
                 WHERE `ft_id` = {$topic}");
        echo " ... Done<br />";
    }
    else
    {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $pn = $db->escape($r['fp_poster_name']);
        $posts_q =
                $db->query(
                        "SELECT COUNT(`fp_id`)
        					   FROM `forum_posts`
        					   WHERE `fp_topic_id` = {$topic}");
        $posts = $db->fetch_single($posts_q);
        $db->free_result($posts_q);
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_last_id` = {$r['fp_poster_id']},
                 `ft_last_time` = {$r['fp_time']}, `ft_last_name` = '$pn',
                 `ft_posts` = $posts
                 WHERE `ft_id` = {$topic}");
        echo " ... Done<br />";
    }
}

function move()
{
    global $ir, $h, $db;
    if (!in_array($ir['user_level'], array(2, 3, 5)))
    {
        echo 'There seems to be a error somewhere.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $_GET['topic'] =
            (isset($_GET['topic']) && is_numeric($_GET['topic']))
                    ? abs(intval($_GET['topic'])) : '';
    $_POST['forum'] =
            (isset($_POST['forum']) && is_numeric($_POST['forum']))
                    ? abs(intval($_POST['forum'])) : '';
    if (empty($_GET['topic']) || empty($_POST['forum']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_name`, `ft_forum_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$_POST['forum']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        echo 'Destination forum doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    $db->query(
            "UPDATE `forum_topics`
             SET `ft_forum_id` = {$_POST['forum']}
             WHERE `ft_id` = {$_GET['topic']}");
    $db->query(
            "UPDATE `forum_posts`
             SET `fp_forum_id` = {$_POST['forum']}
             WHERE `fp_topic_id` = {$_GET['topic']}");
    echo 'Topic moved...<br />';
    stafflog_add("Moved Topic {$topic['ft_name']} to {$forum['ff_name']}");
    recache_forum($topic['ft_forum_id']);
    recache_forum($_POST['forum']);
    echo '&gt; <a href="forums.php" alt="Go Back" title="Go Back">Go Back</a><br />';
}

function lock()
{
    global $ir, $h, $db;
    if (!in_array($ir['user_level'], array(2, 3, 5)))
    {
        echo 'There seems to be a error somewhere.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $_GET['topic'] =
            (isset($_GET['topic']) && is_numeric($_GET['topic']))
                    ? abs(intval($_GET['topic'])) : '';
    if (empty($_GET['topic']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_name`,`ft_locked`,`ft_forum_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ft_locked'] == 1)
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_locked` = 0
                 WHERE `ft_id` = {$_GET['topic']}");
        echo 'Topic unlocked.<br />&gt; <a href="forums.php?viewforum='
                . $r['ft_forum_id'] . '" title="Go Back">Go Back</a>';
        stafflog_add("Unlocked Topic {$r['ft_name']}");
    }
    else
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_locked` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        echo 'Topic locked.<br />&gt; <a href="forums.php?viewforum='
                . $r['ft_forum_id'] . '" title="Go Back">Go Back</a>';
        stafflog_add("Locked Topic {$r['ft_name']}");
    }
}

function pin()
{
    global $ir, $h, $db;
    if (!in_array($ir['user_level'], array(2, 3, 5)))
    {
        echo 'There seems to be a error somewhere.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $_GET['topic'] =
            (isset($_GET['topic']) && is_numeric($_GET['topic']))
                    ? abs(intval($_GET['topic'])) : '';
    if (empty($_GET['topic']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_name`, `ft_pinned`, `ft_forum_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ft_pinned'] == 1)
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_pinned` = 0
                 WHERE `ft_id` = {$_GET['topic']}");
        echo 'Topic unpinned.<br />&gt; <a href="forums.php?viewforum='
                . $r['ft_forum_id'] . '" title="Go Back">Go Back</a>';
        stafflog_add("Unpinned Topic {$r['ft_name']}");
    }
    else
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_pinned` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        echo 'Topic pinned.<br />&gt; <a href="forums.php?viewforum='
                . $r['ft_forum_id'] . '" title="Go Back">Go Back</a>';
        stafflog_add("Pinned Topic {$r['ft_name']}");
    }
}

function delepost()
{
    global $ir, $h, $db;
    if (!in_array($ir['user_level'], array(2, 3, 5)))
    {
        echo 'There seems to be a error somewhere.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $_GET['post'] =
            (isset($_GET['post']) && is_numeric($_GET['post']))
                    ? abs(intval($_GET['post'])) : '';
    if (empty($_GET['post']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $q3 =
            $db->query(
                    "SELECT `fp_topic_id`, `fp_poster_name`, `fp_id`,
                     `fp_forum_id`, `fp_subject`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0)
    {
        $db->free_result($q3);
        echo 'Post doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    $q =
            $db->query(
                    "SELECT `ft_name`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$post['fp_topic_id']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $db->query(
            "DELETE FROM `forum_posts`
    		    WHERE `fp_id` = {$post['fp_id']}");
    echo 'Post deleted...<br />';
    recache_topic($post['fp_topic_id']);
    recache_forum($post['fp_forum_id']);
    stafflog_add("Deleted post ({$post['fp_subject']}) in {$topic['ft_name']}");

}

function deletopic()
{
    global $h, $db;
    $_GET['topic'] =
            (isset($_GET['topic']) && is_numeric($_GET['topic']))
                    ? abs(intval($_GET['topic'])) : '';
    if (empty($_GET['topic']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_name`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Topic doesn\'t exist.<br />
        &gt; <a href="forums.php" title="Go Back">go back</a>';
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $db->query(
            "DELETE FROM `forum_topics`
    		    WHERE `ft_id` = {$_GET['topic']}");
    $db->query(
            "DELETE FROM `forum_posts`
             WHERE `fp_topic_id` = {$_GET['topic']}");
    echo "Deleting topic... Done<br />";
    recache_forum($topic['ft_forum_id']);
    stafflog_add("Deleted topic {$topic['ft_name']}");
}

$h->endpage();
