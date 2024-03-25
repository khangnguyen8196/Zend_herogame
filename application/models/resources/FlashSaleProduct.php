<?php

/**
 * Process for Menu
 */
class FlashSaleProduct extends Zend_Db_Table_Abstract {

    protected $_name = 'flash_sale_product';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function getAllFlashSale() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
    	$select = $select->order('matp ASC');
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    public function getProductFlashSaleByProductId($product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('fsp' => 'flash_sale_product'))
            ->where('product_id= ?', $product_id)
            ->order('id ASC');
        return $this->getAdapter()->fetchAll($select);
    }
    public function getFlashSaleProductById($flash_sale_id) {
        $select = $this->getAdapter()->select()
            ->from(array('fsp' => 'flash_sale_product'))
            ->where('flash_sale_id= ?', $flash_sale_id)
            ->order('id ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    public function getFlashSaleProductId($flash_sale_id) {
        $select = $this->getAdapter()->select()
            ->from(array('fsp' => 'flash_sale_product'), 'product_id') 
            ->where('fsp.flash_sale_id = ?', $flash_sale_id)
            ->order('id ASC');
        $results = $this->getAdapter()->fetchAll($select);
        
        $productIds = array();
        foreach ($results as $result) {
            $productIds[] = $result['product_id'];
        }
        return $productIds;
    }
    

    public function fetchFlashSaleProductById($id) {
        $select = $this->getAdapter()->select()
            ->from(array('fsp' => 'flash_sale_product'))
            ->where('id= ?', $id);
        return $this->getAdapter()->fetchRow($select);
    }

    public function deleteFlashSaleProductById($id) {
        $where = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }

    public function deleteFlashSaleProduct($flash_sale_id) {
        $where[] = $this->getAdapter()->quoteInto("flash_sale_id = ?", $flash_sale_id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }

    public function deleteFlashSaleProductByProductId($productId) {
        $where = $this->getAdapter()->quoteInto("product_id = ?", $productId, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }
    public function deleteFlashSaleProductBy($flash_sale_id, $product_id) {
        $where = array(
            $this->getAdapter()->quoteInto("flash_sale_id = ?", $flash_sale_id, Zend_Db::INT_TYPE),
            $this->getAdapter()->quoteInto("product_id = ?", $product_id, Zend_Db::INT_TYPE)
        );
        return $this->delete($where);
    }

    public function getFlashSaleProductBy($flash_sale_id, $product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('fspv' => $this->_name))
            ->where('fspv.flash_sale_id = ?', $flash_sale_id)
            ->where('fspv.product_id = ?', $product_id);
            
        return $this->getAdapter()->fetchRow($select);
    }
    

    public function saveFlashSaleProduct( $data) {
        $datain = array();
        if (isset($data['flash_sale_id']) == true) {
            $datain['flash_sale_id'] = $data['flash_sale_id'];
        }
        if (isset($data['product_name']) == true) {
            $datain['product_name'] = $data['product_name'];
        }
        if (isset($data['product_id']) == true) {
            $datain['product_id'] = $data['product_id'];
        }
        if (isset($data['price']) == true) {
            $datain['price'] = $data['price'];
        }
        if (isset($data['price_sales']) == true) {
            $datain['price_sales'] = $data['price_sales'];
        }
        if (isset($data['price_flash_sale']) == true) {
            $datain['price_flash_sale'] = $data['price_flash_sale'];
        }
        if (isset($data['percent_flash_sale']) == true) {
            $datain['percent_flash_sale'] = $data['percent_flash_sale'];
        }
            return $this->insert($datain);
    }

    public function updateFlashSaleProduct($data, $flash_sale_id, $product_id) {
        if (!empty($product_id) && !empty($flash_sale_id)) {
            $where = array(
                $this->getAdapter()->quoteInto("product_id = ?", $product_id, Zend_Db::INT_TYPE),
                $this->getAdapter()->quoteInto("flash_sale_id = ?", $flash_sale_id, Zend_Db::INT_TYPE)
            );
            return $this->update($data, $where);
        }
        return false;
    }
}
