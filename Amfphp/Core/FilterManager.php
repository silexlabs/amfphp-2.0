<?php
/**
 *  This file part is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/*This file is part of Silex - see http://projects.silexlabs.org/?/silex

Silex is © 2010-2011 Silex Labs and is released under the GPL License:

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License (GPL) as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. 

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/

/**
 * This class is a kind of event dispatcher <br />
 * Filters are provided by Amfphp to allow your contexts to 'hook into' the rest of Amfphp, i.e. to call functions in your context at specific times<br />
 * This is a singleton, so use getInstance
 */
class Amfphp_Core_FilterManager{
    /**
     * registered hooks
     */
    private $hooksArray = NULL;

    /**
    *private instance of singleton
    */
    private static $instance = NULL;
    /**
     * constructor
     */
    private function __construct(){
        $this->hooksArray = Array();
    }

    /**
     *
     * @return Amfphp_Core_FilterManager
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Amfphp_Core_FilterManager();
        }
        return self::$instance;
    }

     /**
     * call the functions registered for the given hook.
     *
     * @param String $hookName the name of the hook which was used in addFilter( a string)
     * @param array $paramsArray the array of the parameters to call the function with
     * @return array $paramsArray, as modified by the hook callees.
     */
    public function oldcallFilters($hookName, array $paramsArray){
            if (isset($this->hooksArray[$hookName])){
                    // loop on registered hooks
                    foreach($this->hooksArray[$hookName] as $callBack){
                        $ret = call_user_func_array($callBack, $paramsArray);
                        if($ret){
                            if(!is_array($ret)){
                                throw new Amfphp_Core_Exception("hooked method for $hookName must return array");
                            }
                            if(count($ret) != count($paramsArray)){
                                throw new Amfphp_Core_Exception("hooked function for $hookName returned array size doesn't match. returned : " . count($ret) . ", expected : " . count($paramsArray));
                            }
                            $paramsArray = $ret;
                        }
                    }
            }
            return $paramsArray;
    }


    /**
     * call the functions registered for the given hook. There can be as many parameters as necessary, but only the first
     * one can be changed and and returned by the callees.
     * The other parameters must be considered as context, and should not be modified by the callees, and will not be returned to the caller.
     * 
     * @param String $hookName the name of the hook which was used in addFilter( a string)
     * @param parameters for the function call. As many as necessary can be passed, but only the first will be filtered
     * @return mixed the first call parameter, as filtered by the callees.
     */
    public function callFilters(){
        
        //get arguments with which to call the function. All except first, which is the hook name
        $hookArgs = func_get_args();
        $hookName = array_shift($hookArgs);
        //throw new Exception("hookArgs " . print_r($hookArgs, true));
        $filtered = $hookArgs[0];
        if (isset($this->hooksArray[$hookName])){
            // loop on registered hooks
            foreach($this->hooksArray[$hookName] as $callBack){
                $fromCallee = call_user_func_array($callBack, $hookArgs);
                if($fromCallee){
                    $filtered = $hookArgs[0] = $fromCallee;
                }
            }
        }

        return $filtered;
    }



    /**
     * register an object method for the given hook
     * call this method in your contexts to be notified when the hook occures
     * @see http://php.net/manual/en/function.call-user-func.php
     * @see http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback
     *
     * 
     * @param String $hookName  the name of the hook
     * @param Object $object the object on which to call the method
     * @param String $methodName the name of the method to call on the object
     */
    public function addFilter($hookName, $object, $methodName){
        // init the hook placeholder
        if (!isset($this->hooksArray[$hookName])) $this->hooksArray[$hookName] = Array();
        // add the hook callback
        $this->hooksArray[$hookName][] = array($object, $methodName);
    }
}
?>