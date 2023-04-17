<?php

/**
 * Process for Menu
 */
class Menu extends Zend_Db_Table_Abstract {

    protected $_name = 'menu';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllMenu($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		  $select = $select->from($this->_name);
    	}
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        if (empty($data["name"]) == false) {
            $data["name"] = $commonObj->quoteLike($data["name"]);
            $select = $select->where("name like ?", "%" . $data["name"] . "%");
        }
        
        if ( empty($data['search-key']) == false ){
        	$select->where( "name like '%".$data['search-key']."%' or url like '%".$data['search-key']."%'");
        }
        //check count only purpose
        if( empty( $data['count_only'] ) == true || $data['count_only'] != 1 ) {
        	if ( empty( $data["order"] ) == false ) {
        		$order = $data["order"]["column"] . " " . $data["order"]["dir"];
        		$select = $select->order( $order );
        	}
        	$start = ( empty( $data['start'] ) == false ) ? $data['start'] : 0;
        	$length = ( empty( $data['length'] ) == false ) ? $data['length'] : 0;
        	$select = $select->limit( $length, $start );
        }
        $result = $this->getAdapter()->fetchAll( $select );
        if( empty( $data['count_only'] ) == false && $data['count_only'] == 1 ) {
        	return $result[0]['cnt'];
        }
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchMenuById( $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    /**
     * Update/Add user
     * @param array $data
     * @return boolean
     */
    public function saveMenu( $data, $id  ) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }
    public function fetchMenu( ) {
    	$select = $this->getAdapter()->select();
    	$select = $select->from( $this->_name );
    	$result = $this->getAdapter()->fetchAll( $select );
    	return $result;
    }
    
    public function fetchMenuByUrl( $url, $id ){
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "url = ?", $url);
    	if( empty($id) == false && $id > 0 ){
    		$where[] = $db->quoteInto( "id <> ?", $id, Zend_Db::INT_TYPE );
    	}
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    
    public function deleteMenu( $id ) {
    	$where = $this->getAdapter()->quoteInto(' id = ?', $id );
    	return $this->delete($where);
    }
    // front end
    public function getListMenu(){
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
    	$result = $this->getAdapter()->fetchAll( $select );
    	return $result;
    }
}
