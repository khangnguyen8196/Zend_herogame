<?php

/**
 * Process for user
 */
class Users extends Zend_Db_Table_Abstract {

    protected $_name = 'user';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllUsers($data = array()) {
        $select = $this->getAdapter()->select();
        if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
            $select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
            $select = $select->where( "user.status <> ?", STATUS_DELETE);
    	} else {
            $select = $select->from($this->_name)
                ->columns(array('user.created_at' => new Zend_Db_Expr("DATE_FORMAT(user.created_at,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('user.updated_at' => new Zend_Db_Expr("DATE_FORMAT(user.updated_at,'%Y-%m-%d %H:%i:%s')")));
        }
        $select = $select->joinLeft('role', 'role.role_id = user.role_id', 'role_name as role_name');
        $select = $select->where( "user.status <> ?", STATUS_DELETE);
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        if (empty($data["user_name"]) == false) {
            $data["user_name"] = $commonObj->quoteLike($data["user_name"]);
            $select = $select->where("user_name like ?", "%" . $data["user_name"] . "%");
        }
        //search by email
        if (empty($data["email"]) == false) {
            $data["email"] = $commonObj->quoteLike($data["email"]);
            $select = $select->where("email like ?", "%" . $data["email"] . "%");
        }
        //search by email
        if (empty($data["status"]) == false) {
            $data["status"] = $commonObj->quoteLike($data["status"]);
            $select = $select->where("user.status =?", $data["status"]);
        }

        //search by role
        if (empty($data["role_id"]) == false && intval($data["role_id"]) > 0) {
            $data["role_id"] = $commonObj->quoteLike($data["role_id"]);
            $likeExp1 = $data["role_id"] . ",%";
            $likeExp2 = "%," . $data["role_id"] . ",%";
            $likeExp3 = "%," . $data["role_id"];
            $equalExp = $data["role_id"];
            $select = $select->where("user.role_id like '" . $likeExp1 . "'  OR user.role_id like '" . $likeExp2 . "' OR user.role_id like '" . $likeExp3 . "' OR user.role_id = '" . $equalExp . "'");
        }
        if ( empty($data['search-key']) == false ){
        	$select = $select->where("phone_number like ?", "%" . $data["search-key"] . "%")
        	->orWhere("user.created_by like ?", "%" . $data["search-key"] . "%")
        	->orWhere("user.updated_by like ?", "%" . $data["search-key"] . "%")
            // ->orWhere("first_name like ?", "%" . $data["search-key"] . "%")
            ->orWhere("email like ?", "%" . $data["search-key"] . "%")
            ->orWhere("fullname like ?", "%" . $data["search-key"] . "%");
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
    public function searchAllUserCustomer($data){
    	$select = $this->getAdapter()->select();
    	if (isset($data['count_only']) == true && $data['count_only'] == 1) {
    		$select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
    		$select = $select->where("user.status = ?", STATUS_ACTIVE);
    	} else {
    		$select = $select->from($this->_name,array('id' => 'user_id','title'=>'user_name','first_name' => 'first_name','last_name' => 'last_name','email' => 'email'));
    		$select = $select->where("user.status = ?", STATUS_ACTIVE);
    	}
    	$commonObj = new My_Controller_Action_Helper_Common();
    	//search by name
    	if ( empty($data["q"]) == false ) {
    		$select = $select->where("user_name like ?", "%" . $data["q"] . "%");
    	}
    	$select = $select->where("role_id = ?", 4);
    	//check count only purpose
    	if (empty($data['count_only']) == true || $data['count_only'] != 1) {
    		$start = ( empty($data['start']) == false ) ? $data['start'] : 0;
    		$length = ( empty($data['length']) == false ) ? $data['length'] : 0;
    		$select = $select->limit($length, $start);
    	}
    	$result = $this->getAdapter()->fetchAll($select);
    	if (empty($data['count_only']) == false && $data['count_only'] == 1) {
    		return $result[0]['cnt'];
    	}
    	return $result;
    }
    /**
     * Get user info by user id
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchUserByParam($params) {
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
    public function getUserById( $id ){
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "user_id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    /**
     * Update user
     * @param array $data
     * @param int $userId
     * @return number
     */
    public function addUser( $data ) {
        return $this->insert( $data );
    }
    /**
     * Update user
     * @param array $data
     * @param int $userId
     * @return number
     */
    public function updateUser($data, $userId) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto("user_id = ?", $userId, Zend_Db::INT_TYPE);
        return $this->update($data, $where);
    }

    /**
     * Delete User
     * @param int $apiUserId
     * @return number
     */
    public function deleteUser($userId) {
        $where = $this->getAdapter()->quoteInto('user_id = ?', $userId);
        return $this->delete($where);
    }

}
