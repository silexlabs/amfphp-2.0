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
    public function testAccessAllowed() {
        if (session_id() == '') {
            session_start();
        }

        if (isset($_POST['username'])) {
            //user is logging in.
            $username = $_POST['username'];
            $password = $_POST['password'];
            $staySignedIn = isset($_POST['staySignedIn']);
            $config = new Amfphp_BackOffice_Config();
            if (isset($config->backOfficeCredentials[$username]) && ($config->backOfficeCredentials[$username] === $password)) {
                if (!isset($_SESSION[self::SESSION_FIELD_ROLES])) {
                    $_SESSION[self::SESSION_FIELD_ROLES] = array();
                }

                //check role isn't already available
                foreach ($_SESSION[self::SESSION_FIELD_ROLES] as $userRole) {
                    if ($userRole == self::AMFPHP_ADMIN_ROLE) {
                        return;
                    }
                }
                $_SESSION[self::SESSION_FIELD_ROLES][] = self::AMFPHP_ADMIN_ROLE;
            } else {
                throw new Amfphp_Core_Exception('Invalid username/password');
            }
        } else {
            //no credentials in POST, so user should already be logged in
            if (!isset($_SESSION[self::SESSION_FIELD_ROLES])) {
                throw new Amfphp_Core_Exception('');
            }
            $userRoles = $_SESSION[self::SESSION_FIELD_ROLES];

            foreach ($userRoles as $userRole) {

                if ($userRole == self::AMFPHP_ADMIN_ROLE) {
                    //a match is found
                    return;
                }
            }
            throw new Amfphp_Core_Exception('');
        }

    }

}

?>
