<?php

/**
 * Process for Menu
 */
class ComboDetail extends Zend_Db_Table_Abstract {

    protected $_name = 'combo_detail';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllComboDetail() {
        
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
    public function fetchComboDetailById( $id ) {
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
    public function saveComboDetail( $data, $id  ) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }


    /**
     * [deleteComboDetail description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    // public function deleteComboDetail( $id ) {
    //     $where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    //     return $this->update( array('status' => STATUS_DELETE ), $where );
    // }
    public function deleteComboDetail($id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }    
    public function getComboDetailById($combo_id) {
        $select = $this->getAdapter()->select()
            ->from(array('cb' => 'combo_detail'))
            ->where('combo_id= ?', $combo_id)
            ->order('id ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    public function getAllComboDetailIdsByComboId($combo_id) {
        $select = $this->getAdapter()->select()
            ->from(array('cbd' => 'combo_detail'), array('cbd.*'))
            ->join(array('cb' => 'combo_product'), 'cbd.combo_id = cb.id', array())
            ->where('cbd.combo_id = ?', $combo_id);
        $results = $this->getAdapter()->fetchAll($select);
        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        return $ids;
    }

    public function getProductByComboId($combo_id) {
        $select = $this->getAdapter()->select();
        $select->from(array('cd' => 'combo_detail'), array('product_id'))
            ->join(array('p' => 'product'), 'p.id = cd.product_id', array('title','price','price_sales', 'image', 'url_product','combo_id'))
            ->where('cd.combo_id = ?', $combo_id)
            ->where('cd.status = ?', STATUS_ACTIVE)
            ->where('p.status = ?', STATUS_ACTIVE);
        return $this->getAdapter()->fetchAll($select);        
    }
    public function getComboByProductId($product_id) {
        $select = $this->getAdapter()->select();
        $select->from(array('cd' => 'combo_detail'), array('product_id','combo_id'))
            ->join(array('cb' => 'combo_product'), 'cb.id = cd.combo_id', array('title','total_discount','image_cb'))
            ->where('cd.product_id = ?', $product_id)
            ->where('cd.status = ?', STATUS_ACTIVE)
            ->where('cb.status = ?', STATUS_ACTIVE);
        return $this->getAdapter()->fetchAll($select);        
    }
    
    
    
 
}
