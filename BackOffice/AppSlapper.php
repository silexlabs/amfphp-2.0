<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice_ClientGenerator
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');
require_once(dirname(__FILE__) . '/ClientGenerator/Generators/AmfphpFlashClientGenerator/AmfphpFlashClientGenerator.php');
$addToTitle = ' - App Slapper';
require_once(dirname(__FILE__) . '/Top.php');
echo '<div id="menu"/>';
$config = new Amfphp_BackOffice_Config();

define('APP_SLAPPER_BASE', 'http://localhost:8888/workspaceNetbeans/AppSlapper/');

$ch = curl_init(APP_SLAPPER_BASE . 'description.php');
$content = curl_exec($ch);
$err = curl_errno($ch);
$errmsg = curl_error($ch);
$header = curl_getinfo($ch);
curl_close($ch);
?>

<br/><br/>
Code will be generated for the following services:
<br/><br/>
<?php
$generatorManager = new Amfphp_BackOffice_ClientGenerator_GeneratorManager();
$generators = $generatorManager->loadGenerators(array('ClientGenerator/Generators'));

$config = new Amfphp_BackOffice_Config();
$amfphpUrl = $config->resolveAmfphpEntryPointUrl();
$discoveryServiceCaller = new Amfphp_BackOffice_ServiceCaller($amfphpUrl);
//load service descriptors
$services = $discoveryServiceCaller->makeAmfphpJsonServiceCall("AmfphpDiscoveryService", "discover");

//remove discovery service from list
unset($services->AmfphpDiscoveryService);
//list services
echo '<ul>';
foreach ($services as $service) {
    echo "<li>$service->name</li>";
}
echo '</ul>';
echo "\n        <td><a href=\"?generate\">Generate!</a></td>";

//
if (isset($_GET['generate'])) {

    $appSlapperServiceCaller = new Amfphp_BackOffice_ServiceCaller(APP_SLAPPER_BASE . 'amfphp.php');
    $parameters = array('AppSlapperMobileWebApp', $amfphpUrl, $services);
    $ret = $appSlapperServiceCaller->makeAmfphpJsonServiceCall("ClientGeneratorService", "generate", $parameters);

    echo "<br/><br/>Generated! Project zip available here: <a href='$ret->zipUrl'>$ret->zipUrl</a>";
    if ($ret->testUrlSuffix) {
        //download zip
        $genRootRelativeUrl = 'ClientGenerator/Generated/';
        $genRootFolder = AMFPHP_BACKOFFICE_ROOTPATH. $genRootRelativeUrl;
        $zipPath = $genRootFolder . $ret->zipFileName;

        $fp = fopen($zipPath, 'w');

        $ch = curl_init($ret->zipUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        if (!curl_exec($ch)) {

           throw new Exception("download zip failed at $ret->zipUrl . error : " . curl_error($ch));
        }

        curl_close($ch);
        fclose($fp);

        //unzip generated client
        $zip = new ZipArchive;
        if (!$zip->open($zipPath) === TRUE) {
            throw new Exception("couldn't open zip at $zipPath");
        }
        $zip->extractTo($genRootFolder);
        $zip->close();
        echo "<br/><br/>Downloaded and unpacked for your convenience. <a href='$genRootRelativeUrl$ret->testUrlSuffix'>Try it here</a><br/>";
    }

}
