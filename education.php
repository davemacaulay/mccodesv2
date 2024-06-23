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
echo '<h3>Schooling</h3>';
if ($ir['course'] > 0)
{
    $cd =
            $db->query(
                    "SELECT `crNAME`
    				 FROM `courses`
    				 WHERE `crID` = {$ir['course']}");
    $coud = $db->fetch_row($cd);
    $db->free_result($cd);
    echo "You are currently doing the {$coud['crNAME']}, you have
          {$ir['cdays']} days remaining.";
} elseif (isset($_GET['cstart'])) {
    $_GET['cstart'] = abs((int)$_GET['cstart']);
    //Verify.
    $cd =
        $db->query(
            "SELECT `crCOST`, `crDAYS`, `crNAME`
                         FROM `courses`
                         WHERE `crID` = {$_GET['cstart']}");
    if ($db->num_rows($cd) == 0) {
        echo 'You are trying to start a non-existent course!';
    } else {
        $coud = $db->fetch_row($cd);
        $db->free_result($cd);
        $cdo =
            $db->query(
                "SELECT COUNT(`userid`)
                             FROM `coursesdone`
                             WHERE `userid` = $userid
                     		 AND `courseid` = {$_GET['cstart']}");
        if ($ir['money'] < $coud['crCOST']) {
            echo "You don't have enough money to start this course.";
            $h->endpage();
            exit;
        }
        if ($db->fetch_single($cdo) > 0) {
            $db->free_result($cdo);
            echo 'You have already done this course.';
            $h->endpage();
            exit;
        }
        $db->free_result($cdo);
        $db->query(
            "UPDATE `users`
                     SET `course` = {$_GET['cstart']},
                     `cdays` = {$coud['crDAYS']},
                     `money` = `money` - {$coud['crCOST']}
                     WHERE `userid` = $userid");
        echo "You have started the {$coud['crNAME']},
                  it will take {$coud['crDAYS']} days to complete.";
    }
} else {
    //list courses
    echo 'Here is a list of available courses.<br />';
    $q =
        $db->query(
            'SELECT `crID`, `crNAME`, `crDESC`, `crCOST`
        				 FROM `courses`');
    echo "<table width='75%' cellspacing='1' class='table'>
        		<tr style='background:gray;'>
        			<th>Course</th>
        			<th>Description</th>
        			<th>Cost</th>
        			<th>Take</th>
        		</tr>";
    while ($r = $db->fetch_row($q)) {
        $cdo =
            $db->query(
                "SELECT COUNT(`userid`)
                             FROM `coursesdone`
                             WHERE `userid` = $userid
                             AND `courseid` = {$r['crID']}");
        if ($db->fetch_single($cdo) > 0) {
            $do = '<i>Done</i>';
        } else {
            $do = "<a href='education.php?cstart={$r['crID']}'>Take</a>";
        }
        $db->free_result($cdo);
        echo "<tr>
            		<td>{$r['crNAME']}</td>
            		<td>{$r['crDESC']}</td>
            		<td>" . money_formatter((int)$r['crCOST'])
            . "</td>
                    <td>$do</td>
                  </tr>";
    }
    $db->free_result($q);
    echo '</table>';
}
$h->endpage();
