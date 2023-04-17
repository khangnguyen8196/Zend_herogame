<?php

/**
 * Role model
 *
 */
class Role extends Zend_Db_Table_Abstract {

    protected $_name = 'role';
    protected $_rowClass = 'DbTableRow';

    /**
     * Fetch all roles
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchRoles($data = array()) {
        $select = $this->getAdapter()->select();
        if (isset($data['count_only']) == true && $data['count_only'] == 1) {
            $select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
            $select = $select->where( "status <> ?", STATUS_DELETE);
        } else {
            $select = $select->from($this->_name);
        }
        $commonObj = new My_Controller_Action_Helper_Common();
        $select = $select->where( "status <> ?", STATUS_DELETE);
        //search by name
        if (empty($data["role_name"]) == false) {
            $data["role_name"] = $commonObj->quoteLike($data["role_name"]);
            $select = $select->where("role_name like ?", "%" . $data["role_name"] . "%");
        }
        if (empty($data["order"]) == false) {
            $select = $select->order($data["order"]["column"] . ' ' . $data["order"]["dir"]);
        }
        if ( empty($data['search-key']) == false ){
        	$select = $select->where("user_name like ?", "%" . $data["search-key"] . "%")
        	->orWhere("created_by like ?", "%" . $data["search-key"] . "%")
        	->orWhere("updated_by like ?", "%" . $data["search-key"] . "%")
                ->orWhere("role_name like ?", "%" . $data["search-key"] . "%");
        }
        //check count only purpose
        if (empty($data['count_only']) == true || $data['count_only'] != 1) {
            if (empty($data["order"]) == false) {
                $order = $data["order"]["column"] . " " . $data["order"]["dir"];
                $select = $select->order($order);
            }
            $start = ( empty($data['start']) == false ) ? $data['start'] : 0;
            $length = ( empty($data['length']) == false ) ? $data['length'] : 0;
            $select = $select->limit($length, $start);
        }
        $result = $this->getAdapter()->fetchAll($select);
        if (empty($data['count_only']) == false && $data['count_only'] == 1) {
            return $result[0]['cnt'];
        }
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    /**
     * Delete User Type
     * @param int $userTypeId
     * @return number
     */
    public function deleteUserType($userTypeId) {
        $response = "";
        $where = $this->getAdapter()->quoteInto("role_id = ?", $userTypeId, Zend_Db::INT_TYPE);
        $response = $this->delete($where);
        UtilLogs::logHistory(LOG_ACTION_DELETE, Commons::getUtilsNameXml(Constants::USER_TYPE_CTRL), $userTypeId, '', array());
        if (empty($response) == false) {
            $tags = array(UtilEncryption::generateKeyCache(Commons::getTagsCache('/user-type/')));
            UtilCache::cleanCacheUser(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        }
        return $response;
    }

    /**
     * Get role by id
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchRoleByParam($params) {
        $select = $this->getAdapter()->select()->from($this->_name);
        if (empty($params) == false) {
            foreach ($params as $key => $value) {
                $select = $select->where(' ' . $key . '= ?', $value);
            }
        }
        $select = $select->where( "status <> ?", STATUS_DELETE);
        $result = $this->getAdapter()->fetchRow($select);
        return $result;
    }

    /**
     * Update/Add user type
     * @param array $data
     * @return boolean
     */
    public function updateRole($data) {
        $where = $this->getAdapter()->quoteInto('role_id = ?', $data["role_id"]);
        $response = $this->update($data, $where);
        return $response;
    }

    /**
     * 
     * @param type $data
     * @return type
     */
    public function insertRole( $data ){
        $roleId = $this->insert($data);
        return $roleId;
    }

}
