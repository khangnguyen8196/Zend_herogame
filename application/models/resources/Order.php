<?php

/**
 * Process for Menu
 */
class Order extends Zend_Db_Table_Abstract {

    protected $_name = 'order';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllOrder($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		  $select = $select->from($this->_name);
    	}
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        if (empty($data['search-key']) == false) {
        	$select->where("name like '%" . $data['search-key'] . "%' or order_code like '%" . $data['search-key'] . "%' or email like '%" . $data['search-key'] . "%'
        			 or phone like '%" . $data['search-key'] . "%' or total like '%" . $data['search-key'] . "%'");
        }
        if (empty($data["created_date"]) == false) {
        	$data["created_date"] = $commonObj->quoteLike($data["created_date"]);
        	$select = $select->where("DATE(created_date) =?", $data["created_date"]);
        }
        if (empty($data["updated_date"]) == false) {
        	$data["updated_date"] = $commonObj->quoteLike($data["updated_date"]);
        	$select = $select->where("DATE(updated_date) =?", $data["updated_date"]);
        }
        //for statistic
        if (empty($data["from_date"]) == false) {
        	$data["from_date"] = $commonObj->quoteLike($data["from_date"]);
        	$select = $select->where("DATE(created_date) >=?", $data["from_date"]);
        }
        if (empty($data["to_date"]) == false) {
        	$data["to_date"] = $commonObj->quoteLike($data["to_date"]);
        	$select = $select->where("DATE(created_date) <=?", $data["to_date"]);
        }
        //----------------
        if (empty($data["name"]) == false) {
            $data["name"] = $commonObj->quoteLike($data["name"]);
            $select = $select->where("name like ?", "%" . $data["name"] . "%");
        }
        if (empty($data["status"]) == false) {
        	$select = $select->where("status = ?", $data["status"]);
        }
        if (isset($data["is_pay"]) == true) {
        	$select = $select->where("is_pay = ?", $data["is_pay"]);
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
    public function fetchOrderById( $id ) {
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
     * Update/Add
     * @param array $data
     * @return boolean
     */
    public function saveOrder( $data, $id = 0  ) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }
    
    public function deleteOrderPromo( $id ) {
    	$where = $this->getAdapter()->quoteInto(' id = ?', $id );
    	return $this->delete($where);
    }
    
    
    //----------------------------------FRONT END-------------------------------------
    public function getOrders($data = array()) {
        $select = $this->getAdapter()->select();
        $select = $select->from(array("order" => $this->_name))
                ->join(array("order_detail" => "order_detail"), "order.id = order_detail.id_order", array("order_detail.price", "order_detail.number"))
                ->join(array("user" => "user"), "user.user_id = order.user_id and user.user_id = " . STATUS_ACTIVE, array("user.first_name", "user.last_name"))
                ->join(array("product" => "product"), "order_detail.id_product = product.id");
        //search by name
        if (empty($data["status"]) == false) {
            $select = $select->where("order.status IN (?)", $data["status"]);
        }
        $select = $select->order("order.created_date DESC");
        $result = $this->getAdapter()->fetchAll($select);

        if (empty($result) == true) {
            return array();
        }
        return $result;
    }

    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function getOrderDetail($orderId) {
        $select = $this->getAdapter()->select();
        $select = $select->from(array("order" => $this->_name))
                ->join(array("order_detail" => "order_detail"), "order.id = order_detail.id_order", array("order_detail.price", "order_detail.number"))
                ->join(array("user" => "user"), "user.user_id = order.user_id and user.user_id = " . STATUS_ACTIVE, array("user.first_name", "user.last_name"))
                ->join(array("product" => "product"), "order_detail.id_product = product.id");
        $select = $select->where("order.id =?", $orderId);
        $result = $this->getAdapter()->fetchRow($select);
        
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }
    /**
     * 
     * @param type $data
     * @return type
     */
    public function addOrder($data){
        return $this->insert( $data );
    }
 	public function deleteOrder($id) {
        $where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }
    public function deleteOrderCancel() {
        $where[] = $this->getAdapter()->quoteInto("status = ?", 5, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }
    public function fetchOrderToApproveAll() {
    	$select = $this->getAdapter()->select();
        $select = $select->from(array("order" => $this->_name));
        //search by name
        $select = $select->where( "DATEDIFF(CURDATE(), `created_date`) > ". MAX_DAY_APPROVE );
        $select = $select->where( "status = 2 or status = 3");
        $result = $this->getAdapter()->fetchAll($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }
    public function getOrderByCodeAndUser( $code, $userId ){
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);
        //search by name
        $select = $select->where( "order_code = ?",$code);
        $select = $select->where( "user_id = ?",$userId);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }
  
    public function fetchOrderByUser($userId) {
        $select = $this->getAdapter()->select();
        $select = $select->from(array("order" => $this->_name));
        $select = $select->where("user_id = ?", $userId);
        $select = $select->order("created_date DESC"); // Sắp xếp theo created_date giảm dần
        $result = $this->getAdapter()->fetchAll($select);
    
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }
    
}
