<?php

/**
 * Process for Category
 */
class Post extends Zend_Db_Table_Abstract {

    protected $_name = 'post';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllPost($data = array()) {
        $select = $this->getAdapter()->select();
        if (isset($data['count_only']) == true && $data['count_only'] == 1) {
            $select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
            $select = $select->where("post.status <> ?", STATUS_DELETE);
        } else {
            $select = $select->from($this->_name)
                    ->columns(array('post.created_at' => new Zend_Db_Expr("DATE_FORMAT(post.created_at,'%Y-%m-%d %H:%i:%s')")))
                    ->columns(array('post.updated_at' => new Zend_Db_Expr("DATE_FORMAT(post.updated_at,'%Y-%m-%d %H:%i:%s')")));
        }
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        $select = $select->where("post.status <> ?", STATUS_DELETE);

        if (empty($data["title"]) == false) {
            $data["title"] = $commonObj->quoteLike($data["title"]);
            $select = $select->where("post.title like ?", "%" . $data["title"] . "%");
        }
        if (empty($data["created_at"]) == false) {
            $data["created_at"] = $commonObj->quoteLike($data["created_at"]);
            $select = $select->where("DATE(post.created_at) =?", $data["created_at"]);
        }
        if (empty($data["updated_at"]) == false) {
            $data["updated_at"] = $commonObj->quoteLike($data["updated_at"]);
            $select = $select->where("DATE(post.updated_at) =?", $data["updated_at"]);
        }
        if (empty($data['search-key']) == false) {
            $select->where("post.title like '%" . $data['search-key'] . "%' or post.created_by like '%" . $data['search-key'] . "%'
        			 or post.updated_by like '%" . $data['search-key'] . "%'");
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
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchPostById($id) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);
        $commonObj = new My_Controller_Action_Helper_Common();
        $id = $commonObj->quoteLike($id);
        $select = $select->where("post.post_id =?", $id);
        $select = $select->where("post.status <>?", STATUS_DELETE);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }

    /**
     * 
     * @param unknown $id
     * @return unknown
     */
    public function checkExistPostUrl($url_name, $id) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto("url_name = ?", $url_name);
        $where[] = $db->quoteInto("status <>  ?", STATUS_DELETE);
        if (empty($id) == false && $id > 0) {
            $where[] = $db->quoteInto("post_id <> ?", $id, Zend_Db::INT_TYPE);
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
    public function savePost($data, $id = 0) {
        $datain = array();
        if (isset($data['title']) == true) {
            $datain['title'] = $data['title'];
        }
        if (isset($data['url_name']) == true) {
            $datain['url_name'] = $data['url_name'];
        }
        if (isset($data['summary']) == true) {
            $datain['summary'] = $data['summary'];
        }
        if (isset($data['content']) == true) {
            $datain['content'] = $data['content'];
        }
        if (isset($data['status']) == true) {
            $datain['status'] = $data['status'];
        }
        if (isset($data['summary']) == true) {
            $datain['summary'] = $data['summary'];
        }
        if (isset($data['content']) == true) {
            $datain['content'] = $data['content'];
        }
    	if (isset($data['keyword_meta']) == true) {
            $datain['keyword_meta'] = $data['keyword_meta'];
        }
        if (isset($data['priority']) == true) {
            $datain['priority'] = $data['priority'];
        }
        if (isset($data['og_url']) == true) {
            $datain['og_url'] = $data['og_url'];
        }
        if (isset($data['og_title']) == true) {
            $datain['og_title'] = $data['og_title'];
        }
        if (isset($data['og_description']) == true) {
            $datain['og_description'] = $data['og_description'];
        }

        if (isset($data['og_site_name']) == true) {
            $datain['og_site_name'] = $data['og_site_name'];
        }
        if (isset($data['og_image']) == true) {
            $datain['og_image'] = $data['og_image'];
        }
        if (isset($data['description_meta']) == true) {
            $datain['description_meta'] = $data['description_meta'];
        }
        if (isset($data['tag']) == true) {
            $datain['tag'] = $data['tag'];
        }
        if (isset($data['destination']) == true) {
            $datain['destination'] = $data['destination'];
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
        if (isset($data['image_id']) == true) {
            $datain['image_id'] = $data['image_id'];
        }
        if (isset($data['id_category']) == true) {
            $datain['id_category'] = $data['id_category'];
        }
        if (isset($data["relative_post"]) == true) {
            $datain["relative_post"] = implode(',', $data["relative_post"]);
        }
        if (isset($data['relative_product']) == true) {
            $datain['relative_product'] = implode(',',$data['relative_product']);
        }
        if (empty($id) == false) {
            $where[] = $this->getAdapter()->quoteInto("post_id = ?", $id, Zend_Db::INT_TYPE);
            return $this->update($datain, $where);
        } else {
            return $this->insert($datain);
        }
    }

    public function deletePost($id) {
        $where[] = $this->getAdapter()->quoteInto("post_id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }

    public function getPostByCategoryId($categoryId) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);

        $commonObj = new My_Controller_Action_Helper_Common();
        $categoryId = $commonObj->quoteLike($categoryId);
        $select = $select->where("id_category =?", $categoryId);
        $select = $select->where("status <>?", STATUS_DELETE);
        $select = $select->order("priority DESC");

        return $select;
    }

    public function fetchPostByUrl($url) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);
        $select = $select->joinLeft(array('c' => 'category'), 'c.id = post.id_category', array('category-name' => 'c.name', 'category-url' => 'c.url_slug'));
        $commonObj = new My_Controller_Action_Helper_Common();
        $id = $commonObj->quoteLike($url);
        $select = $select->where("url_name =?", $url);
        $select->where("post.status =?", STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }

    // ---------------------- FRONT END-----------------------------
    public function getPosts($params = array()) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
                ->columns(array('post.created_at' => new Zend_Db_Expr("DATE_FORMAT(post.created_at,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('post.updated_at' => new Zend_Db_Expr("DATE_FORMAT(post.updated_at,'%Y-%m-%d %H:%i:%s')")));
        
        if (empty($params["updated_at"]) == false && empty($params["condition"]) == false) {
            $select = $select->where("post.updated_at " . $params["condition"] . " ? ", $params["updated_at"]);
        }
        if (empty($params["limit"]) == false) {
        	$start = ( empty($params['start']) == false ) ? $params['start'] : 0;
        	$select = $select->limit($params["limit"], $start);
        }
        if( empty( $params['id_category'] ) == false ){
        	$select = $select->where("id_category =?", $params['id_category']);
        }
        $select = $select->order("post.priority DESC");
        $select = $select->where("post.status = ?", STATUS_ACTIVE);
        $result = $this->getAdapter()->fetchAll($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }
    /**
     * 
     * @param type $key
     * @return type
     */
    public function search($key) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
                ->columns(array('post.created_at' => new Zend_Db_Expr("DATE_FORMAT(post.created_at,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('post.updated_at' => new Zend_Db_Expr("DATE_FORMAT(post.updated_at,'%Y-%m-%d %H:%i:%s')")));
        $select = $select->where("post.status = ?", STATUS_ACTIVE);
        if (empty($key) == false) {
            $select->where('upper( post.title ) LIKE upper(?) or upper( post.summary ) LIKE upper(?) or upper( post.content ) LIKE upper(?)', '%' . $key . '%');
            $case = new Zend_Db_Expr($this->getAdapter()->quoteInto('case when upper( post.title ) LIKE upper(?) then 1 when upper( post.summary ) LIKE upper(?) then 2 else 3 end', '%' . $key . '%'));
            $select = $select->order($case);
        }
        $result = $this->getAdapter()->fetchAll($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }

}
