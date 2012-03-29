<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp__BackOffice_ClientGenerator

  /**
 * common utilities for generators
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp__BackOffice_ClientGenerator
 */
class Amfphp_BackOffice_ClientGenerator_Util {

    /**
     * recursively copies one folder to another.
     * @param string $src
     * @param string $dst must not exist yet
     */
    public static function recurseCopy($src, $dst) {
        $dir = opendir($src);
        mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function getGeneratedProjectDestinationFolder($generatorName) {
        return AMFPHP_BACKOFFICE_ROOTPATH . 'ClientGenerator/Generated/' . date("Ymd-his-") . $generatorName;
    }



}

?>
