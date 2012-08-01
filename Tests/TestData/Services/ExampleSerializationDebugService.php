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
 * this service illustrates parsing a PHP serialized object to load test data. Use this as an example when
* illustrating a possible bug that involves specific data.
 * The getSerializedObject method shows how to PHP serialize an object.
 * The getDataThatCreatesProblems method is the one you need to implement.
 * MyVO is an example VO object.
 *
 * @package Tests_TestData_Services
 * @author Ariel Sommeria-klein
 */

class ExampleSerializationDebugService
{
    public function getDataThatCreatesProblems()
    {
        return unserialize('O:4:"MyVO":2:{s:4:"var1";s:4:"val1";s:4:"var2";s:4:"val2";}');
    }

    public function getSerializedObject()
    {
        $ret = new MyVO();
        $ret->var1 = "val1";
        $ret->var2 = "val2";

        return serialize($ret);
    }

}

/**
 *
 * @package Tests_TestData_Services
 */
class MyVO
{
    public $var1;
    public $var2;
}
