<?php
/*This file is part of Silex - see http://projects.silexlabs.org/?/silex

Silex is © 2010-2011 Silex Labs and is released under the GPL License:

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License (GPL) as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. 

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/

/**
 * This class is a kind of event dispatcher <br />
 * Hooks are provided by AMFPHP to allow your contexts to 'hook into' the rest of AMFPHP, i.e. to call functions in your context at specific times<br />
 * This is a singleton, so use getInstance
 */
class HookManager{
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
                //$this->logger->debug($action . " $siteName/$fileName to $siteName/$newFileName");
                //$this->logger->err("modifying $siteFolderPath/$fileName not allowed");
        }

        /**
         *
         * @return <HookManager>
         */
        public static function getInstance() {
                if (self::$instance == NULL) {
                        self::$instance = new HookManager();
                }
                return self::$instance;
        }

        /**
         * call the functions registered for the given hook. 
         *
         * @param <String> $hookName the name of the hook which was used in addHook( a string)
         * @param <array> $paramsArray the array of the parameters to call the function with
         * @return <array> $paramsArray, as modified by the hook callees.
         */
        public function callHooks($hookName, $paramsArray){
                if (isset($this->hooksArray[$hookName])){
                        // loop on registered hooks
                        foreach($this->hooksArray[$hookName] as $callBack){
                            if($paramsArray){
                                $paramsArray = call_user_func_array($callBack, $paramsArray);
                            }
                        }
                }
                return $paramsArray;
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