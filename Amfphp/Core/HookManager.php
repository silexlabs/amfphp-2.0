<?php
/*This file is part of Silex - see http://projects.silexlabs.org/?/silex

Silex is © 2010-2011 Silex Labs and is released under the GPL License:

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License (GPL) as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. 

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/

/**
 * This class is a kind of event dispatcher <br />
 * Hooks are provided by Amfphp to allow your contexts to 'hook into' the rest of Amfphp, i.e. to call functions in your context at specific times<br />
 * This is a singleton, so use getInstance
 */
class Amfphp_Core_HookManager{
    /**
     * The aim of the hook is for the caller to get an object/value from a plugin.
     * The hook manager will look at each callee's return value, and will return the first non null value
     */
    const BEHAVIOR_GETTER = 0;

    /**
     * the aim of the hook is to allow the plugins to manipulate(filter) the data.
     * The first parameter is the value that can be filtered and returned by successive plugins. Any other parameters are
     * to be considered as context and cannot be returned.
     */
    const BEHAVIOR_FILTER = 1;

    /**
     * the aim of the hook is just to allow plugins to inspect the parameters. Any return value will be ignored.
     */
    const BEHAVIOR_GIVER = 2;

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
     * @return Amfphp_Core_HookManager
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Amfphp_Core_HookManager();
        }
        return self::$instance;
    }

     /**
     * call the functions registered for the given hook.
     *
     * @param String $hookName the name of the hook which was used in addHook( a string)
     * @param array $paramsArray the array of the parameters to call the function with
     * @return array $paramsArray, as modified by the hook callees.
     */
    public function callHooks($hookName, array $paramsArray){
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
     * call the functions registered for the given hook. Parameters are passed as an array, and the first member of this array
     * is returned. This first parameter can be modified and returned by the callees, in the case a hook where the aim is to "filter" the data.
     * The other parameters must be considered as context, and should not be modified by the callees, and will not be returned to the caller.
     * 
     * @param String $hookName the name of the hook which was used in addHook( a string)
     * @param array $paramsArray the array of the parameters to call the function with
     * @param int $behavior. See above for consts BEHAVIOR_xxx. 
     * @return array $paramsArray, as modified by the hook callees.
     */
    public function NewcallHooks($hookName, array $paramsArray){
        $fromCallee = null;
        if (isset($this->hooksArray[$hookName])){
            // loop on registered hooks
            foreach($this->hooksArray[$hookName] as $callBack){
                $fromCallee = call_user_func_array($callBack, $paramsArray);
                if($fromCallee){
                    switch ($behavior){
                        case self::BEHAVIOR_GETTER:
                            return $fromCallee;
                        break;
                        case self::BEHAVIOR_FILTER:
                            $paramsArray[0] = $fromCallee;
                        break;
                        default:
                            //nothing!
                        break;
                    }
                }
            }
        }

        if($behavior == self::BEHAVIOR_FILTER){
            return $fromCallee;
        }else{
            return null;
        }
    }



    /**
     * register a function for the given hook<br />
     * call this method in your contexts to be notified when the hook occures<br />
     *
     * Inputs :
     * $hookName : the name of the hook
     * $callBack : Either the name of the global function, array($object, $methodName) or array($className, $staticMethodName).  See http://php.net/manual/en/function.call-user-func.php
     * and http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback for documentation.
     */
    public function addHook($hookName, $callBack){
        // init the hook placeholder
        if (!isset($this->hooksArray[$hookName])) $this->hooksArray[$hookName] = Array();
        // add the hook callback
        $this->hooksArray[$hookName][] = $callBack;
    }
}
?>