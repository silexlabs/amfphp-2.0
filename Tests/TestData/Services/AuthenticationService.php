<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_TestData_Services
 */

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * a simple authentication service, used for testing the AmfphpAuthentication plugin
 *
 * @package Tests_TestData_Services
 * @author Ariel Sommeria-klein
 */
class AuthenticationService
{
    /**
     * test login function
     *
     * @param  String $userId
     * @param  String $password
     * @return String
     */
    public function login($userId, $password)
    {
        if (($userId == 'user') && ($password == 'userPassword')) {
            AmfphpAuthentication::addRole('user');

            return 'user';
        }
        if (($userId == 'admin') && ($password == 'adminPassword')) {
            AmfphpAuthentication::addRole('admin');

            return 'admin';
        }
        throw new Exception("bad credentials");
    }

    /**
     * test logout function
     */
    public function logout()
    {
        AmfphpAuthentication::clearSessionInfo();
    }

    /**
     * function the authentication plugin uses to get accepted roles for each function
     * Here login and logout are not protected, however
     * @param  String $methodName
     * @return array
     */
    public function _getMethodRoles($methodName)
    {
       if ($methodName == 'adminMethod') {
           return array('admin');
       } else {
           return null;
       }
    }

    /**
     * method that is protected by authentication. Only 'admin' role is authorized. (see _getMethodRoles)
     * @return <String> 'ok'
     */
    public function adminMethod()
    {
        return 'ok';
    }

}
