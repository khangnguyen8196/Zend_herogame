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
    
        if (isset($data['count_only']) && $data['count_only'] == 1) {
            $select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
        } else {
            $select = $select->from($this->_name, array('*'))
                             ->joinLeft('province', 'province.matp = ' . $this->_name . '.ma_province', array('name_province'))
                             ->joinLeft(
                                 array('od' => 'order_detail'),
                                 'od.id_order = ' . $this->_name . '.id',
                                 array()
                             )
                             ->joinLeft(
                                 array('p' => 'product'),
                                 'p.id = CASE WHEN od.id_product = 0 THEN od.product_id_cb ELSE od.id_product END',
                                 array('product_titles' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT p.title ORDER BY p.title SEPARATOR ', ')"))
                             )
                             ->group($this->_name . '.id');
        }
    
        $commonObj = new My_Controller_Action_Helper_Common();
    
        if (!empty($data['search-key'])) {
            $searchKey = str_replace("'", "", $data['search-key']);
            $select->where("name LIKE '%$searchKey%' OR order_code LIKE '%$searchKey%' OR email LIKE '%$searchKey%' OR phone LIKE '%$searchKey%' OR total LIKE '%$searchKey%'");
            $subQuery = $this->getAdapter()->select()
                            ->from('order_detail', array('id_order'))
                            ->joinLeft('product', 'product.id = CASE WHEN order_detail.id_product = 0 THEN order_detail.product_id_cb ELSE order_detail.id_product END', array())
                            ->where('product.title LIKE ?', "%$searchKey%");
    
            $select->orWhere("{$this->_name}.id IN (?)", $subQuery);
        }
    
        if (!empty($data["created_date"])) {
            $select = $select->where("DATE(created_date) =?", $commonObj->quoteLike($data["created_date"]));
        }
        if (!empty($data["updated_date"])) {
            $select = $select->where("DATE(updated_date) =?", $commonObj->quoteLike($data["updated_date"]));
        }
        if (!empty($data["from_date"])) {
            $select = $select->where("DATE(created_date) >=?", $commonObj->quoteLike($data["from_date"]));
        }
        if (!empty($data["to_date"])) {
            $select = $select->where("DATE(created_date) <=?", $commonObj->quoteLike($data["to_date"]));
        }
        if (!empty($data["name"])) {
            $select = $select->where("name LIKE ?", "%" . $commonObj->quoteLike($data["name"]) . "%");
        }
        // if (!empty($data["status"])) {
        //     $select = $select->where("status = ?", $data["status"]);
        // }
        if (!empty($data["status"])) {
            $select = $select->where("{$this->_name}.status = ?", $data["status"]);
        }
        if (isset($data["is_pay"])) {
            $select = $select->where("is_pay = ?", $data["is_pay"]);
        }
    
        if (empty($data['count_only']) || $data['count_only'] != 1) {
            if (!empty($data["order"])) {
                $order = $data["order"]["column"] . " " . $data["order"]["dir"];
                $select = $select->order($order);
            }
            $start = (!empty($data['start'])) ? $data['start'] : 0;
            $length = (!empty($data['length'])) ? $data['length'] : 0;
            $select = $select->limit($length, $start);
        }
    
        $result = $this->getAdapter()->fetchAll($select);
        if (!empty($data['count_only']) && $data['count_only'] == 1) {
            return $result[0]['cnt'];
        }
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
    public function fetchOrderByOrderCode($order_code) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "order_code = ?", $order_code, Zend_Db::INT_TYPE );
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
    public function updateConfirmOrder($data=[], $order_code) {
        if (!empty($order_code)) {
            $where = $this->getAdapter()->quoteInto("order_code = ?", $order_code); // Sử dụng order_code trong điều kiện WHERE
            return $this->update($data, $where);
        }
        return false;
    }
    
    public function fetchOrderByUserId($userId){
        $select = $this->getAdapter()->select()
            ->from(array("o" => $this->_name), array(
                'order_code'=> 'o.order_code',
                'total'=> 'o.total',
                'order_score' => 'o.score',
                'created_date' => 'o.created_date',
            ))
            ->joinLeft(
                array("u" => "user"),
                "u.user_id = o.user_id",
                array(
                    'user_score' => 'u.score' 
                )
            )
            ->where("o.user_id = ?", $userId)
            ->where("o.status_score = ?", 1)
            ->order("o.created_date DESC");

        $result = $this->getAdapter()->fetchAll($select);

        return empty($result) ? array() : $result;
    }
    
}
