<?php
/**
 * analyzes an exception and returns an AMFMessage
 * TODO. the code here is really rough. It needs some enhancement, and some checking 
 *
 * @author Ariel Sommeria-klein
 */
class AMFExceptionHandler {
    /**
     * 
     * @param <Exception> $exception the exception object to analyze
     * @return <mixed>
     */
    public function handle($exception){
        $amfMessage = new AMFMessage();
        $errorMessageBody = new AMFBody();
        $errorMessageBody->data = $exception->__toString();
        $amfMessage->addBody($errorMessageBody);
        return $amfMessage;
    }
}
?>
