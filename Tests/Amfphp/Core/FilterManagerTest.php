<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Core
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../Amfphp/ClassLoader.php';

/**
 * Test class for Amfphp_Core_FilterManager.
 * @package Tests_Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_FilterManagerTest extends PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        //add the same filter twice to test filtering
        $filterManager->addFilter('TESTFILTER', $this, 'double');
        $filterManager->addFilter('TESTFILTER', $this, 'double');

        $ret = $filterManager->callFilters('TESTFILTER', 1);
        $this->assertEquals(4, $ret);

    }
    /**
     *  at the end of the test $testArray should contain 3, 1, 1, 2
     */
    public function testPriorities()
    {
        $testArray = array();
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter('TESTPRIORITIES', $this, 'push1');
        $filterManager->addFilter('TESTPRIORITIES', $this, 'push2', 20);
        $filterManager->addFilter('TESTPRIORITIES', $this, 'push3', 1);
        //same priority, should be called after
        $filterManager->addFilter('TESTPRIORITIES', $this, 'push1', 1);

        $ret = $filterManager->callFilters('TESTPRIORITIES', $testArray);
        $this->assertEquals(3, $ret[0]);
        $this->assertEquals(1, $ret[1]);
        $this->assertEquals(1, $ret[2]);
        $this->assertEquals(2, $ret[3]);

    }

    /**
     * note: this function must be public to be called. This is called by filter
     * @param  number $valueToDouble
     * @return number
     */
    public function double($valueToDouble)
    {
        return $valueToDouble * 2;
    }

    /**
     * note: this function must be public to be called. This is called by filter
     *
     */
    public function push1(array $testArray)
    {
        $testArray[] = 1;

        return $testArray;
    }

    /**
     * note: this function must be public to be called. This is called by filter
     *
     */
    public function push2(array $testArray)
    {
        $testArray[] = 2;

        return $testArray;
    }

    /**
     * note: this function must be public to be called. This is called by filter
     *
     */
    public function push3(array $testArray)
    {
        $testArray[] = 3;

        return $testArray;
    }

}
