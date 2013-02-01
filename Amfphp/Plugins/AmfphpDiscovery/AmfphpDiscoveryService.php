<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Discovery
 */

/**
 * analyses existing services. Warning: if 2 or more services have the same name, t-only one will appear in the returned data, 
 * as it is an associative array using the service name as key. 
 * @package Amfphp_Plugins_Discovery
 * @author Ariel Sommeria-Klein
 */
class AmfphpDiscoveryService {

    /**
     * @see AmfphpDiscovery
     * @var array of strings(patterns)
     */
    public static $excludePaths;

    /**
     * paths to folders containing services(relative or absolute). set by plugin.
     * @var array of paths
     */
    public static $serviceFolderPaths;

    /**
     *
     * @var array of ClassFindInfo. set by plugin.
     */
    public static $serviceNames2ClassFindInfo;

    /**
     * restrict access to amfphp_admin. 
     * @var boolean
     */
    public static $restrictAccess;

    /**
     * get method roles
     * @param string $methodName
     * @return array
     */
    public function _getMethodRoles($methodName) {
        if (self::$restrictAccess) {
            return array('amfphp_admin');
        }
    }

    /**
     * finds classes in folder. If in subfolders add the relative path to the name.
     * recursive, so use with care.
     * @param string $rootPath
     * @param string $subFolder
     * @return array
     */
    protected function searchFolderForServices($rootPath, $subFolder) {
        $ret = array();
        $folderContent = scandir($rootPath . $subFolder);

        if ($folderContent) {
            foreach ($folderContent as $fileName) {
                //add all .php file names, but removing the .php suffix
                if (strpos($fileName, ".php")) {
                    $fullServiceName = $subFolder . substr($fileName, 0, strlen($fileName) - 4);
                    $ret[] = $fullServiceName;
                } else if ((substr($fileName, 0, 1) != '.') && is_dir($rootPath . $subFolder . $fileName)) {
                    $ret = array_merge($ret, $this->searchFolderForServices($rootPath, $subFolder . $fileName . '/'));
                }
            }
        }
        return $ret;
    }

    /**
     * returns a list of available services
     * @param array $serviceFolderPaths
     * @param array $serviceNames2ClassFindInfo
     * @return array of service names
     */
    protected function getServiceNames(array $serviceFolderPaths, array $serviceNames2ClassFindInfo) {
        $ret = array();
        foreach ($serviceFolderPaths as $serviceFolderPath) {
            $ret = array_merge($ret, $this->searchFolderForServices($serviceFolderPath, ''));
        }

        foreach ($serviceNames2ClassFindInfo as $key => $value) {
            $ret[] = $key;
        }

        return $ret;
    }

    /**
     * extracts 
     * - types from param tags. type is first word after tag name, name of the variable is second word ($ is removed)
     * - return tag
     * 
     * @param string $comment 
     * @return array{'returns' => type, 'params' => array{var name => type}}
     */
    protected function parseMethodComment($comment) {
        //get rid of phpdoc formatting
        $comment = str_replace('/**', '', $comment);
        $comment = str_replace('*', '', $comment);
        $comment = str_replace('*/', '', $comment);
        $exploded = explode('@', $comment);
        $ret = array();
        $params = array();
        foreach ($exploded as $value) {
            if (strtolower(substr($value, 0, 5)) == 'param') {
                $words = explode(' ', $value);
                $type = trim($words[1]);
                $varName = trim(str_replace('$', '', $words[2]));
                $params[$varName] = $type;
            } else if (strtolower(substr($value, 0, 6)) == 'return') {

                $words = explode(' ', $value);
                $type = trim($words[1]);
                $ret['return'] = $type;
            }
        }
        $ret['param'] = $params;
        if (!isset($ret['return'])) {
            $ret['return'] = '';
        }
        return $ret;
    }

    /**
     * does the actual collection of data about available services
     * @return array of AmfphpDiscovery_ServiceInfo
     */
    public function discover() {
        $serviceNames = $this->getServiceNames(self::$serviceFolderPaths, self::$serviceNames2ClassFindInfo);
        $ret = array();
        foreach ($serviceNames as $serviceName) {
            $serviceObject = Amfphp_Core_Common_ServiceRouter::getServiceObjectStatically($serviceName, self::$serviceFolderPaths, self::$serviceNames2ClassFindInfo);
            $objR = new ReflectionObject($serviceObject);
            $objComment = $objR->getDocComment();
            $methodRs = $objR->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods = array();
            foreach ($methodRs as $methodR) {
                $methodName = $methodR->name;

                if (substr($methodName, 0, 1) == '_') {
                    //methods starting with a '_' as they are reserved, so filter them out 
                    continue;
                }

                $parameters = array();
                $paramRs = $methodR->getParameters();

                $methodComment = $methodR->getDocComment();
                $parsedMethodComment = $this->parseMethodComment($methodComment);
                foreach ($paramRs as $paramR) {

                    $parameterName = $paramR->name;
                    $type = '';
                    if ($paramR->getClass()) {
                        $type = $paramR->getClass()->name;
                    } else if (isset($parsedMethodComment['param'][$parameterName])) {
                        $type = $parsedMethodComment['param'][$parameterName];
                    }
                    $parameterInfo = new AmfphpDiscovery_ParameterDescriptor($parameterName, $type);

                    $parameters[] = $parameterInfo;
                }
                $methods[$methodName] = new AmfphpDiscovery_MethodDescriptor($methodName, $parameters, $methodComment, $parsedMethodComment['return']);
            }

            $ret[$serviceName] = new AmfphpDiscovery_ServiceDescriptor($serviceName, $methods, $objComment);
        }
        //note : filtering must be done at the end, as for example excluding a Vo class needed by another creates issues
        foreach ($ret as $serviceName => $serviceObj) {
            foreach (self::$excludePaths as $excludePath) {
                if (strpos($serviceName, $excludePath) !== false) {
                    unset($ret[$serviceName]);
                    break;
                }
            }
        }
        return $ret;
    }

}

?>
