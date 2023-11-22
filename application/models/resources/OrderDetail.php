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
    // public function getListOrderDetail( $orderId ){
    // 	$select = $this->getAdapter()->select();
    // 	$select = $select->from($this->_name)
    // 	->join(array("p"=> "product" ), "order_detail.id_product = p.id", array('p_name' => 'p.title', 'p_price' => 'p.price','p_sale' => 'p.price_sales', 'p_img' => 'p.image', 'p_url'=> 'p.url_product') );
    // 	$select->joinLeft(array('pv' => 'product_variant'), 'order_detail.product_variant = pv.id', array('p_variant_name' => 'pv.variant_name'));
	// 	$select->join(array('cb' => 'combo_product'), 'order_detail.combo_id = cb.id', array('p_combo_name' => 'cb.title'));

    // 	//search by name
    // 	$select = $select->where("order_detail.id_order =?", $orderId);
    // 	$result = $this->getAdapter()->fetchAll($select);
    // 	if (empty($result) == true) {
    // 		return array();
    // 	}
    // 	return $result;
    // }

	// public function getListOrderDetail( $orderId ){
    // $select = $this->getAdapter()->select();
    // $select = $select->from($this->_name)
    //     ->joinLeft(array("p"=> "product"), "order_detail.id_product = p.id", array(
    //         'p_name' => 'p.title', 
    //         'p_price' => 'p.price',
    //         'p_sale' => 'p.price_sales', 
    //         'p_img' => 'p.image', 
    //         'p_url'=> 'p.url_product') )
    //     ->joinLeft(array('cb' => 'combo_product'), 'order_detail.combo_id = cb.id', array(
    //         'c_combo_name' => 'cb.title',
    //         'c_img' => 'cb.image_cb',
	// 		// 'p_name' => 'cb.title',
    //         'p_price' => new Zend_Db_Expr('IFNULL(p.price, cb.total_discount)'),
    //         'p_sale' => new Zend_Db_Expr('IFNULL(p.price_sales, cb.total_discount)'),
    //         // 'p_url' => new Zend_Db_Expr('IFNULL(p.url_product, cb.url_product)')
    //     ))
    //     ->joinLeft(array('pv' => 'product_variant'), 'order_detail.product_variant = pv.id', array(
    //         'p_variant_name' => 'pv.variant_name',
    //         'p_variant_price'=>'pv.variant_price',
    //         'p_variant_price_sales'=>'pv.variant_price_sales',
            
    //         ))
    //     ->where("order_detail.id_order =?", $orderId);
    // $result = $this->getAdapter()->fetchAll($select);
    // if (empty($result) == true) {
    //     return array();
    // }
    // return $result;
	// }

    public function getListOrderDetail($orderId){
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
            ->joinLeft(array("p" => "product"), "order_detail.id_product = p.id", array(
                'p_name' => 'p.title',
                'p_price' => 'p.price',
                'p_sale' => 'p.price_sales',
                'p_img' => 'p.image',
                'p_url' => 'p.url_product'
            ))
            ->joinLeft(array('cb' => 'combo_product'), 'order_detail.combo_id = cb.id', array(
                'c_combo_name' => 'cb.title',
                'c_img' => 'cb.image_cb',
                'p_price' => new Zend_Db_Expr('IFNULL(p.price, cb.total_discount)'),
                'p_sale' => new Zend_Db_Expr('IFNULL(p.price_sales, cb.total_discount)'),
            ))
            ->joinLeft(array('pv' => 'product_variant'), 'order_detail.product_variant = pv.id', array(
                'p_variant_name' => 'pv.variant_name',
                'p_variant_price' => 'pv.variant_price',
                'p_variant_price_sales' => 'pv.variant_price_sales',
            ))
            ->where("order_detail.id_order =?", $orderId)
            ->where("order_detail.id_product != 0 OR order_detail.combo_id != 0");

        $result = $this->getAdapter()->fetchAll($select);

        if (empty($result)) {
            return array();
        }

        return $result;
    }

    public function getProductByComboIdproduct($orderId,$combo_id) {
        $select = $this->getAdapter()->select();
        $select->from(array('od' => 'order_detail'), array('product_id_cb','cb_id_product'))
            ->join(array('p' => 'product'), 'p.id = od.product_id_cb', array('title','price','price_sales', 'image', 'url_product','combo_id'))
            ->where("od.id_order =?", $orderId)
            ->where('od.cb_id_product = ?', $combo_id)
            ->where('p.status = ?', STATUS_ACTIVE);
        return $this->getAdapter()->fetchAll($select);        
    }


}
