<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Monitor

/**
 * monitoring service. controls logging, qnd provides method to fetch data.
 *
 * @package Amfphp_Plugins_Monitor
 * @author Ariel Sommeria-klein
 */
class AmfphpMonitorService {
    public static $logPath;
    

    /**
     * creates log file, which serves as indicator to plugin that it should log.
     */
    public function startLogging(){
        if(!file_exists(self::$logPath)){
            file_put_contents(self::$logPath, '');
        }
    }
    
    /**
     * destroys log file, which serves as indicator to plugin that it should not log.
     */
    public function stopLogging(){
        if(file_exists(self::$logPath)){
            unlink(self::$logPath);
        }
                   
    }
    
    /**
     * get the logged data
     * @todo calculate averages per service instead of just returning the data raw.
     * @param boolean $flush get rid of the logged data (default true)
     * @return array 
     */
    public function getData($flush = true){
        if(!file_exists(self::$logPath)){
            return null;
        }
        $data = file_get_contents(self::$logPath);
        if($flush){
            file_put_contents(self::$logPath, '');
        }
        $exploded = explode("\n", $data);
        $ret = array();
        foreach($exploded as $callData){
            $ret[] = unserialize($callData); 
        }
            
        return $ret;
    }

}

?>
