<?php

/**
 * Process for Menu
 */
class FlashSale extends Zend_Db_Table_Abstract {

    protected $_name = 'flash_sale';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function getAllFlashSale() { 
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
    	$select = $select->order('flash_sale_id DESC');
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }

    public function getFlashSale() {
        $currentTime = date('Y-m-d H:i:s'); 
        
        $select = $this->getAdapter()->select()
            ->from($this->_name)
            ->where('status = ?', STATUS_ACTIVE)
            ->where('count_time_end > ?', $currentTime) 
            ->order('count_time_start ASC') 
            ->limit(1);
            
        $result = $this->getAdapter()->fetchRow($select);
        return $result;
    }
    
      

    public function fetchAllFlashSale($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
            $select = $select->where("flash_sale.status <> ?", STATUS_DELETE );
    	} else {
    		  $select = $select->from($this->_name);
    	}
        $select = $select->where("flash_sale.status <> ?", STATUS_DELETE);
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        if ( empty($data['search-key']) == false ){
        	$select->where( "title_flash_sale like '%".$data['search-key']."%'");
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

    public function getFlashSaleById($id) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "flash_sale_id = ?", $id, Zend_Db::INT_TYPE );
    	// $where[] = $db->quoteInto( "status <> ?", STATUS_DELETE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }

    public function deleteFlashSale( $id ) {
        $where[] = $this->getAdapter()->quoteInto("flash_sale_id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }

    public function saveFlashSale( $data, $id) {
        $datain = array();
        if (isset($data['status']) == true) {
            $datain['status'] = $data['status'];
        }
        if (isset($data['title_flash_sale']) == true) {
            $datain['title_flash_sale'] = $data['title_flash_sale'];
        }
        if (isset($data['count_time_start']) == true) {
            $datain['count_time_start'] = $data['count_time_start'];
        }
        if (isset($data['count_time_end']) == true) {
            $datain['count_time_end'] = $data['count_time_end'];
        }
        if (isset($data['created_at']) == true) {
            $datain['created_at'] = $data['created_at'];
        }
        if (isset($data['updated_at']) == true) {
            $datain['updated_at'] = $data['updated_at'];
        }
        if (isset($data['created_by']) == true) {
            $datain['created_by'] = $data['created_by'];
        }
        if (isset($data['updated_by']) == true) {
            $datain['updated_by'] = $data['updated_by'];
        }
        if (empty($id) == false) {
            $where[] = $this->getAdapter()->quoteInto("flash_sale_id = ?", $id, Zend_Db::INT_TYPE);
            return $this->update($datain, $where);
        } else {
            return $this->insert($datain);
        }
    }
       
}
