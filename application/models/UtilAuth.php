<?php
/**
 * Priviledge utilities
 *
 */
class UtilAuth {
    
    protected static $login_info = null;
    protected static $instance = null;
    protected static $all_permission = array();
    protected static $controller_permission = array();
    protected static $cus_login_info = null;
    
    /**
     * Check user type has privilege on action of a controller
     * @param string $controllerName
     * @param string $actionName
     * @return boolean
     */
    public static function hasPrivilege( $controllerName, $actionName ) {
        $result = false;
        $loginInfo = self::getLoginInfo();
        if ( empty( $loginInfo ) == false) {
                $arrayResources = self::getAllPermission();
                if ( empty( $arrayResources ) == false ) {
                    $controllerPermission = self::getPermissionByController( $controllerName );
                    if ( empty( $controllerPermission ) == false && empty( $controllerPermission[$actionName] ) == false ) {
                        $resourceId = $controllerPermission[$actionName];
                        if ( in_array( $resourceId, $arrayResources ) ) {
                            $result = true;
                        }
                    }
                }
        }
        return $result;
    }
    
    /**
     * Get login info
     */
    public static function getPermissionByController( $controllerName ) {
        if ( empty( self::$controller_permission[$controllerName] ) == true ) {
            $permission = array();
            $aclResource = new Model_Acl_Resources();
            $resourcesList = $aclResource->fetchResourceByController( $controllerName );
            if ( empty( $resourcesList ) == false && is_array( $resourcesList ) ) {
                foreach ( $resourcesList as $key => $resource ) {
                    $permission[$resource["action_name"]] = $resource["permission_id"];
                }
            }
            self::$controller_permission[$controllerName] = $permission;
        }
        return self::$controller_permission[$controllerName];
    }
    
    /**
     * Get all permission of this login info
     */
    public static function getAllPermission() {
        if ( self::$all_permission == null ) {
            $permission = array();
            $loginInfo = self::getLoginInfo();
            if ( empty( $loginInfo ) == false ) {
                $roleId = $loginInfo["role_id"];
                $role = new Role();
                $roleInfo = $role->fetchRoleByParam( array ( 'role_id' => $roleId ) );
                if ( empty( $roleInfo ) == false ) {
                    $resources = $roleInfo["permission"];
                    $permission = explode( ",", $resources );
                }
            }
            self::$all_permission = $permission;
        }
        return self::$all_permission;
    }
    
    /**
     * Get login info
     */
    public static function getLoginInfo() {
        if ( self::$login_info == null ) {
            self::$login_info = UtilSession::get( 'ACCOUNT_LOGING' );
        }
        return self::$login_info;
    }
    public static function getCustommerLoginInfo( $force = false) {
    	if ( self::$cus_login_info == null || $force == true ) {
    		self::$cus_login_info = UtilSession::get( 'CUSTOMMER_LOGING' );
    	}
    	return self::$cus_login_info;
    }
    /**
     * Set login info
     * @param array
     */
    public static function setLoginInfo( $loginInfo ) {
        UtilSession::set( 'ACCOUNT_LOGING', $loginInfo );
    }
	public static function setCustommerLoginInfo( $loginInfo ) {
        UtilSession::set( 'CUSTOMMER_LOGING', $loginInfo );
    }
    
}
