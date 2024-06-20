<?php
declare(strict_types=1);
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
 * File: creategang.php
 * Signature: 4c05acde022af67e7df7d3aa23e43ca9
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */
global $db, $ir, $userid, $h;
require_once('globals.php');
$cg_price = 500000;
if ($ir['money'] < $cg_price)
{
    echo "You don't have enough money. You need " . money_formatter($cg_price)
            . '.';
    $h->endpage();
    exit;
}
if ($ir['gang'])
{
    echo "You're already in a gang!";
    $h->endpage();
    exit;
}
if (isset($_POST['submit']) && isset($_POST['desc'])
        && !empty($_POST['name']))
{
    if (!isset($_POST['verf'])
            || !verify_csrf_code('creategang', stripslashes($_POST['verf'])))
    {
        echo '<h3>Error</h3><hr />
    This transaction has been blocked for your security.<br />
    Please create your gang quickly after you open the form - do not leave it open in tabs.<br />
    &gt; <a href="creategang.php">Try Again</a>';
        $h->endpage();
        exit;
    }
    $name =
            $db->escape(
                    htmlentities(stripslashes($_POST['name']), ENT_QUOTES,
                            'ISO-8859-1'));
    $desc =
            $db->escape(
                    htmlentities(stripslashes($_POST['desc']), ENT_QUOTES,
                            'ISO-8859-1'));
    $db->query(
            "INSERT INTO `gangs`
                    (`gangNAME`, `gangDESC`, `gangRESPECT`, `gangPRESIDENT`, `gangVICEPRES`, `gangCAPACITY`)
                     VALUES('$name', '$desc', 100, $userid, $userid, 5)");
    $i = $db->insert_id();
    $db->query(
            "UPDATE `users` SET `gang` = $i, `money` = `money` - {$cg_price} WHERE `userid` = $userid");
    echo 'Gang created!';
}
else
{
    $code = request_csrf_code('creategang');
    echo "<h3> Create A Gang </h3>
    <form action='creategang.php' method='post'>
    <input type='hidden' name='submit' value='1' />
    Name:<input type='text' name='name' /><br />
    Description:<br />
    <textarea name='desc' cols='40' rows='7'></textarea>
    <br />
    <input type='hidden' name='verf' value='{$code}' />
    <input type='submit' value='Create Gang for " . money_formatter($cg_price)
            . "' />
    </form>";
}
$h->endpage();
