<?php

/**
 * Process for Info
 */
class Info extends Zend_Db_Table_Abstract {

    protected $_name = 'info_pages';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllInfo($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    		$select = $select->where( "status <> ?", STATUS_DELETE);
    	} else {
    		  $select = $select->from($this->_name)
              ->columns(array('created_at' => new Zend_Db_Expr("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s')")))
              ->columns(array('updated_at' => new Zend_Db_Expr("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s')")));
    	}
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        $select = $select->where( "status <> ?", STATUS_DELETE);
        if (empty($data["title_pages"]) == false) {
            $data["title_pages"] = $commonObj->quoteLike($data["title_pages"]);
            $select = $select->where("title_pages like ?", "%" . $data["title_pages"] . "%");
        }
        if( empty( $data["created_at"] ) == false ) {
        	$data["created_at"] = $commonObj->quoteLike( $data["created_at"] );
        	$select = $select->where( "DATE(created_at) =?", $data["created_at"] );
        }
        if( empty( $data["updated_at"] ) == false ) {
        	$data["updated_at"] = $commonObj->quoteLike( $data["updated_at"] );
        	$select = $select->where( "DATE(updated_at) =?", $data["updated_at"] );
        }
        if( empty( $data["url_name"] ) == false ) {
        	$data["url_name"] = $commonObj->quoteLike( $data["url_name"] );
        	$select = $select->where( "url_name =?", $data["url_name"] );
        }
        if ( empty($data['search-key']) == false ){
        	$select->where( "title_pages like '%".$data['search-key']."%' or url_name like '%".$data['search-key']."%' or created_by like '%".$data['search-key']."%'
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
    public function fetchInfoById( $id ) {
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
    /**
     * 
     * @param unknown $id
     * @return unknown
     */
    public function checkExistInfoUrl( $url_name , $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "url_name = ?", $url_name);
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
    /**
     * Update/Add user
     * @param array $data
     * @return boolean
     */
    public function saveInfo( $data, $id  ) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		$where[] = $this->getAdapter()->quoteInto( "status <> ?", STATUS_DELETE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }
	public function deleteInfo( $id ){
		$data = array('status' => STATUS_DELETE);
		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
		return $this->update( $data, $where );
	}
}
