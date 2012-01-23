<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_ErrorHandler
 */

/**
 * sets a custom error handler to catch notices and such and transform them to exceptions.
 * 
 * This is a bit experimental and only really useful when getting badly formed responses through errors. so disabled by default
 *  
 * @todo this could be enhanced to use filters so that at the end of the gateway execution the error handling is set back to normal. This could be useful especially for integration with frameworks.
 * @package Amfphp_Plugins_ErrorHandler
 * @author Ariel Sommeria-Klein
 */

class AmfphpErrorHandler {
     /**
     * constructor. Add filters on the HookManager.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {
        set_error_handler('custom_warning_handler');
    }
}

function custom_warning_handler($errno, $errstr, $errfile, $errline, $errcontext) {
    throw new Exception("$errstr . \n<br>file:  $errfile \n<br>line: $errline \n<br>context: " . print_r($errcontext, true), $errno);
}

?>
