<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice_Generators

  /**
 * Handles typical client generation, override various methods for customisation
 * 1. copies the template.
 * 2. looks for template directives in the code. Usually these directives indicate a block of code that must be replicated.
 * Each directive starts with '/**ACG' and must end with * / 
 * note that services in subfolders should get a special treatment, and ideally code would be generated in them 
 * with additionnal sub-packages. This is technically too messy, so the '/' is simply replaced
 * by an '_'.  This will be replaced by a '/' in Amfphp. This unfortunately means that '_' is now no longer
 * an acceptable character in a service name.
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice_Generators
 */
class Amfphp_BackOffice_ClientGenerator_ClientGeneratorBase {

    protected $codeFileExtensions;
    protected $templateFolderUrl;
    protected $services;
    protected $serviceBeingProcessed;
    protected $methodBeingProcessed;
    protected $amfphpEntryPointUrl;

    //terms to replace
    const _SERVICE_ = '_SERVICE_';
    const _METHOD_ = '_METHOD_';
    const _PARAMETER_ = '_PARAMETER_';
    //directive types
    const SERVICE = 'SERVICE';
    const METHOD = 'METHOD';
    const PARAMETER = 'PARAMETER';
    const PARAMETER_COMMA = 'PARAMETER_COMMA';

    /**
     *
     * @param array $codeFileExtensions
     * @param type $templateFolderUrl
     * @param type $services . note: here '/' in each service name is replaced by '_', to avoid dealing with packages
     * @param type $amfphpEntryPointUrl 
     */
    public function __construct(array $codeFileExtensions, $templateFolderUrl, $services, $amfphpEntryPointUrl) {
        $this->codeFileExtensions = $codeFileExtensions;
        $this->templateFolderUrl = $templateFolderUrl;
        $this->services = $services;
        $this->amfphpEntryPointUrl = $amfphpEntryPointUrl;
        
        foreach ($this->services as $service) {
            $service->name = str_replace('/', '_', $service->name);
        }
        
    }

    public function generate() {
        $dstFolder = Amfphp_BackOffice_ClientGenerator_Util::getGeneratedProjectDestinationFolder('AmfphpFlash');
        Amfphp_BackOffice_ClientGenerator_Util::recurseCopy($this->templateFolderUrl, $dstFolder);
        $it = new RecursiveDirectoryIterator($dstFolder);
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if (In_Array(SubStr($file, StrrPos($file, '.') + 1), $this->codeFileExtensions) == true) {
                $this->processSourceFile($file);
            }
        }
    }

    /**
     * looks for blocks delimited by the start and stop markers matching the directive, and applies a processing function to each
     * found block.
     * @param String $code the template code. Is modified continually
     * @param String $directive for example 'SERVICE'
     * @param String functionName
     * @return mixed. if there was a change, returns the modified code, else returns false
     */
    protected function searchForBlocksAndApplyProcessing($code, $directive, $functionName) {
        $marker = '/*ACG_' . $directive . '*/';
        $markerLength = strlen($marker);
        $codeLength = strlen($code);
        $callBack = array($this, $functionName);


        $startPos = 0;
        $stopPos = 0;
        $seekStartPos = 0;
        $hasChanged = false;

        while (1) {
            $startPos = strpos($code, $marker, $seekStartPos);
            if ($startPos === false) {
                break;
            }
            //echo $startPos . '<br/><br/>';
            //startPos: before start Marker, stopPos: after stop Marker

            $stopPos = strpos($code, $marker, $startPos + 1) + $markerLength;
            //blockText: text within the Markers, excluding the Markers
            $blockText = substr($code, $startPos + $markerLength, $stopPos - $startPos - 2 * $markerLength);
            //$processedText = $this->processServiceListBlock($blockText);
            $processedText = call_user_func($callBack, $blockText);
            //up to, but exculding Marker
            $beforeBlock = substr($code, 0, $startPos);
            //after Marker
            $afterBlock = substr($code, $stopPos);
            $code = $beforeBlock . $processedText . $afterBlock;
            $hasChanged = true;
            $seekStartPos = strlen($beforeBlock . $processedText);
        }
        if ($hasChanged) {
            return $code;
        } else {
            return false;
        }
    }
    
    /**
     * load the code, and look if either file is a service block, or il it contains service blocks.
     * If the file is a service block(detected by having '_SERVICE_' in the file name), call generateServiceFiles
     * If not, look for block delimited by the 'SERVICE' directive and call processServiceListBlock on them
     * Also sets the amfphp entry point url
     * @param SplFileInfo $file 
     */
    protected function processSourceFile(SplFileInfo $file) {
        $code = file_get_contents($file);
        $amfphpUrlMarkerPos = strpos($code, '/**ACG_AMFPHPURL_**/');
        if ($amfphpUrlMarkerPos !== false) {
            $code = str_replace('/**ACG_AMFPHPURL_**/', $this->amfphpEntryPointUrl, $code);
            file_put_contents($file, $code);
        }
        $fileName = $file->getFilename();
        if (strpos($fileName, self::_SERVICE_) !== false) {
            $this->generateServiceFiles($code, $file);
        } else {
            $processed = $this->searchForBlocksAndApplyProcessing($code, self::SERVICE, 'processServiceListBlock');
            if ($processed) {
                file_put_contents($file, $processed);
            }
        }
    }

    /**
     * generate as many copies as there are services and 
     * treat it as a service block.
     * @param String $code 
     * @param SplFileInfo $file
     */
    protected function generateServiceFiles($code, SplFileInfo $file) {
        foreach ($this->services as $service) {
            $fileNameMatchingService = str_replace(self::_SERVICE_, $service->name, $file->getFilename());
            $this->serviceBeingProcessed = $service;
            $newFilePath = $file->getPath() . '/' . $fileNameMatchingService;
            $codeMatchingService = $this->generateOneServiceFileCode($code);
            file_put_contents($newFilePath, $codeMatchingService);
        }
        unlink($file);
    }
    
    /**
     * generates code for one Service File. 
     * @param String $code
     * @return String 
     */
    protected function generateOneServiceFileCode($code){
            $codeMatchingService = str_replace(self::_SERVICE_, $this->serviceBeingProcessed->name, $code);
            $processed = $this->searchForBlocksAndApplyProcessing($codeMatchingService, self::METHOD, 'processMethodListBlock');
            if ($processed) {
                $codeMatchingService = $processed;
            }
            return $codeMatchingService;
        
    }

    /**
     * finds method blocks.
     * applies processMethodListBlock to each of them
     * then multiplies and adapts the resulting code for each service
     * @param type $code
     */
    protected function processServiceListBlock($code) {
        $ret = '';
        $this->searchForBlocksAndApplyProcessing($code, self::METHOD, 'processMethodListBlock');
        foreach ($this->services as $service) {
            $blockForService = str_replace(self::_SERVICE_, $service->name, $code);
            $ret .= $blockForService;
        }
        return $ret;
    }

    /**
     * finds parameter blocks.
     * applies processParameterListBlock to each of them
     * then multiplies and adapts the resulting code for each method
     * @param type $code
     */
    protected function processMethodListBlock($code) {
        $ret = '';
        foreach ($this->serviceBeingProcessed->methods as $method) {
            $this->methodBeingProcessed = $method;
            $blockForMethod = str_replace(self::_METHOD_, $method->name, $code);
            $processed = $this->searchForBlocksAndApplyProcessing($blockForMethod, self::PARAMETER, 'processParameterListBlock');
            if ($processed) {
                $blockForMethod = $processed;
            }
            $processed = $this->searchForBlocksAndApplyProcessing($blockForMethod, self::PARAMETER_COMMA, 'processParameterCommaListBlock');
            if ($processed) {
                $blockForMethod = $processed;
            }

            $ret .= $blockForMethod;
        }
        return $ret;
    }

    /**
     * multiplies and adapts the code for each parameter
     * @param type $code
     */
    protected function processParameterListBlock($code) {
        $ret = '';
        foreach ($this->methodBeingProcessed->parameters as $parameter) {
            $blockForParameter = str_replace(self::_PARAMETER_, $parameter->name, $code);
            $ret .= $blockForParameter;
        }
        return $ret;
    }

    /**
     * multiplies and adapts the code for each parameter, but adds a comma between each
     * @param type $code
     */
    protected function processParameterCommaListBlock($code) {
        $ret = '';
        foreach ($this->methodBeingProcessed->parameters as $parameter) {
            $blockForParameter = str_replace(self::_PARAMETER_, $parameter->name, $code);
            $ret .= $blockForParameter . ', ';
        }
        //remove last comma
        $ret = substr($ret, 0, -2);
        return $ret;
    }

}

?>
