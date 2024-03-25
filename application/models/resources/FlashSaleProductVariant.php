<?php

/**
 * Process for Menu
 */
class FlashSaleProductVariant extends Zend_Db_Table_Abstract {

    protected $_name = 'flash_sale_product_variant';
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

    public function deleteFlashSaleProductVariant($id) {
        $where[] = $this->getAdapter()->quoteInto("flash_sale_id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }

    public function deleteFlashSaleProductVariantByProductId( $productId ) {
        $where = $this->getAdapter()->quoteInto("product_id = ?", $productId, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }
    public function deleteFlashSaleProductVariantBy($flash_sale_id, $product_id) {
        $where = array(
            $this->getAdapter()->quoteInto("flash_sale_id = ?", $flash_sale_id, Zend_Db::INT_TYPE),
            $this->getAdapter()->quoteInto("product_id = ?", $product_id, Zend_Db::INT_TYPE)
        );
        return $this->delete($where);
    }

    public function deleteFlashProductVariant($product_id, $variant_id) {
        $where = array(
            $this->getAdapter()->quoteInto("product_id = ?", $product_id, Zend_Db::INT_TYPE),
            $this->getAdapter()->quoteInto("variant_id = ?", $variant_id, Zend_Db::INT_TYPE)
        );
        return $this->delete($where);
    }

    // public function getFlashSaleProductVariantBy($flash_sale_id, $product_id) {
    //     $select = $this->getAdapter()->select()
    //         ->from($this->_name)
    //         ->where('flash_sale_id = ?', $flash_sale_id)
    //         ->where('product_id = ?', $product_id);
    //     return $this->getAdapter()->fetchAll($select);
    // }
    public function getFlashSaleProductVariantBy($flash_sale_id, $product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('fspv' => $this->_name))
            ->join(array('pv' => 'product_variant'), 'pv.id = fspv.variant_id', array('variant_name'))
            ->where('fspv.flash_sale_id = ?', $flash_sale_id)
            ->where('fspv.product_id = ?', $product_id);
            
        return $this->getAdapter()->fetchAll($select);
    }

    public function getAllFlashSaleProductVariantBy($product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('fspv' => $this->_name))
            ->where('fspv.product_id = ?', $product_id);     
        return $this->getAdapter()->fetchAll($select);
    }
    
    

    public function saveFlashSaleProductVariant($data) {
        $datain = array();
        if (isset($data['flash_sale_id']) == true) {
            $datain['flash_sale_id'] = $data['flash_sale_id'];
        }
        if (isset($data['variant_id']) == true) {
            $datain['variant_id'] = $data['variant_id'];
        }
        if (isset($data['variant_price_flash_sale']) == true) {
            $datain['variant_price_flash_sale'] = $data['variant_price_flash_sale'];
        }
        if (isset($data['variant_price']) == true) {
            $datain['variant_price'] = $data['variant_price'];
        }
        if (isset($data['variant_price_sales']) == true) {
            $datain['variant_price_sales'] = $data['variant_price_sales'];
        }
        if (isset($data['product_id']) == true) {
            $datain['product_id'] = $data['product_id'];
        }
        if (isset($data['percent_flash_sale']) == true) {
            $datain['percent_flash_sale'] = $data['percent_flash_sale'];
        }
            return $this->insert($datain);
    }

    public function updateFlashSaleProductVariant($data, $flash_sale_id, $product_id, $variant_id) {
        if (!empty($product_id) && !empty($flash_sale_id) && !empty($variant_id)) {
            $where = array(
                $this->getAdapter()->quoteInto("product_id = ?", $product_id, Zend_Db::INT_TYPE),
                $this->getAdapter()->quoteInto("flash_sale_id = ?", $flash_sale_id, Zend_Db::INT_TYPE),
                $this->getAdapter()->quoteInto("variant_id = ?", $variant_id, Zend_Db::INT_TYPE)
            );
            return $this->update($data, $where);
        }
        return false;
    }  
}
