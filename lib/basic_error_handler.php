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
 * File: lib/basic_error_handler.php
 * Signature: 35d36ad46679785e4d1fe19ab6d541d7
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

/**
 * A very basic error handler, essentially just killing execution whenever
 * something goes wrong, or telling us more info if debug mode is on.
 * Can easily be replaced by a more robust one,
 * which e.g. logs raw error data somewhere for admin assessment
 */

// Change to true to show the user more information (for development)
define('DEBUG', false);

function error_critical($human_error, $debug_error, $action,
        $context = array())
{
    // Clear anything that was going to be shown
    ob_get_clean();
    // Setup a new error
    header('HTTP/1.1 500 Internal Server Error');
    // If we can, gracefully show them an error message
    // including the game's name. If not, just say
    // "Internal Server Error"
    global $set;
    if (isset($set) && is_array($set) && array_key_exists('game_name', $set))
    {
        echo '<h1>' . $set['game_name'] . ' - Critical Error</h1>';
    }
    else
    {
        echo '<h1>Internal Server Error</h1>';
    }
    if (DEBUG)
    {
        echo 'A critical error has occurred, and page execution has stopped. '
                . 'Below are the details:<br />' . $debug_error
                . '<br /><br />' . '<strong>Action taken:</strong> ' . $action
                . '<br /><br />';
        // Only uncomment the below if you know what you're doing,
        // for debug purposes.
        //if (is_array($context) && count($context) > 0)
        //{
        //    echo '<strong>Context at error time:</strong> ' . '<br /><br />'
        //            . nl2br(print_r($context, true));
        //}
    }
    else
    {
        echo 'A critical error has occurred, and this page cannot be displayed. '
                . 'Please try again later.';
        if (!empty($human_error))
        {
            echo '<br />' . $human_error;
        }
    }
    exit;
}

function error_php($errno, $errstr, $errfile = '', $errline = 0,
        $errcontext = array())
{
    // What's happened?
    // If it's a PHP warning or user error/warning, don't go further - indicates bad code, unsafe
    if ($errno == E_WARNING)
    {
        error_critical('',
                '<strong>PHP Warning:</strong> ' . $errstr . ' (' . $errno
                        . ')', 'Line executed: ' . $errfile . ':' . $errline,
                $errcontext);
    }
    else if ($errno == E_RECOVERABLE_ERROR)
    {
        error_critical('',
                '<strong>PHP Recoverable Error:</strong> ' . $errstr . ' ('
                        . $errno . ')',
                'Line executed: ' . $errfile . ':' . $errline, $errcontext);
    }
    else if ($errno == E_USER_ERROR)
    {
        error_critical('',
                '<strong>Engine Error:</strong> ' . $errstr . ' (' . $errno
                        . ')', 'Line executed: ' . $errfile . ':' . $errline,
                $errcontext);
    }
    else if ($errno == E_USER_WARNING)
    {
        error_critical('',
                '<strong>Engine Warning:</strong> ' . $errstr . ' (' . $errno
                        . ')', 'Line executed: ' . $errfile . ':' . $errline,
                $errcontext);
    }
    else
    {
        // Only do anything if DEBUG is on, now
        if (DEBUG)
        {
            // Determine the name to display from the error type
            $errname = 'Unknown Error';
            switch ($errno)
            {
            case E_NOTICE:
                $errname = 'PHP Notice';
                break;
            case E_USER_NOTICE:
                $errname = 'User Notice';
                break;
            case 8192:
                $errname = 'PHP Deprecation Notice';
                break; // E_DEPRECATED [since 5.3]
            case 16384:
                $errname = 'User Deprecation Notice';
                break; // E_USER_DEPRECATED [since 5.3]
            }
            echo 'A non-critical error has occurred. Page execution will continue. '
                    . 'Below are the details:<br /><strong>' . $errname
                    . '</strong>: ' . $errstr . ' (' . $errno . ')'
                    . '<br /><br />' . '<strong>Line executed</strong>: '
                    . $errfile . ':' . $errline . '<br /><br />';
            // Only uncomment the below if you know what you're doing,
            // for debug purposes.
            //if (is_array($errcontext) && count($errcontext) > 0)
            //{
            //    echo '<strong>Context at error time:</strong> '
            //            . '<br /><br />' . nl2br(print_r($errcontext, true));
            //}
        }
    }
}