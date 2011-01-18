<?php
/**
 * Loads plugins for AMFPHP. Plugins consist of one class, declared in a file of the same name, in the plugins folder. A plugin interacts with AMFPHP by using the
 * core_HookManager to register its functions to be called at specific times with specific parameters during execution.
 * singleton, so use getInstance
 * @todo the plugins folder is hard coded. consider a mechanism to make this more flexible
 * @todo some more explanation here for plugin developers.
 *
 * @author Ariel Sommeria-Klein
 */
class core_PluginManager {


    /**
     * private instance of singleton
     * @var <core_PluginManager>
     *
     */
    private static $instance = NULL;

    /**
     *
     * @var <array>
     */
    private $pluginInstances;
    /**
     * constructor
     */
    private function __construct(){
        $this->pluginInstances = array();
    }

    /**
     * gives access to the singleton
     * @return <core_PluginManager>
     */
    public static function getInstance() {
            if (self::$instance == NULL) {
                    self::$instance = new core_PluginManager();
            }
            return self::$instance;
    }

    /**
     * load the plugins
     */
    public function loadPlugins($rootFolder){
        $pluginsFolderRootPath = $rootFolder;
        $folderContent = scandir($pluginsFolderRootPath);
        $pluginDescriptors = array();

        foreach($folderContent as $fileName){
            $phpSuffixPos = strpos($fileName, ".php");
            if($phpSuffixPos != false){
                require_once $pluginsFolderRootPath . $fileName;
                $className = substr($fileName, 0, $phpSuffixPos);
                $pluginInstance = new $className();
                array_push($this->pluginInstances, $pluginInstance);
            }
        }
        
    }
}
?>
