<?php

/**
 * Process for Category
 */
class Product extends Zend_Db_Table_Abstract {

    protected $_name = 'product';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllProduct($data = array()) {
        $select = $this->getAdapter()->select();
        if (isset($data['count_only']) == true && $data['count_only'] == 1) {
            $select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
            $select = $select->where("product.status <> ?", STATUS_DELETE);
        } else {
            $select = $select->from($this->_name)
                    ->columns(array('product.created_date' => new Zend_Db_Expr("DATE_FORMAT(product.created_date,'%Y-%m-%d %H:%i:%s')")))
                    ->columns(array('product.updated_date' => new Zend_Db_Expr("DATE_FORMAT(product.updated_date,'%Y-%m-%d %H:%i:%s')")));
        }
        $select = $select->joinLeft(array('c' => 'category'), 'c.id = product.id_category', array('category_name' => 'c.name'));
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        $select = $select->where("product.status <> ?", STATUS_DELETE);

        if (empty($data["title"]) == false) {
            $data["title"] = $commonObj->quoteLike($data["title"]);
            $select = $select->where("product.title like ?", "%" . $data["title"] . "%");
        }
        if (empty($data["created_date"]) == false) {
            $data["created_date"] = $commonObj->quoteLike($data["created_date"]);
            $select = $select->where("DATE(product.created_date) =?", $data["created_date"]);
        }
        if (empty($data["updated_date"]) == false) {
            $data["updated_date"] = $commonObj->quoteLike($data["updated_date"]);
            $select = $select->where("DATE(product.updated_date) =?", $data["updated_date"]);
        }
        if (empty($data['search-key']) == false) {
            $select->where("product.title like '%" . $data['search-key'] . "%' or c.name like '%" . $data['search-key'] . "%'
        			 or product.updated_by like '%" . $data['search-key'] . "%'");
        }
        if (empty($data['name_category']) == false) {
            $select->where('category.name =?', $data['name_category']);
        }
        if (empty($data['status']) == false) {
            $select->where('product.status =?', $data['status']);
        }
        if (empty($data['id_category']) == false) {
        	$select->where('id_category =?', $data['id_category']);
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

    public function searchAllProduct($data) {
        $select = $this->getAdapter()->select();
        if (isset($data['count_only']) == true && $data['count_only'] == 1) {
            $select = $select->from($this->_name, array("cnt" => new Zend_Db_Expr("COUNT(1)")));
            $select = $select->where("product.status = ?", STATUS_ACTIVE);
        } else {
            $select = $select->from($this->_name);
            $select = $select->where("product.status = ?", STATUS_ACTIVE);
        }
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        if (empty($data["q"]) == false) {
            $select = $select->where("title like ?", "%" . $data["q"] . "%");
        }
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
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchProductById($id) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
                ->columns(array('product.created_date' => new Zend_Db_Expr("DATE_FORMAT(product.created_date,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('product.updated_date' => new Zend_Db_Expr("DATE_FORMAT(product.updated_date,'%Y-%m-%d %H:%i:%s')")));

        $select = $select->joinLeft( array('c' => 'category'), 'c.id = product.id_category', array( 'category_name' => 'c.name',"category_url" =>"c.url_slug" ));
        $commonObj = new My_Controller_Action_Helper_Common();
        $id = $commonObj->quoteLike($id);
        $select = $select->where("product.id =?", $id);
        $select = $select->where("product.status <>?", STATUS_DELETE);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }

    public function fetchProductByUrl($url) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
                ->columns(array('product.created_date' => new Zend_Db_Expr("DATE_FORMAT(product.created_date,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('product.updated_date' => new Zend_Db_Expr("DATE_FORMAT(product.updated_date,'%Y-%m-%d %H:%i:%s')")));

        $select = $select->joinLeft( array('c' => 'category'), 'c.id = product.id_category', array( 'category_name' => 'c.name',"category_url" =>"c.url_slug" ));
        $commonObj = new My_Controller_Action_Helper_Common();
        $id = $commonObj->quoteLike($url);
        $select = $select->where("url_product =?", $url);
        $select->where("product.status <>?", STATUS_DELETE);
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
    public function checkExistProductUrl($url_name, $id) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto("url_product = ?", $url_name);
        $where[] = $db->quoteInto("status <>  ?", STATUS_DELETE);
        if (empty($id) == false && $id > 0) {
            $where[] = $db->quoteInto("id <> ?", $id, Zend_Db::INT_TYPE);
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
    public function saveProduct($data, $id = 0) {
        $datain = array();
        if (isset($data['title']) == true) {
            $datain['title'] = $data['title'];
        }
        if (isset($data['url_product']) == true) {
            $datain['url_product'] = $data['url_product'];
        }
        if (isset($data['description']) == true) {
            $datain['description'] = $data['description'];
        }
        if (isset($data['image']) == true) {
            $datain['image'] = $data['image'];
        }
        if (isset($data['status']) == true) {
            $datain['status'] = $data['status'];
        }
        if (isset($data['gallery']) == true) {
            $datain['gallery'] = $data['gallery'];
        }
        if (isset($data['content']) == true) {
            $datain['content'] = $data['content'];
        }
        if (isset($data['priority']) == true) {
            $datain['priority'] = $data['priority'];
        }
        if (isset($data['price']) == true) {
            $datain['price'] = $data['price'];
        }
        if (isset($data['price_sales']) == true) {
            $datain['price_sales'] = $data['price_sales'];
        }
        if (isset($data['price_flash_sale']) == true) {
            $datain['price_flash_sale'] = $data['price_flash_sale'];
        }
        if (isset($data['tag']) == true) {
            $datain['tag'] = $data['tag'];
        }
        if (isset($data['title_page']) == true) {
            $datain['title_page'] = $data['title_page'];
        }
        if (isset($data['keyword']) == true) {
            $datain['keyword'] = $data['keyword'];
        }
        if (isset($data['meta_description']) == true) {
            $datain['meta_description'] = $data['meta_description'];
        }
        if (isset($data['url_menu']) == true) {
            $datain['url_menu'] = $data['url_menu'];
        }
        if (isset($data['id_category']) == true) {
            $datain['id_category'] = $data['id_category'];
        }
        // if (isset($data['combo_id']) == true) {
            //     $datain['combo_id'] = $data['combo_id'];
            // }
        if (isset($data['created_at']) == true) {
            $datain['created_date'] = $data['created_at'];
        }
            if (isset($data['combo_id']) == true) {
            $datain['combo_id'] = implode(',',$data['combo_id']);
        }
        if (isset($data['updated_at']) == true) {
            $datain['updated_date'] = $data['updated_at'];
        }
        if (isset($data['created_by']) == true) {
            $datain['created_by'] = $data['created_by'];
        }
        if (isset($data['updated_by']) == true) {
            $datain['updated_by'] = $data['updated_by'];
        }
        if (isset($data['og_url']) == true) {
            $datain['og_url'] = $data['og_url'];
        }
    	if (isset($data['guarantee']) == true) {
            $datain['guarantee'] = $data['guarantee'];
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
        if (isset($data['notice_message']) == true) {
            $datain['notice_message'] = $data['notice_message'];
        }
        if (isset($data['color']) == true) {
            $datain['color'] = $data['color'];
        }
        if (isset($data['og_image']) == true) {
            $datain['og_image'] = $data['og_image'];
        }
    	if (isset($data['product_note']) == true) {
            $datain['product_note'] = $data['product_note'];
        }
        if (isset($data['relative_product']) == true) {
            $datain['relative_product'] = $data['relative_product'];
        }
        if (isset($data['order_with_product']) == true) {
            $datain['order_with_product'] = $data['order_with_product'];
        }
        if (isset($data['product_color']) == true) {
            $datain['product_color'] = $data['product_color'];
        }
        if (isset($data['image_color']) == true) {
            $datain['image_color'] = $data['image_color'];
        }
        if (isset($data["new_product"]) == true) {
            $datain["new_product"] = $data['new_product'];
        } else {
            $datain["new_product"] = 0;
        }
        if (isset($data["enable_promo"]) == true) {
            $datain["enable_promo"] = $data['enable_promo'];
        } else {
            $datain["enable_promo"] = 0;
        }
        if (isset($data['count_time']) == true) {
            $datain['count_time'] = $data['count_time'];
        }
        if (isset($data["best_sell"]) == true) {
            $datain["best_sell"] = $data['best_sell'];
        } else {
            $datain["best_sell"] = 0;
        }
        if (isset($data["is_promotion"]) == true) {
            $datain["is_promotion"] = $data['is_promotion'];
        } else {
            $datain["is_promotion"] = 0;
        }
    	if (isset($data["show_in_category_home_page"]) == true) {
            $datain["show_in_category_home_page"] = $data['show_in_category_home_page'];
        } else {
            $datain["show_in_category_home_page"] = 0;
        }
        if (empty($id) == false) {
            $where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
            return $this->update($datain, $where);
        } else {
        	$datain['product_code'] = substr( uniqid(), 7, 13);
            return $this->insert($datain);
        }
    }

    public function deleteProduct($id) {
        $where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
        return $this->delete($where);
    }

    //---------------------------- FRONT END------------------------------
    /**
     * get category info
     * @param int $id
     * @return multitype:|unknown
     */
    public function getProductsByCategoryId($categoryGroup, $params = array()) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);

        $select = $select->where("id_category IN (?)", $categoryGroup);
        $select = $select->where("status <>?", STATUS_DELETE);
        $select = $select->where("product.status <> ?", STATUS_IN_ACTIVE);
        if (empty($params["minRange"]) == false && is_numeric($params["minRange"]) == true) {
            $select = $select->where("price_sales >=?", $params["minRange"]);
        }
        if (empty($params["maxRange"]) == false && is_numeric($params["maxRange"]) == true) {
            $select = $select->where("price_sales <=?", $params["maxRange"]);
        }
        if (empty($params["sort"]) == false) {
            $select = $select->order($params["sort"]);
        } else {
            $select = $select->order("priority desc");
        }
        return $select;
    }

     /**
     * 
     * @param type $params
     * @return type
     */
    public function getProducts($params = array()) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
                ->columns(array('product.created_date' => new Zend_Db_Expr("DATE_FORMAT(product.created_date,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('product.updated_date' => new Zend_Db_Expr("DATE_FORMAT(product.updated_date,'%Y-%m-%d %H:%i:%s')")));

        $select = $select->joinLeft( array('c' => 'category'), 'c.id = product.id_category', array( 'category_name' => 'c.name', 'c_priority' => 'c.priority' ));

        if (empty($params["best_sell"]) == false && $params["best_sell"] == 1) {
            $select = $select->where("product.best_sell =? ", $params["best_sell"]);
        }
        if (empty($params["new_product"]) == false && $params["new_product"] == 1) {
            $select = $select->where("product.new_product =?", $params["new_product"]);
        }
        if (empty($params["is_promotion"]) == false && $params["is_promotion"] == 1) {
            $select = $select->where("product.is_promotion =?", $params["is_promotion"]);
        }
        if (empty($params["updated_date"]) == false && empty($params["condition"]) == false) {
            $select = $select->where("product.updated_date " . $params["condition"] . " ? ", $params["updated_date"]);
        }
        if (empty($params["type_of_category"]) == false ) {
            $select = $select->where("c.type_of_category =? ", $params["type_of_category"]);
        }
        if (empty($params["show_list_product_home"]) == false && $params["show_list_product_home"] == 1) {
            $select = $select->where("c.show_list_product_home_page =?", $params["show_list_product_home"]);
        }
    	if (empty($params["show_in_category_home_page"]) == false && $params["show_in_category_home_page"] == 1) {
            $select = $select->where("show_in_category_home_page =?", $params["show_in_category_home_page"]);
        }
        if (empty($params["limit"]) == false) {
            $select = $select->limit($params["limit"]);
        }
        
        if (empty($params["order_category"]) == false) {
        	$select = $select->order("c_priority ASC");
        } else {
            if (empty($params["order"]) == false && $params["order_type"]) {
                $select = $select->order("product." . $params["order"] . " " . $params["order_type"]);
            } else {
                $select = $select->order("product.priority desc");
            }
        }
        //get only active product
        $select = $select->where("product.status <> ?", STATUS_DELETE);
        $select = $select->where("product.status <> ?", STATUS_IN_ACTIVE);
        //
        $result = $this->getAdapter()->fetchAll($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }


    /**
     * 
     * @param type $id
     * @return type
     */
    public function getProductInfoById($id) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name);
        $commonObj = new My_Controller_Action_Helper_Common();
        $id = $commonObj->quoteLike($id);
        $select = $select->where("id =?", $id);
        $select = $select->where("status <>?", STATUS_DELETE);
        $select = $select->where("product.status <> ?", STATUS_IN_ACTIVE);
        $result = $this->getAdapter()->fetchRow($select);
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
    public function search($key,$params = array()) {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name)
                ->columns(array('product.created_date' => new Zend_Db_Expr("DATE_FORMAT(product.created_date,'%Y-%m-%d %H:%i:%s')")))
                ->columns(array('product.updated_date' => new Zend_Db_Expr("DATE_FORMAT(product.updated_date,'%Y-%m-%d %H:%i:%s')")));
        $select = $select->joinLeft(array('c' => 'category'), 'c.id = product.id_category', array('category_name' => 'c.name'));
        //get only active product
        $select = $select->where("product.status <> ?", STATUS_DELETE);
        $select = $select->where("product.status <> ?", STATUS_IN_ACTIVE);
        if (empty($key) == false) {
            $key =str_replace(' ','%',(string)$key);
            $select->where('upper( product.title ) LIKE upper(?) or upper( product.description ) LIKE upper(?) or upper( product.content ) LIKE upper(?) or upper( c.name ) LIKE upper(?)', '%' . $key . '%');
            $case = new Zend_Db_Expr($this->getAdapter()->quoteInto('case when upper( product.title ) LIKE upper(?) then 1
            when upper( product.description ) LIKE upper(?) then 2 
            when upper( c.name ) LIKE upper(?) then 3 
            else 4 end', '%' . $key . '%'));
            $select = $select->order($case);
        }
        //
        if (empty($params["minRange"]) == false && is_numeric($params["minRange"]) == true) {
            $select = $select->where("price_sales >=?", $params["minRange"]);
        }
        if (empty($params["maxRange"]) == false && is_numeric($params["maxRange"]) == true) {
            $select = $select->where("price_sales <=?", $params["maxRange"]);
        }
        if (empty($params["sort"]) == false) {
            $select = $select->order($params["sort"]);
        } else {
            $select = $select->order("priority desc");
        }
        $result = $this->getAdapter()->fetchAll($select);
        if (empty($result) == true) {
            return array();
        }
        return $result;
    }
    /**
     * 
     * @return int
     */
    public function getMaxProductPrice() {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name, array(new Zend_Db_Expr('max(price_sales) as max_price')));
        $select = $select->where("status <>?", STATUS_DELETE);
        $select = $select->where("product.status <> ?", STATUS_IN_ACTIVE);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result) == true) {
            return 0;
        }
        return $result;
    }
    /**
     * 
     * @return int
     */
    public function getMinProductPrice() {
        $select = $this->getAdapter()->select();
        $select = $select->from($this->_name, array(new Zend_Db_Expr('min(price_sales) as min_price')));
        $select = $select->where("status <>?", STATUS_DELETE);
        $select = $select->where("product.status <> ?", STATUS_IN_ACTIVE);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result) == true) {
            return 0;
        }
        return $result;
    }
    
    public function updateProduct( $data, $id ) {
    	$where[] = $this->getAdapter()->quoteInto("id = ?", $id, Zend_Db::INT_TYPE);
    	return $this->update($data, $where);
    }

    public function getProductByComboId($combo_id) {
        $select = $this->getAdapter()->select()
            ->from(array('p' => 'product'))
            ->where('combo_id= ?', $combo_id)
            ->order('id ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    // public function updateProductById($data, $product_id) {
    //     if (!empty($product_id)) {
    //         $where = $this->getAdapter()->quoteInto("product_id = ?", $product_id, Zend_Db::INT_TYPE);
    //         return $this->update($data, $where);
    //     }
    //     return false;
    // }
}
