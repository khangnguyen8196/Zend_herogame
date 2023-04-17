<?php

/**
 * Process for Setting
 */
class Setting extends Zend_Db_Table_Abstract {

    protected $_name = 'setting';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllSetting($data = array()) {
    	$select = $this->getAdapter()->select();
    	$select = $select->from( $this->_name );
        $result = $this->getAdapter()->fetchAll( $select );
        return $result;
    }
    public function fetchSettingByKey( $key ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "`key` = ?", $key );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function saveSetting( $data) {
    	$dataIn = array(
    			'type' => $data['type'],
    			'description' => $data['description']
    	);
    	if( $data['type'] == TYPE_SETTING_VALUE ){
    		$dataIn['value'] = $data['value'];
    	} elseif( $data['type'] == TYPE_SETTING_SHOW_HIDE ){
    		$dataIn['value'] = $data['show-hide'];
    	} else if( $data['type'] == TYPE_SETTING_HTML ){
    		$dataIn['value'] = $data['web_content'];
    	}
    	if ( empty( $data['isEdit'] ) == false && $data['isEdit'] > 0 )  {
    		$where[] = $this->getAdapter()->quoteInto( "`key` = ?", $data['key'] );
    		return $this->update( $dataIn, $where );
    	} else {
    		$dataIn['key'] = $data['key'];
    		return $this->insert( $dataIn );
    	}
    }
    /**
     * Delete User
     * @param int $apiUserId
     * @return number
     */
	public function deleteSetting( $key ){
		$where[] = $this->getAdapter()->quoteInto( "`key` = ?", $key );
		return $this->delete($where );
	}

}
