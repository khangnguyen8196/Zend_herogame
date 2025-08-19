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
    public function getAllImageIdsByVariantId($id) {
        $db = $this->getAdapter();
        $where = $db->quoteInto("product_variant_id = ?", $id, Zend_Db::INT_TYPE);
        $result = $this->fetchAll($where, 'id')->toArray(); 
    
        if (empty($result)) {
            return array();
        }
    
        return array_column($result, 'id');
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
    // public function deleteVariantImage( $id ) {
    //     $where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    //     return $this->update( array('status' => STATUS_DELETE ), $where );
    // }
    public function deleteVariantImage($id){
    $where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
    return $this->delete($where);
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
    
    public function getProductImages($product_id) {
        $select = $this->getAdapter()->select();
        $select = $select->from(array('vi' => 'variant_image'), array('id','product_variant_id', 'url_image'))
            ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array());
        $select = $select->where('pv.product_id = ?', $product_id);
        $select = $select->where('vi.status <> ?', STATUS_DELETE);
        $select = $select->order('vi.id ASC');
            
        $rows = $this->getAdapter()->fetchAll($select);
        
        $result = array();
        foreach ($rows as $row) {
            $result[$row['product_variant_id']][] =[
                'url'=>$row['url_image'],
                'id'=>$row['id']
                ] ;
        }
        
        return $result;
    }
    
    // public function getProductImage($product_id) {
    //     $select = $this->getAdapter()->select();
    //     $select = $select->from(array('vi' => 'variant_image'), array('id','product_variant_id', 'url_image'))
    //         ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array());
    //     $select = $select->where('pv.product_id = ?', $product_id);
    //     $select = $select->where('vi.status <> ?', STATUS_DELETE);
    //     $select = $select->order('vi.id ASC');
            
    //     $rows = $this->getAdapter()->fetchAll($select);
        
    //     $result = array();
    //     foreach ($rows as $row) {
    //         $result[$row['product_variant_id']][] =$row['url_image'];
    //     }
        
    //     return $result;
    // }
    // public function getProductImage($product_id) {
    //     $adapter = $this->getAdapter();
    
    //     // Lấy danh sách ảnh
    //     $select = $adapter->select();
    //     $select->from(array('vi' => 'variant_image'), array('id', 'product_variant_id', 'url_image'))
    //         ->join(array('pv' => 'product_variant'), 'vi.product_variant_id = pv.id', array())
    //         ->where('pv.product_id = ?', $product_id)
    //         ->where('vi.status <> ?', STATUS_DELETE)
    //         ->order('vi.id ASC');
    
    //     $rows = $adapter->fetchAll($select);
    
    //     // Gom ảnh theo variant
    //     $allImages = [];
    //     foreach ($rows as $row) {
    //         $allImages[$row['product_variant_id']][] = $row['url_image'];
    //     }
    
    //     // Lấy danh sách variant ID theo thứ tự
    //     $variant_ids = $adapter->fetchCol(
    //         $adapter->select()
    //             ->from('product_variant', 'id')
    //             ->where('product_id = ?', $product_id)
    //             ->order('id ASC')
    //     );
    
    //     $first_variant_id = $variant_ids[0] ?? null;
    //     $first_variant_images = $allImages[$first_variant_id] ?? [];
    
    //     $result = [];
    
    //     foreach ($variant_ids as $variant_id) {
    //         if ($variant_id == $first_variant_id) {
    //             // Giữ nguyên ảnh của variant đầu tiên
    //             $result[$variant_id] = $first_variant_images;
    //         } else {
    //             // Lấy ảnh đầu tiên của chính nó
    //             $own_first = !empty($allImages[$variant_id]) ? [$allImages[$variant_id][0]] : [];
    
    //             // Lấy ảnh từ variant đầu tiên, trừ ảnh đầu tiên
    //             $shared_images = array_slice($first_variant_images, 1);
    
    //             // Gộp lại
    //             $result[$variant_id] = array_merge($own_first, $shared_images);
    //         }
    //     }
    
    //     return $result;
    // }
    public function getProductImage($product_id) {
        $adapter = $this->getAdapter();
    
        // 1. Lấy ảnh từ variant_image (mỗi ảnh là 1 dòng)
        $select = $adapter->select()
            ->from(['vi' => 'variant_image'], ['id', 'product_variant_id', 'url_image'])
            ->join(['pv' => 'product_variant'], 'vi.product_variant_id = pv.id', [])
            ->where('pv.product_id = ?', $product_id)
            ->where('vi.status <> ?', STATUS_DELETE)
            ->order('vi.id ASC');
    
        $rows = $adapter->fetchAll($select);
    
        // 2. Gom ảnh theo từng variant
        $allImages = [];
        foreach ($rows as $row) {
            $allImages[$row['product_variant_id']][] = $row['url_image'];
        }
    
        // 3. Lấy danh sách variant ID theo thứ tự
        $variant_ids = $adapter->fetchCol(
            $adapter->select()
                ->from('product_variant', 'id')
                ->where('product_id = ?', $product_id)
                ->order('id ASC')
        );
    
        $first_variant_id = $variant_ids[0] ?? null;
        $first_variant_images = $allImages[$first_variant_id] ?? [];
    
        // 4. Lấy gallery từ bảng product
        $gallery = $adapter->fetchOne(
            $adapter->select()
                ->from('product', 'gallery')
                ->where('id = ?', $product_id)
                ->limit(1)
        );
    
        // 5. Nếu có gallery, tách chuỗi và merge ảnh vào variant đầu
        if (!empty($gallery)) {
            $galleryImages = array_filter(array_map('trim', explode(',', $gallery)));
            $first_variant_images = array_merge($first_variant_images, $galleryImages);
            $first_variant_images = array_unique($first_variant_images); // loại trùng
        }
    
        // 6. Gán kết quả cho từng variant
        $result = [];
        foreach ($variant_ids as $variant_id) {
            if ($variant_id == $first_variant_id) {
                $result[$variant_id] = $first_variant_images;
            } else {
                $own_first = !empty($allImages[$variant_id]) ? [$allImages[$variant_id][0]] : [];
                $shared_images = array_slice($first_variant_images, 1); // bỏ ảnh đầu
                $result[$variant_id] = array_merge($own_first, $shared_images);
            }
        }
    
        return $result;
    }
}
