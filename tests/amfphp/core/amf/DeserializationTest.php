<?php
/**
 * Unit tests for AMFSerializer
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../testData/AMFTestData.php';

class DeserializationTest extends PHPUnit_Framework_TestCase{


    public function testBasicMethods(){
        $testData = new AMFTestData();
        $deserializer = new AMFDeserializer($testData->mirrorServiceRequestMessage);
        $ret = $deserializer->deserialize();

        $this->assertEquals("bla", $ret);

        

    }
    


}
?>
