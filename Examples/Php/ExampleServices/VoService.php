<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Examples_ExampleService
 */

/**
 * an example service for typed objects
 * @package Amfphp_Examples_ExampleService
 * @author Ariel Sommeria-klein
 */
class VoService {
    
    /**
     * This method expects a UserVo1 object. So amfPHP must receive a typed UserVo1 object, and the CustomClassConverter plugin
     * must find the UserVo1 class in its value object folders
     * @param UserVo1 $user
     * @return UserVo1
     */
    public function receiveAndReturnUserVo1(UserVo1 $user){
        $user->status = 'the received object is strongly typed';
        return $user; 
    }
    
    /**
     * This method accepts any object. The idea here is that you send an object that is typed(see client example prjects)
     * and it will be returned. This allows you to use typing on the client side without needing to create
     * corresponding PHP VO classes. If you do this exclusively, you can disable the CustomClassConverter plugin,
     * which will increase performance.
     * amfPHP does this by using the reserved '_explicitType' marker on anonymous PHP objects.
     * 
     * @param type $user
     * @return type 
     */
    public function receiveAndReturnUserVo2($user){
        $user->status = 'the received object is not strongly typed';
        return $user; 
    }
}

?>
