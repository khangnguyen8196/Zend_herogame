<?php

/**
 * Process for Menu
 */
class PromotionCode extends Zend_Db_Table_Abstract {

    protected $_name = 'promotion_code';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllPromoCode($data = array()) {
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
    public function fetchpromotionByCode( $code ){
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "code = ?", $code );
        $where[] = $db->quoteInto( "status = ?", STATUS_ACTIVE );
    	$where[] = $db->quoteInto( "enddate  >= ?",date("Y-m-d H:i:s"));
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchPromoById( $id ) {
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
     * fetch promo by code
     * @param unknown $id
     */
    public function fetchPromoByCode( $code ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "code = ?", $code );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    /**
     * Update/Add
     * @param array $data
     * @return boolean
     */
    public function savePromo( $data, $id  ) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }
    
    public function deletePromo( $id ) {
    	$where = $this->getAdapter()->quoteInto(' id = ?', $id );
    	return $this->delete($where);
    }
}
