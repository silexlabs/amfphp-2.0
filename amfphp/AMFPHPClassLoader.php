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
require AMFPHP_ROOTPATH . "core/common/IDeserializer.php";
require AMFPHP_ROOTPATH . "core/common/IExceptionHandler.php";
require AMFPHP_ROOTPATH . "core/common/IServiceRouter.php";
require AMFPHP_ROOTPATH . "core/common/ISerializer.php";
require AMFPHP_ROOTPATH . "core/common/ServiceRouter.php";
require AMFPHP_ROOTPATH . "core/common/ServiceCallParameters.php";

//core/amf
require AMFPHP_ROOTPATH . "core/amf/Constants.php";
require AMFPHP_ROOTPATH . "core/amf/Deserializer.php";
require AMFPHP_ROOTPATH . "core/amf/Header.php";
require AMFPHP_ROOTPATH . "core/amf/Message.php";
require AMFPHP_ROOTPATH . "core/amf/Packet.php";
require AMFPHP_ROOTPATH . "core/amf/Serializer.php";
require AMFPHP_ROOTPATH . "core/amf/Util.php";

//core/amf/types
require AMFPHP_ROOTPATH . "core/amf/types/ByteArray.php";
require AMFPHP_ROOTPATH . "core/amf/types/Undefined.php";

//core
require AMFPHP_ROOTPATH . "core/AmfphpConfig.php";
require AMFPHP_ROOTPATH . "core/AmfphpException.php";
require AMFPHP_ROOTPATH . "core/Gateway.php";
require AMFPHP_ROOTPATH . "core/HookManager.php";
require AMFPHP_ROOTPATH . "core/PluginManager.php";
require AMFPHP_ROOTPATH . "core/ServiceCallContext.php";



?>
