<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

/**
 * An error handler used to handle PHP errors encountered during installation.
 */

function error_critical($debug_error, $action,
                        $context = []): void
{
    require_once('./installer_head.php'); // in case it hasn't been included
    // Set up a new error
    header('HTTP/1.1 500 Internal Server Error');
    echo '<h1>Installer Error</h1>';
    echo 'A critical error has occurred, and installation has stopped. '
            . 'Below are the details:<br />' . $debug_error . '<br /><br />'
            . '<strong>Action taken:</strong> ' . $action . '<br /><br />';
    if (is_array($context) && count($context) > 0)
    {
        echo '<strong>Context at error time:</strong> ' . '<br /><br />'
                . nl2br(print_r($context, true));
    }
    require_once('./installer_foot.php');
    exit;
}

/**
 * @param $errno
 * @param $errstr
 * @param string $errfile
 * @param int $errline
 * @param array $errcontext
 * @return void
 */
function error_php($errno, $errstr, string $errfile = '', int $errline = 0,
                   array $errcontext = []): void
{
    // What's happened?
    // If it's a PHP warning or user error/warning, don't go further - indicates bad code, unsafe
    if ($errno == E_WARNING)
    {
        error_critical('<strong>PHP Warning:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    }
    elseif ($errno == E_RECOVERABLE_ERROR)
    {
        error_critical('<strong>PHP Recoverable Error:</strong> ' . $errstr . ' ('
            . $errno . ')',
            'Line executed: ' . $errfile . ':' . $errline, $errcontext);
    }
    elseif ($errno == E_USER_ERROR)
    {
        error_critical('<strong>User Error:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    }
    elseif ($errno == E_USER_WARNING)
    {
        error_critical('<strong>User Warning:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    } elseif (DEBUG) {
        // Determine the name to display from the error type
        // Only do anything if DEBUG is on, now
        $errname = 'Unknown Error';
        switch ($errno) {
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
        require_once('./installer_head.php'); // in case it hasn't been included
        echo 'A non-critical error has occurred. Page execution will continue. '
            . 'Below are the details:<br /><strong>' . $errname
            . '</strong>: ' . $errstr . ' (' . $errno . ')'
            . '<br /><br />' . '<strong>Line executed</strong>: '
            . $errfile . ':' . $errline . '<br /><br />';
        if (is_array($errcontext) && count($errcontext) > 0) {
            echo '<strong>Context at error time:</strong> '
                . '<br /><br />' . nl2br(print_r($errcontext, true));
        }
    }
}
