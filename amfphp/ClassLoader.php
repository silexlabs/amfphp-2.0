<?php

/**
 * include this to include amfphp
 * note: this list could be generated. In the meantime maintain it manually. 
 * It would be nice to do this alphabetically, It seems however that an interface must be loaded before a class, so do as possible
 *
 * @author Ariel Sommeria-klein
 */

define( 'AMFPHP_ROOTPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

//core/common
require AMFPHP_ROOTPATH . "core/common/ClassFindInfo.php";
require AMFPHP_ROOTPATH . "core/common/IServiceRouter.php";
require AMFPHP_ROOTPATH . "core/common/DefaultServiceRouter.php";

//core/amf
require AMFPHP_ROOTPATH . "core/amf/AMFBody.php";
require AMFPHP_ROOTPATH . "core/amf/AMFHeader.php";
require AMFPHP_ROOTPATH . "core/amf/AMFMessage.php";
require AMFPHP_ROOTPATH . "core/amf/AMFSerializer.php";
require AMFPHP_ROOTPATH . "core/amf/AMFUtil.php";



?>
