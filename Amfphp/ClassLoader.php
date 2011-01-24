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
require_once AMFPHP_ROOTPATH . "Core/Common/ClassFindInfo.php";
require_once AMFPHP_ROOTPATH . "Core/Common/IDeserializer.php";
require_once AMFPHP_ROOTPATH . "Core/Common/IExceptionHandler.php";
require_once AMFPHP_ROOTPATH . "Core/Common/IServiceRouter.php";
require_once AMFPHP_ROOTPATH . "Core/Common/ISerializer.php";
require_once AMFPHP_ROOTPATH . "Core/Common/ServiceRouter.php";
require_once AMFPHP_ROOTPATH . "Core/Common/ServiceCallParameters.php";

//core/amf
require_once AMFPHP_ROOTPATH . "Core/Amf/Constants.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/Deserializer.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/Header.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/Message.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/Packet.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/Serializer.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/Util.php";

//core/Amf/types
require_once AMFPHP_ROOTPATH . "Core/Amf/types/ByteArray.php";
require_once AMFPHP_ROOTPATH . "Core/Amf/types/Undefined.php";

//core
require_once AMFPHP_ROOTPATH . "Core/Config.php";
require_once AMFPHP_ROOTPATH . "Core/Exception.php";
require_once AMFPHP_ROOTPATH . "Core/Gateway.php";
require_once AMFPHP_ROOTPATH . "Core/HookManager.php";
require_once AMFPHP_ROOTPATH . "Core/PluginManager.php";
require_once AMFPHP_ROOTPATH . "Core/ServiceCallContext.php";



?>
