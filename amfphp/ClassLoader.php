<?php

/**
 * include this to include amfphp
 *
 * @author Ariel Sommeria-klein
 */

define( 'AMFPHP_ROOTPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
require_once AMFPHP_ROOTPATH . "core/amf/AMFBody.php";
require_once AMFPHP_ROOTPATH . "core/amf/AMFHeader.php";
require_once AMFPHP_ROOTPATH . "core/amf/AMFMessage.php";
require_once AMFPHP_ROOTPATH . "core/amf/AMFSerializer.php";
require_once AMFPHP_ROOTPATH . "core/amf/AMFUtil.php";



?>
