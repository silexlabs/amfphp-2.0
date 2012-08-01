<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_TestData_Services
 */

/**
 * a service for tests on byte arrays
 *
 * @package Tests_TestData_Services
 * @author Ariel Sommeria-klein
 */

class ByteArrayTestService
{
    /**
     * uncompresses a bytearray, writes the uncompressed data to a file, received.jpg
     * @param  Amfphp_Core_Amf_Types_ByteArray $ba a comporessed bytearray
     * @return true
     */
    public function uploadCompressedByteArray(Amfphp_Core_Amf_Types_ByteArray $ba)
    {
        $uncompressed = gzuncompress($ba->data);
        //uncomment to save file to jpeg
        //file_put_contents('received.jpg', $uncompressed);
        return true;

    }
}
