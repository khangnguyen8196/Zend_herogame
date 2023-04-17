<?php

/**
 * Process for Media
 */
class Media extends Zend_Db_Table_Abstract {

    protected $_name = 'media';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllMedia($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		  $select = $select->from($this->_name)
              ->columns(array('created_at' => new Zend_Db_Expr("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s')")))
              ->columns(array('updated_at' => new Zend_Db_Expr("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s')")));
    	}
        $commonObj = new My_Controller_Action_Helper_Common();
        //check count only purpose
        if( empty( $data['count_only'] ) == true || $data['count_only'] != 1 ) {
        	if ( empty( $data["order"] ) == false ) {
        		$order = $data["order"]["column"] . " " . $data["order"]["dir"];
        		$select = $select->order( $order );
        	} else {
		    	$select = $select->order( 'media_id DESC');
        	}
        	$start = ( empty( $data['start'] ) == false ) ? $data['start'] : 0;
        	$length = ( empty( $data['length'] ) == false ) ? $data['length'] : 0;
        	$select = $select->limit( $length, $start );
        }
    	$select = $select->where("status != ?", STATUS_DELETE );
        $result = $this->getAdapter()->fetchAll( $select );
        if( empty( $data['count_only'] ) == false && $data['count_only'] == 1 ) {
        	return $result[0]['cnt'];
        }
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    public function fetchMoreMedia($data = array()) {
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name)
				    		->columns(array('created_at' => new Zend_Db_Expr("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s')")))
				    		->columns(array('updated_at' => new Zend_Db_Expr("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s')")));
    		
    	$start = ( empty( $data['start'] ) == false ) ? $data['start'] : 0;
    	if( empty( $data['limit'] ) == false ){
    		$select = $select->limit( $data['limit'],$start);
    	}	
    		
    	$select = $select->order( 'media_id DESC');
    	$select = $select->where("status <> ?", STATUS_DELETE );
    	$result = $this->getAdapter()->fetchAll($select);
    	return $result;
    }
    /**
     * get media with limit
     */
    
    /**
     * Update user
     * @param array $data
     * @param int $userId
     * @return number
     */
    public function updateMedia( $data, $id ) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto(" media_id = ?", $id, Zend_Db::INT_TYPE);
        return $this->update($data, $where);
    }
    public function fetchMediaById( $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "media_id = ?", $id, Zend_Db::INT_TYPE );
    	$where[] = $db->quoteInto( "status <> ?", STATUS_DELETE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function saveMedia( $data, $id = 0 ) {
    	if ( empty( $id ) == false && $id > 0 )  {
    		$where[] = $this->getAdapter()->quoteInto( "media_id = ?", $id, Zend_Db::INT_TYPE );
    		$where[] = $this->getAdapter()->quoteInto( "status <> ?", STATUS_DELETE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }
    /**
     * Delete User
     * @param int $apiUserId
     * @return number
     */
    public function deleteMedia( $id ) {
        $where = $this->getAdapter()->quoteInto(' media_id = ?', $id );
        return $this->delete($where);
    }
    public function getListImage( $data) {
    	$select = $this->getAdapter()->select();
    	$select->from($this->_name);
    	$select = $select->where( "status = ?", STATUS_ACTIVE );
    	$select = $select->where( "media_id IN (?) ", $data );
    	$select = $select->order( 'created_at DESC');
    	$result = $this->getAdapter()->fetchAll( $select );
    	return $result;
    }
}
