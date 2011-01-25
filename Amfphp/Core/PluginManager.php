<?php
/**
 * Loads plugins for AMFPHP. Plugins consist of one class, declared in a file of the same name, in the plugins folder. A plugin interacts with AMFPHP by using the
 * Amfphp_Core_HookManager to register its functions to be called at specific times with specific parameters during execution.
 * singleton, so use getInstance
 * @todo the plugins folder is hard coded. consider a mechanism to make this more flexible
 * @todo some more explanation here for plugin developers.
 *
 * @author Ariel Sommeria-Klein
 */
class Amfphp_Core_PluginManager {


    /**
     * private instance of singleton
     * @var Amfphp_Core_PluginManager
     *
     */
    private static $instance = NULL;

    /**
     *
     * @var array
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
     * @return Amfphp_Core_PluginManager
     */
    public static function getInstance() {
            if (self::$instance == NULL) {
                    self::$instance = new Amfphp_Core_PluginManager();
            }
            return self::$instance;
    }

    /**
     * load the plugins
     * @param String $rootFolder where to load the plugins from. Absolute path.
     * @param array $pluginConfig . optional. an array containing the plugin configuration, using the plugin name as key.
     * @param array $disabledPlugins . optional.  an array of names of plugins to disable
     */
    public function loadPlugins($rootFolder, array $pluginsConfig = null, array $disabledPlugins = null){
        $pluginsFolderRootPath = $rootFolder;
        $folderContent = scandir($pluginsFolderRootPath);
        $pluginDescriptors = array();

        foreach($folderContent as $fileName){
            $phpSuffixPos = strpos($fileName, ".php");
            if($phpSuffixPos == false){
                continue;
            }
            $className = substr($fileName, 0, $phpSuffixPos);

            //check first if plugin is disabled
            $shouldInstanciatePlugin = true;
            if($disabledPlugins){
                foreach($disabledPlugins as $disabledPlugin){
                    if($disabledPlugin == $className){
                        $shouldInstanciatePlugin = false;
                    }
                }
            }
            if(!$shouldInstanciatePlugin){
                continue;
            }
            
            if(!class_exists($className)){
                require_once $pluginsFolderRootPath . $fileName;
            }
            $pluginConfig = null;
            if(isset($pluginsConfig[$className])){
                $pluginConfig = $pluginsConfig[$className];
            }
            $pluginInstance = new $className($pluginConfig);
            array_push($this->pluginInstances, $pluginInstance);
        }
        
    }
}
?>
