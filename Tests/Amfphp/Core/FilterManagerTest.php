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
class Amfphp_Core_FilterManagerTest extends PHPUnit_Framework_TestCase {
    public function testFilter() {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        //add the same filter twice to test filtering
        $filterManager->addFilter('TESTFILTER', $this, 'double');
        $filterManager->addFilter('TESTFILTER', $this, 'double');

        $ret = $filterManager->callFilters('TESTFILTER', 1);
        $this->assertEquals(4, $ret);


    }

    /**
     * note: this function must be public to be called. This is called by filter
     * @param <type> $valueToDouble
     * @return <type>
     */
    public function double($valueToDouble){
        return $valueToDouble * 2;
    }

}

?>
