<?php

/**
 * Process for Menu
 */
class Province extends Zend_Db_Table_Abstract {

    protected $_name = 'province';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function getAllProvince() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
    	$select = $select->order('matp ASC');
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
       
}
