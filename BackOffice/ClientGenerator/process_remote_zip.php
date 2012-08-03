<?php
/**
 * downloads and unpacks a generated client. outputs url for test
 */
$zipFileName = $_POST('zipFileName');
//download zip
$genRootRelativeUrl = 'ClientGenerator/Generated/';
$genRootFolder = AMFPHP_BACKOFFICE_ROOTPATH . $genRootRelativeUrl;
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
?>
