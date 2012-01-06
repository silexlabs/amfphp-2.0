<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Core
 */

/**
 * This class is a kind of event dispatcher <br />
 * Filters are provided by Amfphp to allow your contexts to 'filter into' the rest of Amfphp, i.e. to call functions in your context at specific times<br />
 * This is a singleton, so use getInstance
 * @package Amfphp_Core
 * @author Ariel Sommeria-klein
 *  */
class Amfphp_Core_FilterManager{
    /**
     * registered filters
     */
    protected $filtersArray = NULL;

    /**
    *protected instance of singleton
    */
    protected static $instance = NULL;
    /**
     * constructor
     */
    protected function __construct(){
        $this->filtersArray = Array();
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
     * call the functions registered for the given filter. There can be as many parameters as necessary, but only the first
     * one can be changed and and returned by the callees.
     * The other parameters must be considered as context, and should not be modified by the callees, and will not be returned to the caller.
     * 
     * @param String $filterName the name of the filter which was used in addFilter( a string)
     * @param parameters for the function call. As many as necessary can be passed, but only the first will be filtered
     * @return mixed the first call parameter, as filtered by the callees.
     */
    public function callFilters(){
        
        //get arguments with which to call the function. All except first, which is the filter name
        $filterArgs = func_get_args();
        $filterName = array_shift($filterArgs);
        //throw new Exception('filterArgs ' . print_r($filterArgs, true));
        $filtered = $filterArgs[0];
        if (isset($this->filtersArray[$filterName])){
            // loop on registered filters
            foreach($this->filtersArray[$filterName] as $callBack){
                $fromCallee = call_user_func_array($callBack, $filterArgs);
                if($fromCallee !== null){ //!== null because otherwise array() doesn't qualify
                    $filtered = $filterArgs[0] = $fromCallee;
                }
            }
        }

        return $filtered;
    }



    /**
     * register an object method for the given filter
     * call this method in your contexts to be notified when the filter occures
     * @see http://php.net/manual/en/function.call-user-func.php
     * @see http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback
     *
     * 
     * @param String $filterName  the name of the filter
     * @param Object $object the object on which to call the method
     * @param String $methodName the name of the method to call on the object
     */
    public function addFilter($filterName, $object, $methodName){
        // init the filter placeholder
        if (!isset($this->filtersArray[$filterName])) $this->filtersArray[$filterName] = Array();
        // add the filter callback
        $this->filtersArray[$filterName][] = array($object, $methodName);
    }
}
?>