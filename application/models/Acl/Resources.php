<?php
/**
 * Acl resources
 *
 */
class Model_Acl_Resources extends Model_Abstract {
    protected $_name = 'permission';
    protected $_primary = 'permission_id';

    /**
     * Delete resources was deleted
     * @param int $resources
     */
    public function deleteResourcesIsNotExist( $resources ) {
        if ( empty( $resources ) == false && is_array( $resources ) ) {
            $where = array();
            foreach ( $resources as $key => $value ){
                foreach ( $value as $key1 => $value2 ){
                    if ( $key1 != CSS &&  $key1 != CSS_HOME &&  $key1 != NAME_HOME ) {
                        foreach ( $value2 as $controllerName => $actions ) {
                            if ( empty( $actions ) == false && is_array( $actions ) ) {
                                foreach ( $actions['permissions'] as $action ) {
                                    $where[] = "'" . $controllerName . ':' . $action['action_name'] . "'";
                                }
                            }
                        }
                    }
                }
            }
            $where = implode(", ", $where);
            $this->delete("CONCAT(controller_name, ':', action_name) NOT IN ({$where})");
        }
        
    }
    
    /**
     * Get a resource by controller name
     * @param string $controllerName
     * @return multitype:|unknown
     */
    public function fetchResourceByController( $controllerName ) {
        $db     = $this->getAdapter();
        $where[]  = $db->quoteInto( "controller_name = ?", $controllerName );
        $result = $this->fetchAll( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
    /**
     * insert
     * @param array $data
     * @return number
     */
    public function saveData($data){
    	return $this->insert($data);
    }
}

