<?php
/**
 * Contains the data being manipulated in its various formats and flavours, as well as pointers to the various classes
 * that will be called during the process. Is meant to be remoting protocol agnostic, i.e. work just as well for amf as for json or xml.
 * This is initialized  by the gateway, and can be tweaked by the plugins as needed.
 *
 * @author Ariel Sommeria-klein
 */
class ServiceCallContext {

    /**
     * the deserializer. transforms a request to arguments for a service call
     * @var <Amfphp_Core_Common_IDeserializer>
     */
    public $deserializer;

    /**
     * routes a service call to a service
     * @var <Amfphp_Core_Common_IServiceRouter>
     */
    public $serviceRouter;

    /**
     * transforms a service call result into a serialized Packetd ready to go to the output stream
     * @var <Amfphp_Core_Common_IServiceRouter>
     */
    public $serializer;

    /**
     * handles an exception and generates the corresponding Packet
     * @var <Amfphp_Core_Common_IExceptionHandler>
     */
    public $exceptionHandler;

    /**
     * the input data. In the case of AMF, the raw post data.
     * @var <mixed>
     */
    public $rawInputData;

    /**
     * the output data. In the case of AMF, the amf packet in binary form
     * @var <mixed>
     */
    public $rawOutputData;


}
?>
