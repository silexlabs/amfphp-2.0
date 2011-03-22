<?php
/*
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * a dummy plugin to test if loading in the plugin manager works properly. It does mothing except increment a static counter to count instanciation
 *
 * @package Tests_TestData_TestPlugins_DisabledPlugin
 * @author Ariel Sommeria-klein
 */
class DisabledPlugin {
    public static $instanciationCounter = 0;

    public function  __construct() {
        self::$instanciationCounter++;
    }
}
?>
