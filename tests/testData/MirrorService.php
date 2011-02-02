<?php

/**
 * MirrorService is a test service that has only one function, mirror, which returns the arguments received. This should be made into a plugin
 * as it shouldn't be in the core distribution, but for now it will stay here. 
 *
 * @author Ariel Sommeria-klein
 */

$myFile = "testFile.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = "Bobby Bopper\n";
fwrite($fh, $stringData);
$stringData = "Tracy Tanner\n";
fwrite($fh, $stringData);
fclose($fh);


require_once("./includes/DummyInclude.php");

class MirrorService {

    public function returnOneParam($param){
        return $param;
    }

}
?>
