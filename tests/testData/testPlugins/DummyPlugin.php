<?php
/**
 * a dummy plugin to test if loading in the plugin manager works properly. It does mothing except increment a static counter to count instanciation
 *
 * @author Ariel Sommeria-klein
 */
class DummyPlugin {
    public static $instanciationCounter = 0;

    public function  __construct() {
        self::$instanciationCounter++;
    }
}
?>
