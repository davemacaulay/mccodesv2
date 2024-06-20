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
 * File: staffnotes.php
 * Signature: 3d41877d54f2ee96787e4016324a4931
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
if (in_array($ir['user_level'], [2, 3, 5]))
{
    $_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : '';
    $_POST['staffnotes'] =
            (isset($_POST['staffnotes']) && !is_array($_POST['staffnotes']))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['staffnotes'])))
                    : '';
    if (empty($_POST['ID']) || empty($_POST['staffnotes']))
    {
        echo 'You must enter data for this to work.
        <br />&gt; <a href="index.php">Go Home</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `staffnotes`
    				 FROM `users`
    				 WHERE `userid` = {$_POST['ID']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'That user does not exist.
        <br />&gt; <a href="index.php">Go Home</a>';
        die($h->endpage());
    }
    $old = $db->escape($db->fetch_single($q));
    $db->free_result($q);
    $db->query(
            "UPDATE `users`
             SET `staffnotes` = '{$_POST['staffnotes']}'
             WHERE `userid` = '{$_POST['ID']}'");
    $db->query(
            "INSERT INTO `staffnotelogs`
             VALUES (NULL, $userid, {$_POST['ID']}, " . time()
                    . ", '$old',
              '{$_POST['staffnotes']}')");
    echo '
	User notes updated!
	<br />
	&gt; <a href="viewuser.php?u=' . $_POST['ID']
            . '">Back To Profile</a>
  	 ';
}
else
{
    echo 'You cannot access this file.
    <br />&gt; <a href="index.php">Go Home</a>';
}
$h->endpage();
