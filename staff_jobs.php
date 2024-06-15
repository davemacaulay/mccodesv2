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
 * File: staff_jobs.php
 * Signature: 238a4aaf80c7e08fc7419790bc32463b
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('sglobals.php');
if ($ir['user_level'] != 2)
{
    echo 'You cannot access this area.<br />
    &gt; <a href="staff.php">Go Back</a>';
    die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'newjob':
    newjob();
    break;
case 'jobedit':
    jobedit();
    break;
case 'newjobrank':
    newjobrank();
    break;
case 'jobrankedit':
    jobrankedit();
    break;
case 'jobdele':
    jobdele();
    break;
case 'jobrankdele':
    jobrankdele();
    break;
default:
    echo "Error: This script requires an action.";
    break;
}

function newjob()
{
    global $db, $ir, $userid, $h;
    $price =
            (isset($_POST['price']) && is_numeric($_POST['price']))
                    ? abs(intval($_POST['price'])) : '';
    $will =
            (isset($_POST['will']) && is_numeric($_POST['will']))
                    ? abs(intval($_POST['will'])) : '';

    $_POST['jNAME'] =
            (isset($_POST['jNAME'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jNAME']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jNAME'])))
                    : '';
    $_POST['jDESC'] =
            (isset($_POST['jDESC']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jDESC'])))
                    : '';
    $_POST['jOWNER'] =
            (isset($_POST['jOWNER'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jOWNER']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jOWNER'])))
                    : '';
    $_POST['jrNAME'] =
            (isset($_POST['jrNAME'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jrNAME']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jrNAME'])))
                    : '';
    $_POST['jrPAY'] =
            (isset($_POST['jrPAY']) && is_numeric($_POST['jrPAY']))
                    ? abs(intval($_POST['jrPAY'])) : '';
    $_POST['jrSTRG'] =
            (isset($_POST['jrSTRG']) && is_numeric($_POST['jrSTRG']))
                    ? abs(intval($_POST['jrSTRG'])) : 0;
    $_POST['jrLABOURG'] =
            (isset($_POST['jrLABOURG']) && is_numeric($_POST['jrLABOURG']))
                    ? abs(intval($_POST['jrLABOURG'])) : 0;
    $_POST['jrIQG'] =
            (isset($_POST['jrIQG']) && is_numeric($_POST['jrIQG']))
                    ? abs(intval($_POST['jrIQG'])) : 0;
    $_POST['jrSTRN'] =
            (isset($_POST['jrSTRN']) && is_numeric($_POST['jrSTRN']))
                    ? abs(intval($_POST['jrSTRN'])) : 0;
    $_POST['jrLABOURN'] =
            (isset($_POST['jrLABOURN']) && is_numeric($_POST['jrLABOURN']))
                    ? abs(intval($_POST['jrLABOURN'])) : 0;
    $_POST['jrIQN'] =
            (isset($_POST['jrIQN']) && is_numeric($_POST['jrIQN']))
                    ? abs(intval($_POST['jrIQN'])) : 0;
    if (!empty($_POST['jNAME']) && !empty($_POST['jDESC'])
            && !empty($_POST['jOWNER']) && !empty($_POST['jrNAME'])
            && !empty($_POST['jrPAY']) && !empty($_POST['jrSTRN'])
            && !empty($_POST['jrLABOURN']) && !empty($_POST['jrIQN']))
    {
        staff_csrf_stdverify('staff_newjob', 'staff_jobs.php?action=newjob');
        $db->query(
                "INSERT INTO `jobs`
                 VALUES(NULL, '{$_POST['jNAME']}', 0,
                 '{$_POST['jDESC']}', '{$_POST['jOWNER']}')");
        $i = $db->insert_id();
        $db->query(
                "INSERT INTO `jobranks`
                 VALUES(NULL, '{$_POST['jrNAME']}', $i,
                 {$_POST['jrPAY']}, {$_POST['jrIQG']},
                 {$_POST['jrLABOURG']}, {$_POST['jrSTRG']},
                 {$_POST['jrIQN']}, {$_POST['jrLABOURN']},
                 {$_POST['jrSTRN']})");
        $j = $db->insert_id();
        $db->query(
                "UPDATE `jobs`
         		 SET `jFIRST` = $j
         		 WHERE `jID` = $i");
        echo 'Job created!<br />
        &gt; <a href="staff.php">Go Home</a>';
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_newjob');
        echo "
        <form action='staff_jobs.php?action=newjob' method='post'>
        	<b>Job Name:</b> <input type='text' name='jNAME' />
        	<br />
        	<b>Job Description:</b> <input type='text' name='jDESC' />
        	<br />
        	<b>Job Owner:</b> <input type='text' name='jOWNER' />
        	<br />
        	<hr />
        	<b>First Job Rank:</b>
        	<br />
        	<b>Rank Name:</b> <input type='text' name='jrNAME' />
        	<br />
        	<b>Pays:</b> <input type='text' name='jrPAY' value='10' />
        	<br />
        	<b>Gains:</b>
        		Str: <input type='text' name='jrSTRG' size='3' maxlength='3' value='0' />
        		Lab: <input type='text' name='jrLABOURG' size='3' maxlength='3' value='0' />
        		IQ: <input type='text' name='jrIQG' size='3' maxlength='3' value='0' />
        	<br />
        	<b>Reqs:</b>
        		Str: <input type='text' name='jrSTRN' size='5' maxlength='5' value='1' />
        		Lab: <input type='text' name='jrLABOURN' size='5' maxlength='5' value='1' />
        		IQ: <input type='text' name='jrIQN' size='5' maxlength='5' value='1' />
        	<br />
        	{$csrf}
        	<input type='submit' value='Create Job' />
        </form>
           ";
    }
}

function jobedit()
{
    global $db, $ir, $userid, $h;
    $_POST['jNAME'] =
            (isset($_POST['jNAME'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jNAME']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jNAME'])))
                    : '';
    $_POST['jDESC'] =
            (isset($_POST['jDESC']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jDESC'])))
                    : '';
    $_POST['jOWNER'] =
            (isset($_POST['jOWNER'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jOWNER']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jOWNER'])))
                    : '';
    $_POST['jFIRST'] =
            (isset($_POST['jFIRST']) && is_numeric($_POST['jFIRST']))
                    ? abs(intval($_POST['jFIRST'])) : '';
    $_POST['jID'] =
            (isset($_POST['jID']) && is_numeric($_POST['jID']))
                    ? abs(intval($_POST['jID'])) : '';
    if (!empty($_POST['jID']) && !empty($_POST['jFIRST'])
            && !empty($_POST['jOWNER']) && !empty($_POST['jDESC'])
            && !empty($_POST['jNAME']))
    {
        staff_csrf_stdverify('staff_editjob2', 'staff_jobs.php?action=jobedit');
        $q =
                $db->query(
                        "SELECT COUNT(`jID`)
                         FROM `jobs`
                         WHERE `jID` = {$_POST['jID']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid job.<br />
            &gt; <a href="staff_jobs.php?action=jobedit">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $q =
                $db->query(
                        "SELECT COUNT(`jrID`)
                         FROM `jobranks`
                         WHERE `jrID` = {$_POST['jFIRST']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid rank.<br />
            &gt; <a href="staff_jobs.php?action=jobedit">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "UPDATE `jobs`
                 SET `jNAME` = '{$_POST['jNAME']}',
                 `jDESC` = '{$_POST['jDESC']}',
                 `jOWNER` = '{$_POST['jOWNER']}',
                 `jFIRST` = {$_POST['jFIRST']}
                 WHERE `jID` = {$_POST['jID']}");
        echo 'Job updated!<br />
        &gt; <a href="staff.php">Go Home</a>';
        die($h->endpage());
    }
    else if (!empty($_POST['jID']))
    {
        staff_csrf_stdverify('staff_editjob1', 'staff_jobs.php?action=jobedit');
        $q =
                $db->query(
                        "SELECT `jFIRST`, `jOWNER`, `jDESC`, `jNAME`
                         FROM `jobs`
                         WHERE `jID` = {$_POST['jID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid job.<br />
            &gt; <a href="staff_jobs.php?action=jobedit">Go Back</a>';
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html('staff_editjob2');
        $jobname = addslashes($r['jNAME']);
        $jobdesc = addslashes($r['jDESC']);
        $jobowner = addslashes($r['jOWNER']);
        echo "
        <form action='staff_jobs.php?action=jobedit' method='post'>
        	<input type='hidden' name='jID' value='{$_POST['jID']}'>
        	<b>Job Name:</b> <input type='text' name='jNAME' value='{$jobname}'>
        	<br />
        	<b>Job Description:</b> <input type='text' name='jDESC' value='{$jobdesc}'>
        	<br />
        	<b>Job Owner:</b> <input type='text' name='jOWNER' value='{$jobowner}'>
        	<br />
        	<b>First Job Rank:</b> "
                . jobrank_dropdown(NULL, 'jFIRST', $r['jFIRST'])
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Edit' />
        </form>
           ";
    }
    else
    {
        $csrf = request_csrf_html('staff_editjob1');
        echo "
        <form action='staff_jobs.php?action=jobedit' method='post'>
        Select a job to edit.
        <br />
        	" . job_dropdown(NULL, 'jID', -1)
                . "
        <br />
        	{$csrf}
        	<input type='submit' value='Edit Job' />
        </form>
           ";
    }
}

function newjobrank()
{
    global $db, $ir, $userid, $h;
    $_POST['jrNAME'] =
            (isset($_POST['jrNAME'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jrNAME']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jrNAME'])))
                    : '';
    $_POST['jrJOB'] =
            (isset($_POST['jrJOB']) && is_numeric($_POST['jrJOB']))
                    ? abs(intval($_POST['jrJOB'])) : '';
    $_POST['jrPAY'] =
            (isset($_POST['jrPAY']) && is_numeric($_POST['jrPAY']))
                    ? abs(intval($_POST['jrPAY'])) : '';
    $_POST['jrSTRG'] =
            (isset($_POST['jrSTRG']) && is_numeric($_POST['jrSTRG']))
                    ? abs(intval($_POST['jrSTRG'])) : 0;
    $_POST['jrLABOURG'] =
            (isset($_POST['jrLABOURG']) && is_numeric($_POST['jrLABOURG']))
                    ? abs(intval($_POST['jrLABOURG'])) : 0;
    $_POST['jrIQG'] =
            (isset($_POST['jrIQG']) && is_numeric($_POST['jrIQG']))
                    ? abs(intval($_POST['jrIQG'])) : 0;
    $_POST['jrSTRN'] =
            (isset($_POST['jrSTRN']) && is_numeric($_POST['jrSTRN']))
                    ? abs(intval($_POST['jrSTRN'])) : 0;
    $_POST['jrLABOURN'] =
            (isset($_POST['jrLABOURN']) && is_numeric($_POST['jrLABOURN']))
                    ? abs(intval($_POST['jrLABOURN'])) : 0;
    $_POST['jrIQN'] =
            (isset($_POST['jrIQN']) && is_numeric($_POST['jrIQN']))
                    ? abs(intval($_POST['jrIQN'])) : 0;
    if (!empty($_POST['jrNAME']) && !empty($_POST['jrJOB'])
            && !empty($_POST['jrPAY']) && !empty($_POST['jrSTRN'])
            && !empty($_POST['jrLABOURN']) && !empty($_POST['jrIQN']))
    {
        staff_csrf_stdverify('staff_newjobrank',
                'staff_jobs.php?action=newjobrank');
        $q =
                $db->query(
                        "SELECT COUNT(`jID`)
                         FROM `jobs`
                         WHERE `jID` = {$_POST['jrJOB']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid job.<br />
            &gt; <a href="staff_jobs.php?action=newjobrank">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "INSERT INTO `jobranks`
                 VALUES(NULL, '{$_POST['jrNAME']}', {$_POST['jrJOB']},
                 {$_POST['jrPAY']}, {$_POST['jrIQG']}, {$_POST['jrLABOURG']},
                 {$_POST['jrSTRG']}, {$_POST['jrIQN']}, {$_POST['jrLABOURN']},
                 {$_POST['jrSTRN']})");
        echo 'Job rank created!<br />
        &gt; <a href="staff_jobs.php?action=newjobrank">Go Back</a>';
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_newjobrank');
        echo "
        <form action='staff_jobs.php?action=newjobrank' method='post'>
        	<b>Rank Name:</b> <input type='text' name='jrNAME' />
        	<br />
        	<b>Pays:</b> <input type='text' name='jrPAY' value='10' />
        	<br />
        	<b>Job:</b> " . job_dropdown(NULL, "jrJOB", -1)
                . "
        	<br />
        	<b>Gains:</b>
        		Str: <input type='text' name='jrSTRG' size='3' maxlength='3' value='0' />
        		Lab: <input type='text' name='jrLABOURG' size='3' maxlength='3' value='0' />
        		IQ: <input type='text' name='jrIQG' size='3' maxlength='3' value='0' />
        	<br />
        	<b>Reqs:</b>
        		Str: <input type='text' name='jrSTRN' size='5' maxlength='5' value='1' />
        		Lab: <input type='text' name='jrLABOURN' size='5' maxlength='5'  value='1' />
        		IQ: <input type='text' name='jrIQN' size='5' maxlength='5'  value='1' />
        	<br />
        	{$csrf}
        	<input type='submit' value='Create Job Rank' />
        </form>
           ";
    }
}

function jobrankedit()
{
    global $db, $ir, $userid, $h;
    $_POST['jrID'] =
            (isset($_POST['jrID']) && is_numeric($_POST['jrID']))
                    ? abs(intval($_POST['jrID'])) : '';
    $_POST['jrNAME'] =
            (isset($_POST['jrNAME'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['jrNAME']))
                    ? $db->escape(strip_tags(stripslashes($_POST['jrNAME'])))
                    : '';
    $_POST['jrJOB'] =
            (isset($_POST['jrJOB']) && is_numeric($_POST['jrJOB']))
                    ? abs(intval($_POST['jrJOB'])) : '';
    $_POST['jrPAY'] =
            (isset($_POST['jrPAY']) && is_numeric($_POST['jrPAY']))
                    ? abs(intval($_POST['jrPAY'])) : '';
    $_POST['jrSTRG'] =
            (isset($_POST['jrSTRG']) && is_numeric($_POST['jrSTRG']))
                    ? abs(intval($_POST['jrSTRG'])) : 0;
    $_POST['jrLABOURG'] =
            (isset($_POST['jrLABOURG']) && is_numeric($_POST['jrLABOURG']))
                    ? abs(intval($_POST['jrLABOURG'])) : 0;
    $_POST['jrIQG'] =
            (isset($_POST['jrIQG']) && is_numeric($_POST['jrIQG']))
                    ? abs(intval($_POST['jrIQG'])) : 0;
    $_POST['jrSTRN'] =
            (isset($_POST['jrSTRN']) && is_numeric($_POST['jrSTRN']))
                    ? abs(intval($_POST['jrSTRN'])) : 0;
    $_POST['jrLABOURN'] =
            (isset($_POST['jrLABOURN']) && is_numeric($_POST['jrLABOURN']))
                    ? abs(intval($_POST['jrLABOURN'])) : 0;
    $_POST['jrIQN'] =
            (isset($_POST['jrIQN']) && is_numeric($_POST['jrIQN']))
                    ? abs(intval($_POST['jrIQN'])) : 0;
    if (!empty($_POST['jrID']) && !empty($_POST['jrNAME'])
            && !empty($_POST['jrJOB']) && !empty($_POST['jrPAY'])
            && !empty($_POST['jrSTRN']) && !empty($_POST['jrLABOURN'])
            && !empty($_POST['jrIQN']))
    {
        staff_csrf_stdverify('staff_editjobrank2',
                'staff_jobs.php?action=jobrankedit');
        $q =
                $db->query(
                        "SELECT COUNT(`jrID`)
                         FROM `jobranks`
                         WHERE `jrID` = {$_POST['jrID']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid rank.<br />
            &gt; <a href="staff_jobs.php?action=jobrankedit">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $q =
                $db->query(
                        "SELECT COUNT(`jID`)
                         FROM `jobs`
                         WHERE `jID` = {$_POST['jrJOB']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid job.<br />
            &gt; <a href="staff_jobs.php?action=jobrankedit">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "UPDATE `jobranks`
                 SET `jrNAME` = '{$_POST['jrNAME']}',
                 `jrJOB` = {$_POST['jrJOB']}, `jrPAY` = {$_POST['jrPAY']},
                 `jrIQG` = {$_POST['jrIQG']},
                 `jrLABOURG` = {$_POST['jrLABOURG']},
                 `jrSTRG` = {$_POST['jrSTRG']}, `jrIQN` = {$_POST['jrIQN']},
                 `jrLABOURN` = {$_POST['jrLABOURN']},
                 `jrSTRN` = {$_POST['jrSTRN']}
                 WHERE `jrID` = {$_POST['jrID']}");
        echo 'Job rank updated!<br />
        &gt; <a href="staff.php">Go Home</a>';
    }
    else if (!empty($_POST['jrID']))
    {
        staff_csrf_stdverify('staff_editjobrank1',
                'staff_jobs.php?action=jobrankedit');
        $q =
                $db->query(
                        "SELECT `jrIQN`, `jrLABOURN`, `jrSTRN`, `jrIQG`,
                         `jrLABOURG`, `jrSTRG`, `jrPAY`, `jrJOB`, `jrNAME`
                         FROM `jobranks`
                         WHERE `jrID` = {$_POST['jrID']}");
        if ($db->num_rows($q) == 0)
        {
            echo 'Invalid rank.<br />
            &gt; <a href="staff_jobs.php?action=jobrankedit">Go Back</a>';
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $csrf = request_csrf_html('staff_editjobrank2');
        echo "
        <form action='staff_jobs.php?action=jobrankedit' method='post'>
        	<input type='hidden' name='jrID' value='{$_POST['jrID']}' />
        	<b>Job Rank Name:</b> <input type='text' name='jrNAME' value='{$r['jrNAME']}'><br />
        	<b>Job:</b> " . job_dropdown(NULL, 'jrJOB', $r['jrJOB'])
                . "
        	<br />
        	<b>Pays:</b> <input type='text' name='jrPAY' value='{$r['jrPAY']}' /><br />
        	<b>Gains:</b>
        		Str: <input type='text' name='jrSTRG' size='3' maxlength='3' value='{$r['jrSTRG']}' />
        		Lab: <input type='text' name='jrLABOURG' size='3' maxlength='3' value='{$r['jrLABOURG']}' />
        		IQ: <input type='text' name='jrIQG' size='3' maxlength='3' value='{$r['jrIQG']}' />
        	<br />
        	<b>Reqs:</b>
        		Str: <input type='text' name='jrSTRN' size='5' maxlength='5' value='{$r['jrSTRN']}' />
        		Lab: <input type='text' name='jrLABOURN' size='5' maxlength='5' value='{$r['jrLABOURN']}' />
        		IQ: <input type='text' name='jrIQN' size='5' maxlength='5' value='{$r['jrIQN']}' />
        	<br />
        	{$csrf}
        	<input type='submit' value='Edit' />
        </form>
           ";
    }
    else
    {
        $csrf = request_csrf_html('staff_editjobrank1');
        echo "
        <form action='staff_jobs.php?action=jobrankedit' method='post'>
        	Select a job rank to edit.
        	<br />
        	" . jobrank_dropdown(NULL, 'jrID', -1)
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Edit Job Rank' />
        </form>
           ";
    }
}

function jobrankdele()
{
    global $db, $ir, $userid, $h;
    $_POST['jrID'] =
            (isset($_POST['jrID']) && is_numeric($_POST['jrID']))
                    ? abs(intval($_POST['jrID'])) : '';
    if (!empty($_POST['jrID']))
    {
        staff_csrf_stdverify('staff_deljobrank',
                'staff_jobs.php?action=jobrankdele');
        $q =
                $db->query(
                        "SELECT `jrJOB`
                         FROM `jobranks`
                         WHERE `jrID` = {$_POST['jrID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid rank.<br />
            &gt; <a href="staff_jobs.php?action=jobrankdele">Go Back</a>';
            die($h->endpage());
        }
        $aff_job = $db->fetch_single($q);
        $db->free_result($q);
        $db->query(
                "DELETE FROM `jobranks`
         		 WHERE `jrID` = {$_POST['jrID']}");
        echo "Job rank successfully deleted!";
        $q =
                $db->query(
                        "SELECT `jNAME`
                         FROM `jobs`
                         WHERE `jFIRST` = {$_POST['jrID']}");
        if ($db->num_rows($q) > 0)
        {
            $jname = $db->fetch_single($q);
            echo "<br />
            <b>Warning!</b> The Job {$jname} now has no first rank!
            	Please go edit it and include a first rank.<br />
            Users who were in the rank you deleted will have to
            	reapply for their job.";
            $db->query(
                    "UPDATE `users`
            		 SET `job` = 0, `jobrank` = 0
            		 WHERE `jobrank` = {$_POST['jrID']}");
        }
        else
        {
            $db->query(
                    "UPDATE `users` AS `u`
                     INNER JOIN `jobs` AS `j`
                     ON `u`.`job` = `j`.`jID`
                     SET `u`.`jobrank` = `j`.`jFIRST`
                     WHERE `u`.`job` = {$jr['jrJOB']}
                     AND `u`.`jobrank` = {$_POST['jrID']}");
        }
        $db->free_result($q);
        echo '<br />&gt; <a href="staff.php">Go Home</a>';
    }
    else
    {
        $csrf = request_csrf_html('staff_deljobrank');
        echo "
        <form action='staff_jobs.php?action=jobrankdele' method='post'>
        Select a job rank to delete.<br />
        	" . jobrank_dropdown(NULL, 'jrID', -1)
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Delete Job Rank' />
        </form>
           ";
    }
}

function jobdele()
{
    global $db, $ir, $userid, $h;
    $_POST['jID'] =
            (isset($_POST['jID']) && is_numeric($_POST['jID']))
                    ? abs(intval($_POST['jID'])) : '';
    if (!empty($_POST['jID']))
    {
        staff_csrf_stdverify('staff_deljob', 'staff_jobs.php?action=jobdele');
        $q =
                $db->query(
                        "SELECT COUNT(`jID`)
                         FROM `jobs`
                         WHERE `jID` = {$_POST['jID']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            echo 'Invalid job.<br />
            &gt; <a href="staff_jobs.php?action=jobdele">Go Back</a>';
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "DELETE FROM `jobs`
         		 WHERE `jID` = {$_POST['jID']}");
        echo 'Job successfully deleted!<br />';
        $db->query(
                "DELETE FROM `jobranks`
         		 WHERE `jrJOB` = {$_POST['jID']}");
        echo $db->affected_rows() . ' job ranks deleted.<br />';
        $db->query(
                "UPDATE `users`
                 SET `job` = 0, `jobrank` = 0
                 WHERE `job` = {$_POST['jID']}");
        echo '&gt; <a href="staff.php">Go Home</a>';
    }
    else
    {
        $csrf = request_csrf_html('staff_deljob');
        echo "
        <form action='staff_jobs.php?action=jobdele' method='post'>
        Select a job to delete.<br />
        	" . job_dropdown(NULL, 'jID', -1)
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Delete Job' />
        </form>
           ";
    }
}
$h->endpage();
