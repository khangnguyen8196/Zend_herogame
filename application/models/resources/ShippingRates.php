<?php

/**
 * Process for Menu
 */
class ShippingRates extends Zend_Db_Table_Abstract {

    protected $_name = 'shipping_rates';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return array <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllShippingRates() {
        
    	$select = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
        $select = $select->where("status = ?",STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    public function listAllComboProduct( $params = array() ) {
        $select = $this->getAdapter()->select();
        $select->from($this->_name);
        if( empty( $params ) == false ){
            foreach ($params as $key => $value ) {
                $select = $select->where($key ." = ?", $value);
            }
        }
        $select = $select->where("status = ?", STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchComboProductById( $id ) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
        $where[] = $db->quoteInto( "status = ?", STATUS_ACTIVE, Zend_Db::INT_TYPE ); 
        $result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
    public function getShippingRatesById( $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "fee_id = ?", $id, Zend_Db::INT_TYPE );
    	$where[] = $db->quoteInto( "status <> ?", STATUS_DELETE );
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
    // public function saveShippingRates( $data, $id  ) {
    //     $datain = array();
    //     if (isset($data['province']) == true) {
    //         $datain['fee_matp'] = $data['province'];
    //     }
    //     if (isset($data['district']) == true) {
    //         $datain['fee_maqh'] = $data['district'];
    //     }
    //     if (isset($data['wards']) == true) {
    //         $datain['fee_xaid'] = $data['wards'];
    //     }
    //     if (isset($data['fee_ship']) == true) {
    //         $datain['fee_ship'] = $data['fee_ship'];
    //     }
    //     if (isset($data['status']) == true) {
    //         $datain['status'] = $data['status'];
    //     }
    //     if (isset($data['created_at']) == true) {
    //         $datain['created_at'] = $data['created_at'];
    //     }
    //     if (isset($data['updated_at']) == true) {
    //         $datain['updated_at'] = $data['updated_at'];
    //     }
    //     if (isset($data['created_by']) == true) {
    //         $datain['created_by'] = $data['created_by'];
    //     }
    //     if (isset($data['updated_by']) == true) {
    //         $datain['updated_by'] = $data['updated_by'];
    //     }
    // 	if ( empty( $id ) == false )  {
    // 		$where[] = $this->getAdapter()->quoteInto( "fee_id = ?", $id, Zend_Db::INT_TYPE );
    // 		return $this->update($datain, $where );
    // 	}else {
    //         return $this->insert($datain);
    //     }
    // }

    public function saveShippingRates($data, $id)
    {
        $datain = array();
        if (isset($data['province']) == true) {
            $datain['fee_matp'] = $data['province'];
        }
        if (isset($data['district']) == true) {
            $datain['fee_maqh'] = $data['district'];
        }
        if (isset($data['fee_ship']) == true) {
            $datain['fee_ship'] = $data['fee_ship'];
        }
        if (isset($data['status']) == true) {
            $datain['status'] = $data['status'];
        }
        if (isset($data['created_at']) == true) {
            $datain['created_at'] = $data['created_at'];
        }
        if (isset($data['updated_at']) == true) {
            $datain['updated_at'] = $data['updated_at'];
        }
        if (isset($data['created_by']) == true) {
            $datain['created_by'] = $data['created_by'];
        }
        if (isset($data['updated_by']) == true) {
            $datain['updated_by'] = $data['updated_by'];
        }
        if (empty($id) == false) {
            $where[] = $this->getAdapter()->quoteInto("fee_matp = ?", $data['province'], Zend_Db::INT_TYPE);
            $where[] = $this->getAdapter()->quoteInto("fee_maqh = ?", $data['district'], Zend_Db::INT_TYPE);
            return $this->update($datain, $where);
        }else{
            $matp = $data['province'] ?$data['province']: null;
            $maqh = $data['district'] ?$data['district']: null;
            if ($matp) {
                $db = $this->getAdapter();
                $select = $db->select()
                    ->from(['p' => 'province'], ['p.matp'])
                    ->join(['d' => 'district'], 'p.matp = d.matp', ['maqh'])
                    ->join(['w' => 'wards'], 'd.maqh = w.maqh', ['xaid'])
                    ->where('p.matp = ?', $matp)
                    ->where('d.maqh = ?', $maqh);

                $result = $db->fetchAll($select);
                if ($result) {
                    $dataToInsert = [];
                    foreach ($result as $row) {
                        $dataToInsert[] = [
                            'fee_matp'   => $row['matp'],
                            'fee_maqh'   => $row['maqh'],
                            'fee_xaid'   => $row['xaid'],
                            'fee_ship'   => $data['fee_ship'] ? $data['fee_ship'] :null,
                            'status'     => $data['status'] ?$data['status']: null,
                            'created_at' => $data['created_at'] ?$data['created_at']: null,
                            'updated_at' => $data['updated_at'] ?$data['updated_at']: null,
                            'created_by' => $data['created_by'] ?$data['created_by']: null,
                        ];
                    }
                    if (!empty($dataToInsert)) {
                        foreach ($dataToInsert as $dataRow) {
                            $this->insert($dataRow);
                        }
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        
    }

    /**
     * [deleteColor description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteShippingRates($id,$matp,$maqh) {
        $where = $this->getAdapter()->quoteInto('fee_id = ?', $id);
        $where = $this->getAdapter()->quoteInto('fee_matp = ?', $matp);
        $where = $this->getAdapter()->quoteInto('fee_maqh = ?', $maqh);
        return $this->delete($where);
    }

    public function getFeeShip($province, $district, $wards) {
        $db = $this->getAdapter();
        $select = $db->select()
            ->from(array('sr' => 'shipping_rates'), array('sr.*'))
            ->where('sr.fee_matp = ?', $province)
            ->where('sr.fee_maqh = ?', $district)
            ->where('sr.fee_xaid = ?', $wards)
            ->order('fee_id DESC'); // Sắp xếp giảm dần theo fee_id
        $result = $db->fetchRow($select);
        return $result;
    }
    public function fetchAllShipping($data = array()) {
        $select = $this->getAdapter()->select();
        $select->from(array('sr' => 'shipping_rates'), array('sr.*'))
        ->join(array('p' => 'province'), 'sr.fee_matp = p.matp', array('p.name_province'))
        ->join(array('d' => 'district'), 'sr.fee_maqh = d.maqh', array('d.name_district'))
        ->join(array('w' => 'wards'), 'sr.fee_xaid = w.xaid', array('w.name_wards'));
        if (!empty($data['search-key'])) {
            $searchKey = '%' . $data['search-key'] . '%';
            $select->where('sr.fee_id LIKE ? OR w.name_wards LIKE ? OR p.name_province LIKE ? OR d.name_district LIKE ?', $searchKey, $searchKey, $searchKey, $searchKey);
        }
        $select->where('sr.status <> ?', STATUS_DELETE);
        if (!empty($data['count_only']) && $data['count_only'] == 1) {
             $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
             $select->where("shipping_rates.status <> ?", STATUS_DELETE );
        }
    
        if( empty( $data['count_only'] ) == true || $data['count_only'] != 1 ) {
        	if ( empty( $data["order"] ) == false ) {
        		$order = $data["order"]["column"] . " " . $data["order"]["dir"];
        		$select = $select->order( $order );
        	}
        	$start = ( empty( $data['start'] ) == false ) ? $data['start'] : 0;
        	$length = ( empty( $data['length'] ) == false ) ? $data['length'] : 0;
        	$select = $select->limit( $length, $start );
        }
        if (!empty($data['start']) && !empty($data['length'])) {
            $start = $data['start'];
            $length = $data['length'];
            $select->limit($length, $start);
        }
        $result = $this->getAdapter()->fetchAll($select);
        if (!empty($data['count_only']) && $data['count_only'] == 1) {
            return (int) $result[0]['cnt'];
        }
        return $result;
    }

    public function checkDataExists($province, $district) {
    $select = $this->select()
        ->where('fee_matp = ?', $province)
        ->where('fee_maqh = ?', $district);
    $row = $this->fetchRow($select);

    return $row !== null;
    }

    public function countRowsWithConditions($province, $district, $wards, $id) {
        $select = $this->select()
            ->from($this, array('count' => 'COUNT(*)'))
            ->where('fee_id <> ?', $id)
            ->where('fee_matp = ?', $province)
            ->where('fee_maqh = ?', $district)
            ->where('fee_xaid = ?', $wards);
    
        $result = $this->fetchRow($select);
    
        return (int)$result->count;
    }
    
}
