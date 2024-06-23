<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if (!isset($_POST['password']))
{ // If they are trying to view this without ?password=password.
    die('Whats this document for?'); // Lawl what is this document for anyway?
}
else
{ // ElseIf we cant to check the password's strength.
    $PASS =
            stripslashes(
                    strip_tags(
                            htmlentities($_POST['password'], ENT_QUOTES,
                                    'ISO-8859-1'))); // Cleans all nasty input from the password.
    $strength = 1; // Sets their default amount of points to 1.

    if ($PASS != NULL)
    { // If the current password is not NULL (empty).
        $numbers =
                [ // Creates our array to store 1 - 9 in.
                        1 => '1', // 1.
                        2 => '2', // 2.
                        3 => '3', // 3.
                        4 => '4', // 4.
                        5 => '5', // 5.
                        6 => '6', // 6.
                        7 => '7', // 7.
                        8 => '8', // 8.
                        9 => '9', // 9.
                        0 => '0' // 0.
                ]; // Closes the Array.

        $lowercase =
                [ // Creates our array to store a - z in.
                        1 => 'a', // a.
                        2 => 'b', // b.
                        3 => 'c', // c.
                        4 => 'd', // d.
                        5 => 'e', // e.
                        6 => 'f', // f.
                        7 => 'g', // g.
                        8 => 'h', // h.
                        9 => 'i', // i.
                        10 => 'j', // j.
                        11 => 'k', // k.
                        12 => 'l', // l.
                        13 => 'm', // m.
                        14 => 'n', // n.
                        15 => 'o', // o.
                        16 => 'p', // p.
                        17 => 'q', // q.
                        18 => 'r', // r.
                        19 => 's', // s.
                        20 => 't', // t.
                        21 => 'u', // u.
                        22 => 'v', // v.
                        23 => 'w', // w.
                        24 => 'x', // x.
                        25 => 'y', // y.
                        26 => 'z' // z.
                ]; // Closes the Array.

        $uppercase =
                [ // Creates our array to store A - Z in.
                        1 => 'A', // A.
                        2 => 'B', // B.
                        3 => 'C', // C.
                        4 => 'D', // D.
                        5 => 'E', // E.
                        6 => 'F', // F.
                        7 => 'G', // G.
                        8 => 'H', // H.
                        9 => 'I', // I.
                        10 => 'J', // J.
                        11 => 'K', // K.
                        12 => 'L', // L.
                        13 => 'M', // M.
                        14 => 'N', // N.
                        15 => 'O', // O.
                        16 => 'P', // P.
                        17 => 'Q', // Q.
                        18 => 'R', // R.
                        19 => 'S', // S.
                        20 => 'T', // T.
                        21 => 'U', // U.
                        22 => 'V', // V.
                        23 => 'W', // W.
                        24 => 'X', // X.
                        25 => 'Y', // Y.
                        26 => 'Z' // Z.
                ]; // Closes the Array.
        $symbs =
                ['\\', '/', '"', "'", '{', '}', ')', '(', '|', '?', '.',
                    ',', '<', '>', '_', '-', '!', '#', "\$", '%', '^',
                    '&', '*'];
        $strength = 0;
        if (strlen($PASS) >= 7)
        {
            $strength += 3;
        }
        $nc = 0;
        foreach ($numbers as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $nc = 0;
        foreach ($lowercase as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $nc = 0;
        foreach ($uppercase as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $nc = 0;
        foreach ($symbs as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 1)
        {
            $strength += 1;
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $overall = '';
        if ($strength <= 2)
        { // If there total points are equal or less than 5.
            $overall = '<span style="color:#FF0000">Weak</span>'; // Eeek very week!
        }
        elseif ($strength <= 5)
        { // If there total points are equal or less than 8.
            $overall = '<span style="color:#999900">Moderate</span>'; // Omg week.
        }
        elseif ($strength <= 10)
        { // If there total points are equal or less than 12.
            $overall = '<span style="color:#008800">Good</span>'; // Meh Moderate.
        }
        elseif ($strength >= 12)
        { // If there total points are greater than 12.
            $overall = '<span style="color:#0000ff">Excellent</span>'; // That's the way Superman.
        } // End If.

        echo 'Password strength: ' . $overall; // Tells them their passwords strength.

    }
    else
    { // ElseIf their password is NULL (empty).
        echo ''; // Dont display anything.
    } // End ElseIf.
} // End ElseIF.
