<?php
/* 
 * Authentication for AMFPHP.
 * On a service object, the plugin looks for a method called getMethodRoles. If the method exists, the plugin will look for a role in the session that matches the role.
 * If the roles don't match, an Exception is thrown.
 * The getMethodRoles takes a parameter $methodName, and must return an array of strings containing acceptable roles for the method. If the return value is null,
 * it is considered that that particular method is not protected.
 * 
 * For example:
 * <code>
 * public function getMethodRoles($methodName){
       if($methodName == "adminMethod"){
           return array("admin");
       }else{
           return null;
       }
    }
 *
 * </code>
 * 
 * To authenticate a user, the plugin looks for a "login" method. This method can either be called
 * explicitly, or by setting a header with the name "Credentials", containing {userid: userid, password: password}, as defined by the AS2
 * NetConnection.setCredentials method. It is considered good practise to have a "logout" method, though this is optional
 * The login method returns a role in a "string". It takes 2 parameters, the user id and the password.
 * The logout method should call AMFPHPAuthentication::clearSessionInfo();
 * 
 * See the AuthenticationService class in the test data for an example of an implementation.
 *
 * @author Ariel Sommeria-klein
 */

class AMFPHPAuthentication {
    /**
     * the field in the session where the roles array is stored
     */
    const SESSION_FIELD_ROLES = "amfphp_roles";

    /**
     * the name of the method on the service where the method roles are given
     */
    const METHOD_GET_METHOD_ROLES = "getMethodRoles";

    /**
     * the name of the login method
     */
    const METHOD_LOGIN = "login";
    
    //the following session fields are unused at the moment
    
    /**
     * the field in the session where the user id is stored
     */
    //const SESSION_FIELD_USERID = "amfphp_userid";

    /**
     * the field in the session where the password is stored
     */
    //const SESSION_FIELD_PASSWORD = "amfphp_password";

    /**
     * the user id passed in the credentials header
     * @var <String>
     */
    private $headerUserId;

    /**
     * the password passed in the credentials header
     * @var <String>
     */
    private $headerPassword;
    
    public function  __construct() {
        $hookManager = Amfphp_Core_HookManager::getInstance();
        $hookManager->addHook(Amfphp_Core_Common_ServiceRouter::HOOK_SERVICE_OBJECT_CREATED, array($this, "serviceObjectCreatedHandler"));
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_REQUEST_HEADER, array($this, "requestHeaderHandler"));
        $this->headerUserId = null;
        $this->headerPassword = null;
    }

    /**
     * looks for a match between the user roles and the accepted roles
     * @param <type> $userRoles
     * @param <type> $acceptedRoles
     * @return <type>
     */
    private function doRolesMatch($userRoles, $acceptedRoles){
            foreach($userRoles as $userRole){
                foreach($acceptedRoles as $acceptedRole){
                    if($userRole == $acceptedRole){
                        //a match is found
                        return true;

                    }
                }
            }
            return false;
    }
    
    /**
     * called when the service object is created, just before the method call.
     * Tries to authenticate if a credentials header was sent in the packet.
     * Throws an exception if the roles don't match 
     * 
     * @param <Object> $serviceObject
     * @param <String> $methodName
     * @return <array>
     */
    public function serviceObjectCreatedHandler($serviceObject, $methodName){
        if(!method_exists($serviceObject, self::METHOD_GET_METHOD_ROLES)){
            return;
        }

        //the service object has a "getMethodRoles" method. role checking is necessary if the returned value is not null
        $acceptedRoles = call_user_func(array($serviceObject, self::METHOD_GET_METHOD_ROLES), $methodName);
        if(!$acceptedRoles){
            return;
        }

        //try to authenticate using header info if available
        if($this->headerUserId && $this->headerPassword){
            call_user_func(array($serviceObject, self::METHOD_LOGIN), $this->headerUserId, $this->headerPassword);
        }

        if(session_id () == ""){
            session_start();

        }

        if(!isset ($_SESSION[self::SESSION_FIELD_ROLES])){
            throw new Amfphp_Core_Exception("User not authenticated");
        }
        
        $userRoles = $_SESSION[self::SESSION_FIELD_ROLES];
        if(!$this->doRolesMatch($userRoles, $acceptedRoles)){
            throw new Amfphp_Core_Exception("roles don't match");
        }
    }

    /**
     * clears the session info set by the plugin. Use to logout
     */
    public static function clearSessionInfo(){
        if(session_id () == ""){
            session_start();

        }
        if(isset ($_SESSION[self::SESSION_FIELD_ROLES])){
            unset ($_SESSION[self::SESSION_FIELD_ROLES]);
        }
    }

    /**
     *
     * @param String $role
     */
    public static function addRole($roleToAdd){
        if(session_id () == ""){
            session_start();
        }
        if(!isset($_SESSION[self::SESSION_FIELD_ROLES])){
            $_SESSION[self::SESSION_FIELD_ROLES] = array();
        }

        //check role isn't already available
        foreach($_SESSION[self::SESSION_FIELD_ROLES] as $userRole){
            if($userRole == $roleToAdd){
                return;
            }
        }
        //role isn't already available. Add it.
        array_push($_SESSION[self::SESSION_FIELD_ROLES], $roleToAdd);
    }



    /**
     * looks for a "Credentials" request header. If there is one, uses it to try to authentify the user.
     * @param Amfphp_Core_Amf_Header $header
     */
    public function requestHeaderHandler(Amfphp_Core_Amf_Header $header){
        if($header->name == Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME){
            $userId = $header->value[Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_USERID];
            $password = $header->value[Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_PASSWORD];
            if(session_id () == ""){
                session_start();
            }
            $this->headerUserId = $userId;
            $this->headerPassword = $password;

        }
    }
}
?>
