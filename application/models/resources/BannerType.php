<?php

/**
 * Process for BannerType
 */
class BannerType extends Zend_Db_Table_Abstract {

    protected $_name = 'banner_type';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllBannerType($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		  $select = $select->from($this->_name)
              ->columns(array('created_at' => new Zend_Db_Expr("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s')")))
              ->columns(array('updated_at' => new Zend_Db_Expr("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s')")));
    	}
        $commonObj = new My_Controller_Action_Helper_Common();
        if( empty( $data["created_at"] ) == false ) {
        	$data["created_at"] = $commonObj->quoteLike( $data["created_at"] );
        	$select = $select->where( "DATE(banner_type.created_at) =?", $data["created_at"] );
        }
        if( empty( $data["updated_at"] ) == false ) {
        	$data["updated_at"] = $commonObj->quoteLike( $data["updated_at"] );
        	$select = $select->where( "DATE(banner_type.updated_at) =?", $data["updated_at"] );
        }
        if ( empty($data['search-key']) == false ){
        	$select->where( "name like '%".$data['search-key']."%' or created_by like '%".$data['search-key']."%'
        			 or updated_by like '%".$data['search-key']."%'");
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
    	$select = $select->where("status <> ?", STATUS_DELETE );
        $result = $this->getAdapter()->fetchAll( $select );
        if( empty( $data['count_only'] ) == false && $data['count_only'] == 1 ) {
        	return $result[0]['cnt'];
        }
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    /**
     * Update user
     * @param array $data
     * @param int $userId
     * @return number
     */
    public function updateBannerType( $data, $id ) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto(" banner_type_id = ?", $id, Zend_Db::INT_TYPE);
        return $this->update($data, $where);
    }
    public function fetchBannerTypeById( $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "banner_type_id = ?", $id, Zend_Db::INT_TYPE );
    	$where[] = $db->quoteInto( "status <> ?", STATUS_DELETE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function fetchBannerTypeByName( $name ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "name = ?", $name );
    	$where[] = $db->quoteInto( "status <> ?", STATUS_DELETE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function saveBannerType( $data, $id  ) {
    	if ( empty( $id ) == false && $id > 0 )  {
    		$where[] = $this->getAdapter()->quoteInto( "banner_type_id = ?", $id, Zend_Db::INT_TYPE );
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
    public function deleteBannerType( $id ) {
       	$data = array('status' => STATUS_DELETE);
		$where[] = $this->getAdapter()->quoteInto( "banner_type_id = ?", $id, Zend_Db::INT_TYPE );
		return $this->update( $data, $where );
    }
    public function checkExistBannerTypeKey( $key , $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "`key` = ?", $key);
    	if( empty($id) == false ){
    		$where[] = $db->quoteInto( "banner_type_id <>?", $id, Zend_Db::INT_TYPE );
    	}
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
}
