<?php
/* 
 * Authentication for AMFPHP.
 * On a service object, the plugin looks for a method called getMethodRoles. If the method exists, the plugin will look for a role in the session that matches the role.
 * If the roles don't match, an Exception is thrown.
 * The getMethodRoles takes a parameter $methodName, and must return an array of strings containing acceptable roles for the method.
 * 
 * For example:
 * <code>
 * public function getMethodRoles($methodName){
 *     return array("user", 'admin");
 * }
 *
 * </code>
 * 
 * To authenticate a user, the plugin looks for a "login" method. This method can either be called
 * explicitly, or by setting a header with the name "Credentials", containing {userid: userid, password: password}, as defined by the AS2
 * NetConnection.setCredentials method. It is considered good practise to have a "logout" method, though this is optional
 * The login method returns a role in a "string". It takes 2 parameters, the user id and the password.
 * The logout method should call AMFPHPAuthentication::clearSessionInfo();
 * 
 * for example:
 * class AuthenticationService {

    public function login($userid, $password){
        if(($userId == "user") && ($password == "userPassword")){
            AMFPHPAuthentication::addRole("user");
        }
        if(($userId == "admin") && ($password == "adminPassword")){
            AMFPHPAuthentication::addRole("admin");
        }
    }

    public function logout(){
        AMFPHPAuthentication::clearSessionInfo();
    }
  }
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
        $hookManager = HookManager::getInstance();
        $hookManager->addHook(ServiceRouter::HOOK_SERVICE_OBJECT_CREATED, "serviceObjectCreatedHandler");
        $hookManager->addHook(Gateway::HOOK_REQUEST_HEADER, "requestHeaderHandler");
        $this->headerUserId = null;
        $this->headerPassword = null;
    }

    /**
     * looks for a "Credentials" request header. If there is one, uses it to try to authentify the user.
     * @param <type> $serviceRouter the service router is used to call the authentication service
     * @param AMFHeader $header
     */
    public function requestHeaderHandler(AMFHeader $header){
        if($header->name == AMFConstants::CREDENTIALS_HEADER_NAME){
            $userId = $header->value[AMFConstants::CREDENTIALS_FIELD_USERID];
            $password = $header->value[AMFConstants::CREDENTIALS_FIELD_PASSWORD];
            if(session_id () == ""){
                session_start();
            }
            $_SESSION[self::SESSION_FIELD_USERID] = $userId;
            $_SESSION[self::SESSION_FIELD_PASSWORD] = $password;


        }
    }

    /**
     * looks for a match between the user roles and the accepted roles
     * @param <type> $userRoles
     * @param <type> $acceptedRoles
     * @return <type>
     */
    private function doesRoleMatch($userRoles, $acceptedRoles){
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
     * @param <type> $serviceObject
     * @param <type> $serviceName
     * @param <type> $methodName
     * @return <type>
     */
    public function serviceObjectCreatedHandler($serviceObject, $serviceName, $methodName){
        if(isset ($serviceObject[self::GET_METHOD_ROLES])){

            //the service object has a "getMethodRoles" method. role checking is necessary
            $acceptedRoles = $serviceObject[self::GET_METHOD_ROLES]($methodName);

            //try to authenticate using header info if available
            if($this->headerUserId && $this->headerPassword){
                $serviceObject[self::METHOD_LOGIN]($this->headerUserId, $this->headerPassword);
            }

            if(session_id () == ""){
                session_start();
            }
            
            $userRoles = $_SESSION[self::SESSION_FIELD_ROLES];
            if(!$userRoles){
                throw new Exception("User not authenticated");
            }
            if(!$this->doesRoleMatch($userRoles, $acceptedRoles)){
                throw new Exception("role doesn't match");
            }

        }

        return array($serviceObject, $serviceName, $methodName);
    }

    /**
     * clears the session info set by the plugin. Use to logout
     */
    public static function clearSessionInfo(){
        if(session_id () != ""){
            unset ($_SESSION[self::SESSION_FIELD_ROLES]);
        }
    }

    /**
     *
     * @param String $role
     */
    public static function addRole($role){
        if(session_id () == ""){
            session_start();
        }
        if(!isset($_SESSION[self::SESSION_FIELD_ROLES])){
            $_SESSION[self::SESSION_FIELD_ROLES] = array();
        }
        
        array_push($_SESSION[self::SESSION_FIELD_ROLES], $role);
    }
}
?>
