<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_ServiceBrowser
 */

/**
 * A simple service browser with html only. Sometimes you don't need the full thing with AMF etc., so use this
 * This plugin should be deactivated on a production server.
 * 
 * call the gateway with the following GET parameters:
 * serviceName: the service name
 * methodName : the method to call on the service
 *
 * pass the parameters as POST data. Each will be JSON decoded to be able to pass complex parameters. This requires PHP 5.2 or higher
 *
 * @package Amfphp_Plugins_ServiceBrowser
 * @author Ariel Sommeria-Klein, Daniel Hoffmann (intermedi8.de) 
 */
class AmfphpServiceBrowser implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer {
    /**
* if content type is not set or content is set to "application/x-www-form-urlencoded", this plugin will handle the request
*/
    const CONTENT_TYPE = "application/x-www-form-urlencoded";

    protected $serviceName;
    protected $methodName;

    /**
* used for service call
* @var array
*/
    protected $parameters;

    /**
* associative array of parameters. Used to set the parameters input fields to the same values again after a call.
* note: stored encoded because that's the way we need them to show them in the dialog
* @var array
*/
    protected $parametersAssoc;
    protected $serviceRouter;
    protected $showResult;
    protected $callStartTimeMs;
    protected $callDurationMs;
    protected $returnErrorDetails = false;

    /**
* constructor.
* @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
*/
    public function __construct(array $config = null) {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_EXCEPTION_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_HEADERS, $this, "filterHeaders");
        $this->returnErrorDetails = (isset($config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]) && $config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]);
    }

    /**
* if no content type, then returns this.
* @param mixed null at call in gateway.
* @param String $contentType
* @return this or null
*/
    public function filterHandler($handler, $contentType) {
        if (!$contentType || $contentType == self::CONTENT_TYPE) {
            return $this;
        }
    }

    /**
* @see Amfphp_Core_Common_IDeserializer
*/
    public function deserialize(array $getData, array $postData, $rawPostData) {
        $ret = new stdClass();
        $ret->get = $getData;
        $ret->post = $postData;
        return $ret;
    }
    
    /**
* finds classes in folder. If in subfolders add the relative path to the name.
* recursive, so use with care.
* @param type $rootPath
* @param type $subFolder
* @return type
*/
    protected function searchFolderForServices($rootPath, $subFolder){
        $ret = array();
        $folderContent = scandir($rootPath . $subFolder);

        if ($folderContent) {
            foreach ($folderContent as $fileName) {
                //add all .php file names, but removing the .php suffix
                if (strpos($fileName, ".php")) {
                    $serviceName = substr($fileName, 0, strlen($fileName) - 4);
                    $ret[] = $subFolder . $serviceName;
                }else if((substr ($fileName, 0, 1) != '.') && is_dir($rootPath . $subFolder . $fileName)){
                    $ret = array_merge($ret, $this->searchFolderForServices($rootPath, $subFolder . $fileName . '/'));
                }
            }
        }
        return $ret;
        
    }

    /**
* returns a list of available services
* @return array of service names
*/
    protected function getAvailableServiceNames(array $serviceFolderPaths, array $serviceNames2ClassFindInfo) {
        $ret = array();
        foreach ($serviceFolderPaths as $serviceFolderPath) {
            $ret += $this->searchFolderForServices($serviceFolderPath, '');
        }

        foreach ($serviceNames2ClassFindInfo as $key => $value) {
            $ret[] = $key;
        }

        return $ret;
    }

    /**
* @see Amfphp_Core_Common_IDeserializedRequestHandler
*/
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter) {
        $this->serviceRouter = $serviceRouter;

        if (isset($deserializedRequest->get["serviceName"])) {
            $this->serviceName = $deserializedRequest->get["serviceName"];
        }

        if (isset($deserializedRequest->get["methodName"])) {
            $this->methodName = $deserializedRequest->get["methodName"];
        }


        //if a method has parameters, they are set in post. If it has no parameters, set noParams in the GET.
        //if neither case is applicable, an error message with a form allowing the user to set the values is shown
        $paramsGiven = false;
        if (isset($deserializedRequest->post) && $deserializedRequest->post != null) {
            $this->parameters = array();
            $this->parametersAssoc = array();
            //try to json decode each parameter, then push it to $thios->parameters
            $numParams = count($deserializedRequest->post);
            foreach ($deserializedRequest->post as $key => $value) {
                $this->parametersAssoc[$key] = $value;
                $decodedValue = json_decode($value);
                $valueToUse = $value;
                if ($decodedValue) {
                    $valueToUse = $decodedValue;
                }
                $this->parameters[] = $valueToUse;
            }
            $paramsGiven = true;
        } else if (isset($deserializedRequest->get["noParams"])) {
            $this->parameters = array();
            $paramsGiven = true;
            //note: use $paramsGiven because somehow if $$this->parameters contains an empty array, ($this->parameters == null) is true.
        }

        if ($this->serviceName && $this->methodName && $paramsGiven) {
            $this->showResult = true;
            $this->callStartTimeMs = microtime(true);
            $ret = $serviceRouter->executeServiceCall($this->serviceName, $this->methodName, $this->parameters);
            $this->callDurationMs = round((microtime(true) - $this->callStartTimeMs) * 1000);
            return $ret;
        } else {
            $this->showResult = false;
            return null;
        }
    }

    /**
* @todo show stack trace
* @see Amfphp_Core_Common_IExceptionHandler
*/
    public function handleException(Exception $exception) {
        $exceptionInfo = "Exception thrown\n<br>";
        $exceptionInfo .= "message : " . $exception->getMessage() . "\n<br>";
        $exceptionInfo .= "code : " . $exception->getCode() . "\n<br>";
        if ($this->returnErrorDetails) {
            $exceptionInfo .= "file : " . $exception->getFile() . "\n<br>";
            $exceptionInfo .= "line : " . $exception->getLine() . "\n<br>";
        }
        //$exceptionInfo .= "trace : " . str_replace("\n", "<br>\n", print_r($exception->getTrace(), true)) . "\n<br>";
        $this->showResult = true;
        $this->callDurationMs = round((microtime(true) - $this->callStartTimeMs) * 1000);
        return $exceptionInfo;
    }

    /**
* @see Amfphp_Core_Common_ISerializer
*/
    public function serialize($data) {

        $availableServiceNames = $this->getAvailableServiceNames($this->serviceRouter->serviceFolderPaths, $this->serviceRouter->serviceNames2ClassFindInfo);
        include(dirname(__FILE__) . "/Top.php");
        $message = "\n<ul id='menu'>";
        foreach ($availableServiceNames as $availableServiceName) {
            $message .= "\n <li><b>$availableServiceName</b>";

            $serviceObject = $this->serviceRouter->getServiceObject($availableServiceName);

            $reflectionObj = new ReflectionObject($serviceObject);
            $availablePublicMethods = $reflectionObj->getMethods(ReflectionMethod::IS_PUBLIC);

            if (count($availablePublicMethods) > 0) {
                $message .= "\n<ul>";
                foreach ($availablePublicMethods as $methodDescriptor) {
                    $availableMethodName = $methodDescriptor->name;
                    if(substr($availableMethodName, 0, 1) == '_'){
                        //methods starting with a '_' as they are reserved, so filter them out 
                        continue;
                    }
                    $message .= "\n <li><a href='?serviceName=$availableServiceName&amp;methodName=$availableMethodName'>$availableMethodName</a></li>";
                }
                $message .= "\n</ul>";
            }
            $message .= "</li>";
        }
        $message .= "\n</ul><div id='content'>";

        if ($this->methodName) {
            $serviceObject = $this->serviceRouter->getServiceObject($this->serviceName);
            $reflectionObj = new ReflectionObject($serviceObject);
            $method = $reflectionObj->getMethod($this->methodName);
            $parameterDescriptors = $method->getParameters();
            $message .= "<h3>$this->methodName method on $this->serviceName service</h3>";
            if (count($parameterDescriptors) > 0) {

                $message .= "\nUse JSON notation for complex values. ";
                $message .= "\n<form action='?serviceName=$this->serviceName&amp;methodName=$this->methodName' method='POST'>\n<table>";
                foreach ($parameterDescriptors as $parameterDescriptor) {
                    $availableParameterName = $parameterDescriptor->name;
                    $message .= "\n <tr><td>$availableParameterName</td><td><input name='$availableParameterName' ";
                    if ($this->parametersAssoc) {
                        $message .= "value='" . $this->parametersAssoc[$availableParameterName] . "'";
                    }
                    $message .= "></td></tr>";
                }
                $message .= "\n</table>\n<input type='submit' value='Call method &raquo;'></form>";
            } else {
                $message .= "This method has no parameters.";
                $message .= "\n<form action='?serviceName=$this->serviceName&amp;methodName=$this->methodName&amp;noParams' method='POST'>\n";
                $message .= "\n<input type='submit' value='Call method'></form>";
            }
        }

        if ($this->showResult) {
            $message .= "<h3>Result ( call took " . $this->callDurationMs . " ms )</h3>";
            $message .= '<pre>';
            $message .= print_r($data, true);
            $message .= '</pre>';
        }
        $message .= "</div>" . file_get_contents(dirname(__FILE__) . "/Bottom.html");


        return $message;
    }

    /**
* filter the headers to make sure the content type is set to text/html if the request was handled by the service browser
* @param array $headers
* @return array
*/
    public function filterHeaders($headers, $contentType) {
        if (!$contentType || $contentType == self::CONTENT_TYPE) {
            return array();
        }
    }

}



?>
