<?php


/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 */

  /**
 * Makes a call to the amfphp entry point and returns the data
 * 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice
 */
class Amfphp_BackOffice_AccessManager {
    const IS_LOGGED = 'IS_LOGGED';
    
    /**
     * This looks at the config to see if the backoffice is activated. 
     */
    public function isBackOfficeActivated(){
        $config = new Amfphp_BackOffice_Config();
        return $config->activated;  
    }
    /**
     * checks if logged
     * @return boolean
     */
    public function isLogged(){
        if(session_id () == ''){
            session_start();
        }
        return $_SESSION[IS_LOGGED];
    }
    
}

?>
