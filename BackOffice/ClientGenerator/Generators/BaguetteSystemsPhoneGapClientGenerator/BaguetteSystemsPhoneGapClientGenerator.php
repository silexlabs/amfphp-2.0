<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package BaguetteSystems_Generators

  /**
 * 
 *
 * @author Ariel Sommeria-klein
 * @package BaguetteSystems_Generators
 */
class BaguetteSystemsPhoneGapClientGenerator extends Amfphp_BackOffice_ClientGenerator_RemoteClientGenerator {
    
    public function getIframeUrl() {
        return 'http://127.0.0.1:8888/workspaceNetbeans/AppSlapper/from_amfphp.php?type=PhoneGap';
    }
    public function getUiCallText(){
        return 'Phone Gap';
    }    
    
    public function getInfoUrl(){
        return "http://www.baguettesystems.com/documentation/client-generators/phonegap";
    }    
    
    public function getProxyUrl(){
        return "http:///127.0.0.1:8888/workspaceNetbeans/AppSlapper/Lib/porthole/proxy.html";
    }
        
}

?>
