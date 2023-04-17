<?php

/**
 * Process for Category
 */
class Category extends Zend_Db_Table_Abstract {

    protected $_name = 'category';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllCategory($data = array()) {
        $select = $this->getAdapter()->select();
        if (isset($data['count_only']) == true && $data['count_only'] == 1) {
            $select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
            $select = $select->where("category.status <> ?", STATUS_DELETE);
        } else {
            $select = $select->from($this->_name);
        }
        $select = $select->joinLeft(array('mn' => 'menu'), 'mn.url = category.url_menu', array('menu_name' => 'mn.name'));
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        $select = $select->where("status <> ?", STATUS_DELETE);
        if (empty($data["name"]) == false) {
            $data["name"] = $commonObj->quoteLike($data["name"]);
            $select = $select->where("category.name like ?", "%" . $data["name"] . "%");
        }
        if (empty($data['search-key']) == false) {
            $select = $select->where("category.name like '%" . $data['search-key'] . "%' or mn.name like '%" . $data['search-key'] . "%'  or category.url_slug like '%" . $data['search-key'] . "%'");
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
	public function listAllCategoryHomePage( $data ){
		$select = $this->getAdapter()->select();
        $select->from($this->_name);
        $select = $select->where("status = ?", STATUS_ACTIVE);
        if( empty($data['type']) == false ){
        	$select = $select->where("type_of_category = ?", $data['type']);
        }
        if( empty($data['show_home']) == false ){
        	$select = $select->where("show_in_home_cate_page = ?", $data['show_home']);
        }
        $select = $select->order('priority asc');
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
	}
    public function listAllCategoryByMenu($menu) {
        $select = $this->getAdapter()->select();
        $select->from($this->_name);
        $select = $select->where("status = ?", STATUS_ACTIVE);
        $select = $select->where("menu = ?", $menu);
        $select = $select->order('priority asc');
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    public function listAllCategory( $params = array() ) {
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

    public function listAllCategoryOnMenu() {
        $select = $this->getAdapter()->select();
        $select->from($this->_name);
        $select = $select->where("status = ?", STATUS_ACTIVE);
        $select = $select->where("show_in_menu = ?", 1);
        $select = $select->order('priority asc');
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchCategoryById($id) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
        $where[] = $db->quoteInto("status <> ?", STATUS_DELETE);
        $result = $this->fetchRow($where);
        if (empty($result) == true) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }

    /**
     * 
     * @param unknown $id
     * @return unknown
     */
    public function checkExistCategoryUrl($url_name, $id) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto("url_slug = ?", $url_name);
        if (empty($id) == false) {
            $where[] = $db->quoteInto("id <>?", $id, Zend_Db::INT_TYPE);
        }
        $result = $this->fetchRow($where);
        if (empty($result) == true) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }

    /**
     * Update/Add user
     * @param array $data
     * @return boolean
     */
    public function saveCategory($data, $id) {
        if (empty($id) == false) {
            $where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
            $where[] = $this->getAdapter()->quoteInto("status <> ?", STATUS_DELETE);
            return $this->update($data, $where);
        } else {
            return $this->insert($data);
        }
    }

    public function deleteCategory($id) {
        $where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }
    
    // ----------------------- FRONT END ---------------------------------------
    /**
     * 
     * @param type $params
     * @return type
     */
    public function getCategoryInfoByParams($params = array()) {
        $select = $this->getAdapter()->select();
        $select->from($this->_name);
        $select = $select->where("status = ?", STATUS_ACTIVE);
        if (empty($params) == false && is_array($params) == true) {
            foreach ($params as $key => $value) {
                $select = $select->where("$key = ?", $value);
            }
        }
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    
    /**
     * 
     * @param type $params
     * @return type
     */
    public function getChildCategoryInfoByParentCategoryId($parentId) {
        $select = $this->getAdapter()->select();
        $select->from($this->_name);
        $select = $select->where("status = ?", STATUS_ACTIVE);
        $select = $select->where("parent_category = ?", $parentId);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

}
