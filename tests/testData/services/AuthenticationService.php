<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * a simple authentication service, used for testing the AMFPHPAuthentication plugin
 *
 * @author Ariel Sommeria-klein
 */
class AuthenticationService {

    public function login($userid, $password){
        if(($userId == "user") && ($password == "userPassword")){
            return "user";
        }
        if(($userId == "admin") && ($password == "adminPassword")){
            return "admin";
        }

        return null;
    }

    public function logout(){
        AMFPHPAuthentication::clearSessionInfo();
    }
}
?>
