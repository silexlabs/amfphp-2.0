<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_QuickServiceDebug
 */

/**
*  includes
*  */
require dirname(__FILE__) . "/AmfphpQuickServiceDebugException.php";
/**
 * A simple service browser with html only. Sometimes you don't need the full thing with AMF etc., so use this
 * This plugin should not be deployed on a production server.
 * 
 * call the gateway with the following GET parameters:
 * serviceName: the service name
 * methodName : the method to call on the service
 *
 * pass the parameters as POST data. Each will be JSON decoded to be able to pass complex parameters. This requires PHP 5.2 or higher
 *
 * if all goes well, the return value will be output. If there is an exception thrown in the plugin because something is missing
 * in the GET and POST data, some useful information and links should be displayed. If there is an exception elsewhere in the code,
 * either in the service object itself or in the rest of amfphp, information about the exception will be shown.
 *
 * @package Amfphp_Plugins_QuickServiceDebug
 * @author Ariel Sommeria-Klein
 */
class AmfphpQuickServiceDebug implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer {

    /**
     * if content type is not set or content is set to "application/x-www-form-urlencoded", this plugin will handle the request
     */
    const CONTENT_TYPE = "application/x-www-form-urlencoded";
    
    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_EXCEPTION_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZER, $this, "filterHandler");
    }

    /**
     * if no content type, then returns this. 
     * @param mixed null at call in gateway.
     * @param String $contentType
     * @return this or null
     */
    public function filterHandler($handler, $contentType){
        if(!$contentType || $contentType == self::CONTENT_TYPE){
            return $this;
        }
    }

    /**
     * @see Amfphp_Core_Common_IDeserializer
     */
    public function deserialize(array $getData, array $postData, $rawPostData){
        $ret = new stdClass();
        $ret->get = $getData;
        $ret->post = $postData;
        return $ret;
    }

    /**
     * adds an item to an array if and only if a duplicate is not already in the array
     * @param array $targetArray
     * @param <type> $toAdd
     * @return array
     */
    private function addToArrayIfUnique(array $targetArray, $toAdd){
        foreach($targetArray as $value){
            if($value == $toAdd){
                return $targetArray;
            }
        }
        $targetArray[] = $toAdd;
        return $targetArray;
    }
    /**
     * returns a list of available services
     * @return array of service names
     */
    private function getAvailableServiceNames(array $serviceFolderPaths, array $serviceNames2ClassFindInfo){
        $ret = array();
        foreach ($serviceFolderPaths as $serviceFolderPath) {
            $folderContent = scandir($serviceFolderPath);

            foreach($folderContent as $fileName){
                //add all .php file names, but removing the .php suffix
                if(strpos($fileName, ".php")){
                    $serviceName = substr($fileName, 0, strlen($fileName) - 4);
                    $ret = $this->addToArrayIfUnique($ret, $serviceName);
                }
            }

            
        }

        foreach ($serviceNames2ClassFindInfo as $key => $value) {
            $ret = $this->addToArrayIfUnique($ret, $key);
        }
        
        return $ret;
    }

    /**
     * throws an exception with an html fragment saying that the serviceName parameter is missing and containing information about available services
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     */
    private function throwMissingServiceNameException(Amfphp_Core_Common_ServiceRouter $serviceRouter){
        $availableServiceNames = $this->getAvailableServiceNames($serviceRouter->serviceFolderPaths, $serviceRouter->serviceNames2ClassFindInfo);
        $message = "Click below to use a service : ";
        $message .= "\n<ul>";
        foreach ($availableServiceNames as $serviceName){
            $message .= "\n     <li><a href='?serviceName=$serviceName'>$serviceName</a></li>";
        }
        $message .= "\n</ul>";
        throw new AmfphpQuickServiceDebugException($message);
    }

    /**
     * throws an Exception with an html fragment saying that the methodName parameter is missing and containing information about avialable methods
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     * @param <type> $serviceName
     */
    private function throwMissingMethodNameException(Amfphp_Core_Common_ServiceRouter $serviceRouter, $serviceName){
        $serviceObject = $serviceRouter->getServiceObject($serviceName);
        $reflectionObj = new ReflectionObject($serviceObject);
        $availablePublicMethods = $reflectionObj->getMethods(ReflectionMethod::IS_PUBLIC);

        $message = "Click below to use a method on the $serviceName service : ";
        $message .= "\n<ul>";
        foreach ($availablePublicMethods as $methodDescriptor){
            $methodName = $methodDescriptor->name;
            $message .= "\n     <li><a href='?serviceName=$serviceName&methodName=$methodName'>$methodName</a></li>";
        }
        $message .= "\n</ul>";
        throw new AmfphpQuickServiceDebugException($message);
    }

    
    private function throwMissingParametersException(Amfphp_Core_Common_ServiceRouter $serviceRouter, $serviceName, $methodName){
        $serviceObject = $serviceRouter->getServiceObject($serviceName);
        $reflectionObj = new ReflectionObject($serviceObject);
        $method = $reflectionObj->getMethod($methodName);
        $parameterDescriptors = $method->getParameters();
        if(count($parameterDescriptors) > 0){
            $message = "Fill in the parameters below then click to call the $methodName method on $serviceName service. : ";
            $message .= "\n<br>Use JSON notation for complex values. ";
            $message .= "\n<form action='?serviceName=$serviceName&methodName=$methodName' method='POST'>\n<table>";
            foreach ($parameterDescriptors as $parameterDescriptor){
                $parameterName = $parameterDescriptor->name;
                $message .= "\n     <tr><td>$parameterName</td><td><input name='$parameterName'></td></tr>";
            }
            $message .= "\n</table>\n<input type='submit'></form>";
        }else{
            $message = "This method has no parameters. Click to call it.";
            $message .= "\n<form action='?serviceName=$serviceName&methodName=$methodName&noParams' method='POST'>\n";
            $message .= "\n<input type='submit'></form>";
        }
        throw new AmfphpQuickServiceDebugException($message);

    }

    /**
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter){
        $serviceName = null;
        if(isset ($deserializedRequest->get["serviceName"])){
            $serviceName = $deserializedRequest->get["serviceName"];
        }else{
            $this->throwMissingServiceNameException($serviceRouter);
        }

        $methodName = null;
        if(isset ($deserializedRequest->get["methodName"])){
            $methodName = $deserializedRequest->get["methodName"];
        }else{
            $this->throwMissingMethodNameException($serviceRouter, $serviceName);
        }

        $parameters = null;

        //if a method has parameters, they are set in post. If it has no parameters, set noParams in the GET.
        //if neither case is applicable, an error message with a form allowing the user to set the values is shown
        if(isset ($deserializedRequest->post) && $deserializedRequest->post != null){
            $parameters = $deserializedRequest->post;
        }else if(isset($deserializedRequest->get["noParams"])){
            $parameters = array();
            for($i = 0; $i < count($parameters); $i++){
                $parameters[$i] = json_decode($parameters[$i]);
            }
        }else{
            $this->throwMissingParametersException($serviceRouter, $serviceName, $methodName);
        }
        //throw new Exception("debug exception " . print_r($parameters, true));
        return $serviceRouter->executeServiceCall($serviceName, $methodName, $parameters);
        
    }

    /**
     * @todo show stack trace
     * @see Amfphp_Core_Common_IExceptionHandler
     */
    public function handleException(Exception $exception){
        $exceptionInfo = null;
        if(is_a($exception, "AmfphpQuickServiceDebugException")){
            $exceptionInfo = $exception->getMessage();

        }else{
            $exceptionInfo = "Exception thrown\n<br>";
            $exceptionInfo .= "message : " . $exception->getMessage() . "\n<br>";
            $exceptionInfo .= "code : " . $exception->getCode() . "\n<br>";
            $exceptionInfo .= "file : " . $exception->getFile() . "\n<br>";
            $exceptionInfo .= "line : " . $exception->getLine() . "\n<br>";
            //$exceptionInfo .= "trace : " . str_replace("\n", "<br>\n", print_r($exception->getTrace(), true)) . "\n<br>";
        }

        return "<html>\n<body>\n $exceptionInfo \n</body>\n</html>";
        
    }
    
    /**
     * @see Amfphp_Core_Common_ISerializer
     */
    public function serialize($data){
        return $data;

    }


}
?>
