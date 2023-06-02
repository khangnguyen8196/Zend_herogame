<?php

/**
 * Process for Menu
 */
class ComboProduct extends Zend_Db_Table_Abstract {

    protected $_name = 'combo_product';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllComboProduct() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
        $select = $select->where("status = ?",STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    public function listAllComboProduct( $params = array() ) {
        $select = $this->getAdapter()->select();
        $select->from($this->_name);
        if( empty( $params ) == false ){
            foreach ($params as $key => $value ) {
                $select = $select->where($key ." = ?", $value);
            }
        }
        $select = $select->where("status = ?", STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchComboProductById( $id ) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
        $where[] = $db->quoteInto( "status = ?", STATUS_ACTIVE, Zend_Db::INT_TYPE ); // Thêm điều kiện này vào
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
    public function saveComboProduct( $data, $id  ) {
        $datain = array();
        if (isset($data['title']) == true) {
            $datain['title'] = $data['title'];
        }
        if (isset($data['image_cb']) == true) {
            $datain['image_cb'] = $data['image_cb'];
        }
        if (isset($data['total_discount']) == true) {
            $datain['total_discount'] = $data['total_discount'];
        }
        if (isset($data['total_price']) == true) {
            $datain['total_price'] = $data['total_price'];
        }
        if (isset($data['status']) == true) {
            $datain['status'] = $data['status'];
        }
        if (isset($data['created_at']) == true) {
            $datain['created_date'] = $data['created_at'];
        }
        if (isset($data['updated_at']) == true) {
            $datain['updated_date'] = $data['updated_at'];
        }
        if (isset($data['created_by']) == true) {
            $datain['created_by'] = $data['created_by'];
        }
        if (isset($data['updated_by']) == true) {
            $datain['updated_by'] = $data['updated_by'];
        }
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		return $this->update($datain, $where );
    	} else {
        	$datain['combo_code'] = substr( uniqid(), 7, 13);
            return $this->insert($datain);
        }
    }

    /**
     * [deleteColor description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteComboProduct( $id ) {
        $where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
        return $this->update( array('status' => STATUS_DELETE ), $where );
    }

    public function getAllComboProduct($product_id) {
        $db = $this->getAdapter();
        $select = $db->select()
            ->from(array('cp' => 'combo_product'), array('cp.*'))
            ->join(array('cbd' => 'combo_detail'), 'cp.id = cbd.combo_id', array())
            ->where('cbd.product_id IN (?)', $product_id)
            ->where('cp.status = ?', STATUS_ACTIVE)
            ->where('cbd.status = ?', STATUS_ACTIVE);
        $result = $db->fetchAll($select);
        return $result;
    }


    public function fetchAllCombo($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		  $select = $select->from($this->_name);
    	}
        $commonObj = new My_Controller_Action_Helper_Common();
        $select = $select->where("combo_product.status <> ?", STATUS_DELETE);
        //search by name
        if (empty($data["title"]) == false) {
            $data["title"] = $commonObj->quoteLike($data["title"]);
            $select = $select->where("title like ?", "%" . $data["title"] . "%");
        }
        if (empty($data['status']) == false) {
            $select->where('combo_product.status =?', $data['status']);
        }
        
        if ( empty($data['search-key']) == false ){
        	$select->where( "title like '%".$data['search-key']."%' ");
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
    
}
