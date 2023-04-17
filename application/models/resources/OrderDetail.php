<?php

/**
 * Process for Menu
 */
class OrderDetail extends Zend_Db_Table_Abstract {

    protected $_name = 'order_detail';
    protected $_rowClass = 'DbTableRow';
    /**
     * Update/Add
     * @param array $data
     * @return boolean
     */
    public function saveOrderDetail( $data ) {
    	return $this->insert( $data );
    }
    public function getListOrderDetail( $orderId ){
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name)
    	->join(array("p"=> "product" ), "order_detail.id_product = p.id", array('p_name' => 'p.title', 'p_price' => 'p.price','p_sale' => 'p.price_sales', 'p_img' => 'p.image', 'p_url'=> 'p.url_product') );
    	$select->joinLeft(array('pc' => 'product_color'), 'order_detail.product_color = pc.id', array('p_color_name' => 'pc.color_name'));
    	//search by name
    	$select = $select->where("order_detail.id_order =?", $orderId);
    	$result = $this->getAdapter()->fetchAll($select);
    	if (empty($result) == true) {
    		return array();
    	}
    	return $result;
    }
}
