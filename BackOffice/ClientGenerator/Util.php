<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp__BackOffice_ClientGenerator
 *
 */

/**
 * common utilities for generators
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp__BackOffice_ClientGenerator
 */
class Amfphp_BackOffice_ClientGenerator_Util
{
    /**
     * recursively copies one folder to another.
     * @param string $src
     * @param string $dst must not exist yet
     */
    public static function recurseCopy($src, $dst)
    {
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

    /**
     *
     * @param type $sourcefolder
     * @param type $zipfilename
     * @param type $removeFromLocalName use to reduce paths inside zip
     */
    public function zipFolder($sourcefolder, $zipfilename, $removeFromLocalName)
    {
// instantate an iterator (before creating the zip archive, just
// in case the zip file is created inside the source folder)
// and traverse the directory to get the file list.
        $dirlist = new RecursiveDirectoryIterator($sourcefolder);
        $filelist = new RecursiveIteratorIterator($dirlist);

// instantate object
        $zip = new ZipArchive();

// create and open the archive
        if ($zip->open("$zipfilename", ZipArchive::CREATE) !== TRUE) {
            throw new Exception("Could not open archive");
        }

// add each file in the file list to the archive
        foreach ($filelist as $value) {
            $localName = str_replace($removeFromLocalName, '', $value);
            if (!$zip->addFile(realpath($value), $localName)) {
                throw new Exception("ERROR: Could not add file: $key");
            }
        }

// close the archive
        $zip->close();
    }

    // ------------ lixlpixel recursive PHP functions -------------
    // recursive_remove_directory( directory to delete, empty )
    // expects path to directory and optional TRUE / FALSE to empty
    // of course PHP has to have the rights to delete the directory
    // you specify and all files and folders inside the directory
    // ------------------------------------------------------------
    // to use this function to totally remove a directory, write:
    // recursive_remove_directory('path/to/directory/to/delete');
    // to use this function to empty a directory, write:
    // recursive_remove_directory('path/to/full_directory',TRUE);

    public static function recursive_remove_directory($directory, $empty=FALSE)
    {
        // if the path has a slash at the end we remove it here
        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }

        // if the path is not valid or is not a directory ...
        if (!file_exists($directory) || !is_dir($directory)) {
            // ... we return false and exit the function
            return FALSE;

            // ... if the path is not readable
        } elseif (!is_readable($directory)) {
            // ... we return false and exit the function
            return FALSE;

            // ... else if the path is readable
        } else {

            // we open the directory
            $handle = opendir($directory);

            // and scan through the items inside
            while (FALSE !== ($item = readdir($handle))) {
                // if the filepointer is not the current directory
                // or the parent directory
                if ($item != '.' && $item != '..') {
                    // we build the new path to delete
                    $path = $directory . '/' . $item;

                    // if the new path is a directory
                    if (is_dir($path)) {
                        // we call this function with the new path
                        self::recursive_remove_directory($path);

                        // if the new path is a file
                    } else {
                        // we remove the file
                        unlink($path);
                    }
                }
            }
            // close the directory
            closedir($handle);

            // if the option to empty is not set to true
            if ($empty == FALSE) {
                // try to delete the now empty directory
                if (!rmdir($directory)) {
                    // return false if not possible
                    return FALSE;
                }
            }
            // return success
            return TRUE;
        }
    }

}
