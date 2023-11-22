<?php

/**
 * Process for Menu
 */
class District extends Zend_Db_Table_Abstract {

    protected $_name = 'district';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function getAllDistrict() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
        $select = $select->order('maqh ASC');
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }

    public function getAllDistrictByMatp($data) {
        $select = $this->getAdapter()->select()
            ->from(array('cb' => 'district'))
            ->where('matp= ?', $data)
            ->order('maqh ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    public function getListDistrictByMatp($matp) {
        $select = $this->getAdapter()->select()
            ->from(array('cb' => 'district'))
            ->where('matp= ?', $matp)
            ->order('maqh ASC');
        return $this->getAdapter()->fetchAll($select);
    }
       
}
