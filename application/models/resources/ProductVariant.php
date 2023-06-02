<?php

/**
 * Process for Menu
 */
class ProductVariant extends Zend_Db_Table_Abstract {

    protected $_name = 'product_variant';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllVariant() {
        
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
    public function fetchVariantById( $id ) {
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
    public function saveVariant( $data, $id  ) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }

    /**
     * [deleteColor description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteVariant( $id ) {
        $where[] = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
        return $this->update( array('status' => STATUS_DELETE ), $where );
    }

    /**
     * [fetchAllColorByGroup description]
     * @param  [type] $group [description]
     * @return [type]        [description]
     */
    public function fetchAllVariantByGroup( $group ){
        $select = $this->getAdapter()->select()->from($this->_name);
        $select->where('id IN(?)', $group);
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    
    public function fetchAllCustomVariant() {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);
        $select = $select->where("status = ?",STATUS_ACTIVE);
        $select = $select->where("id <> ?", 1);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    public function getProductVariants($product_id) {
        $select = $this->getAdapter()->select()
            ->from(array('p' => 'product_variant'))
            ->where('product_id= ?', $product_id)
            ->order('id ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    public function getLastInsertId() {
        $db = $this->getAdapter();
        return $db->lastInsertId();
    }
    
    
}
