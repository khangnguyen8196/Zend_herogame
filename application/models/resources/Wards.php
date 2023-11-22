<?php

/**
 * Process for Menu
 */
class Wards extends Zend_Db_Table_Abstract {

    protected $_name = 'wards';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function getAllWards() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
        $select = $select->order('xaid ASC');
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }

    public function getAllWardsByMatp($data) {
        $select = $this->getAdapter()->select()
            ->from(array('cb' => 'wards'))
            ->where('maqh= ?', $data)
            ->order('xaid ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    public function getListWardsByMatp($maqh) {
        $select = $this->getAdapter()->select()
            ->from(array('cb' => 'wards'))
            ->where('maqh= ?', $maqh)
            ->order('xaid ASC');
        return $this->getAdapter()->fetchAll($select);
    }
    
       
}
