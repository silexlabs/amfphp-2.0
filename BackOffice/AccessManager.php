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
    /**
     * the field in the session where the roles array is stored
     */

    const SESSION_FIELD_ROLES = 'amfphp_roles';
    const AMFPHP_ADMIN_ROLE = 'amfphp_admin';

    /**
     * checks if logged. If not checks POST data for login credentials.
     * throws Exception containing user feedback
     * @todo fix session roles to use a dictionary approach, here and in AmfphpAuthentication plugin.
     * Wait for bigger version to break compatibility
     */
    public function isSignedIn() {
        if (session_id() == '') {
            session_start();
        }

        
        if (!isset($_SESSION[self::SESSION_FIELD_ROLES])) {
            return false;
        }
        return isset($_SESSION[self::SESSION_FIELD_ROLES][self::AMFPHP_ADMIN_ROLE]);

    }

}

?>
