<?php

/**
 * Process for Banner
 */
class Banner extends Zend_Db_Table_Abstract {

    protected $_name = 'banner';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllBanner($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    		$select = $select->where("banner.status <> ?", STATUS_DELETE );
    	} else {
    		  $select = $select->from($this->_name);
    	}
    	$select = $select->where("status <> ?", STATUS_DELETE );
        $commonObj = new My_Controller_Action_Helper_Common();
        if ( empty($data['search-key']) == false ){
        	$select->where( "title like '%".$data['search-key']."%'");
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
     * Update user
     * @param array $data
     * @param int $userId
     * @return number
     */
    public function updateBanner( $data, $id ) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto(" id = ?", $id, Zend_Db::INT_TYPE);
        return $this->update($data, $where);
    }
    public function fetchBannerById( $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    	$where[] = $db->quoteInto( "status <> ?", STATUS_DELETE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function saveBanner( $data, $id  ) {
    	if ( empty( $id ) == false && $id > 0 )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
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
	public function deleteBanner( $id ){
		$where = $this->getAdapter()->quoteInto(' id = ?', $id );
		return $this->delete($where);
	}
	
	public function checkExistBannerUrl( $url_name , $id ) {
		$db     = $this->getAdapter();
		$where[] = $db->quoteInto( "url = ?", $url_name);
		if( empty($id) == false ){
			$where[] = $db->quoteInto( "id <>?", $id, Zend_Db::INT_TYPE );
		}
		$result = $this->fetchRow( $where );
		if ( empty( $result ) == true ) {
			return array();
		}
		$result = $result->toArray();
		return $result;
	}
	/* load banner */
	public function loadListBannerBytype( $type ){
		$select = $this->getAdapter()->select();
		$select = $select->from($this->_name);
		$select = $select->where("status =?", STATUS_ACTIVE );
		if( empty($type) == false ){
			$select = $select->where("type = ?", $type );
		}
		$select = $select->order( 'priority ASC' );
		$result = $this->getAdapter()->fetchAll( $select );
		return $result;
	}
}
