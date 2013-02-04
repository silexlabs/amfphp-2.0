<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_TestData_TestPlugins_DummyPlugin
 */

/**
 * a dummy plugin to test if loading in the plugin manager works properly. It does mothing except increment a static counter to count instanciation
 *
 * @package Tests_TestData_TestPlugins_DummyPlugin
 * @author Ariel Sommeria-klein
 */
class DummyPlugin {

    /**
     * instanciation counter
     * @var int
     */
    public static $instanciationCounter = 0;

    /**
     * dummy conf variable
     * @var string
     */
    public static $dummyConfVar = 'default';

    /**
     * constructor
     * @param array $pluginConfig
     */
    public function __construct(array $pluginConfig = null) {
        self::$instanciationCounter++;
        if ($pluginConfig) {
            if (isset($pluginConfig['dummyConfVar'])) {
                self::$dummyConfVar = $pluginConfig['dummyConfVar'];
            }
        }
    }

}

?>
