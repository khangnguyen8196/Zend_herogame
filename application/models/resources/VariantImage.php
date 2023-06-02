<?php

/**
 * Process for Menu
 */
class VariantImage extends Zend_Db_Table_Abstract {

    protected $_name = 'variant_image';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllVariantImage() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
        $select = $select->where("status = ?",STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchVariantImageById( $id ) {
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
    public function saveVariantImage($data, $id = 0)
    {
        $affected_rows = 0;
        foreach ($data as $variant_image) {
            if (empty($id) == false) {
                $where[] = $this->getAdapter()->quoteInto("product_variant_id = ?", $id, Zend_Db::INT_TYPE);
                $where[] = $this->getAdapter()->quoteInto("status <> ?", STATUS_DELETE);
                $result = $this->update($variant_image, $where);
            } else {
                $result = $this->insert($variant_image);
            }
            if ($result) {
                $affected_rows += 1;
            }
        }
        return $affected_rows;
    }


    /**
     * [deleteColor description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteVariantImage( $id ) {
        $where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
        return $this->update( array('status' => STATUS_DELETE ), $where );
    }


    

    /**
     * [fetchAllColorByGroup description]
     * @param  [type] $group [description]
     * @return [type]        [description]
     */
    public function fetchAllVariantImageByGroup( $group ){
        $select = $this->getAdapter()->select()->from($this->_name);
        $select->where('id IN(?)', $group);
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    
    public function fetchAllCustomVariantImage() {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);
        $select = $select->where("status = ?",STATUS_ACTIVE);
        $select = $select->where("id <> ?", 1);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    public function getVariantImage($variant_id) {
        $select = $this->getAdapter()->select()
            ->from(array('v' => 'variant_image'))
            ->where('product_variant_id= ?', $variant_id);
        return $this->getAdapter()->fetchAll($select);
    }

    
    public function getAllImages($product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('vi' => 'variant_image'), array(new Zend_Db_Expr("GROUP_CONCAT(url_image SEPARATOR ',') as url_images")))
            ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array())
            ->where('pv.product_id = ?', $product_id);
        $result = $this->getAdapter()->fetchRow($select);
        return $result['url_images'];
    }

    public function deleteImageByProductUrl($product_id, $url_image) {
        $select = $this->getAdapter()->select()
            ->from(array('vi' => 'variant_image'), array('product_variant_id'))
            ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array('product_id'))
            ->where('vi.url_image = ?', $url_image)
            ->limit(1);
    
        $row = $this->getAdapter()->fetchRow($select);
        if (!$row) {
            return false; // Không tìm thấy ảnh cần xóa
        }
    
        $where = array(
            $this->getAdapter()->quoteInto('product_variant_id = ?', $row['product_variant_id']),
            $this->getAdapter()->quoteInto('url_image = ?', $url_image)
        );
    
        $this->getAdapter()->delete('variant_image', $where);
        return true;
    }
    
    
    
    
    // public function getProductImages($product_id) {
    //     $select = $this->getAdapter()->select()
    //         ->from(array('vi' => 'variant_image'), array('product_variant_id', 'images' => new Zend_Db_Expr('GROUP_CONCAT(vi.url_image SEPARATOR ",")')))
    //         ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array())
    //         ->where('pv.product_id = ?', $product_id)
    //         ->group('vi.product_variant_id');
        
    //     $rows = $this->getAdapter()->fetchAll($select);
    
    //     $result = array();
    //     foreach ($rows as $row) {
    //         $result[$row['product_variant_id']] = explode(',', $row['images']);
    //     }
    
    //     return $result;
    // }

    public function getProductImages($product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('vi' => 'variant_image'), array('product_variant_id', 'url_image'))
            ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array())
            ->where('pv.product_id = ?', $product_id)
            ->order('vi.id ASC');
            
        $rows = $this->getAdapter()->fetchAll($select);
        
        $result = array();
        foreach ($rows as $row) {
            $result[$row['product_variant_id']][] = $row['url_image'];
        }
        
        return $result;
    }
    
  
}
