<?php

/**
 * Process for Category
 */
class Gallery extends Zend_Db_Table_Abstract {

    protected $_name = 'gallery';
    protected $_rowClass = 'DbTableRow';

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllGallery($data = array()) {
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    		$select = $select->where( "gallery.status <> ?", STATUS_DELETE);
    	} else {
    		  $select = $select->from($this->_name)
              ->columns(array('gallery.created_at' => new Zend_Db_Expr("DATE_FORMAT(gallery.created_at,'%Y-%m-%d %H:%i:%s')")))
              ->columns(array('gallery.updated_at' => new Zend_Db_Expr("DATE_FORMAT(gallery.updated_at,'%Y-%m-%d %H:%i:%s')")));
    	}
    	$select = $select->joinLeft( 'category', 'category.category_id = gallery.category_id and category.status = '.STATUS_ACTIVE,array('category.category_name'));
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        $select = $select->where( "gallery.status <> ?", STATUS_DELETE);
//         $select = $select->where( "gallery.language = ?", 1);
        if (empty($data["title"]) == false) {
            $data["title"] = $commonObj->quoteLike($data["title"]);
            $select = $select->where("gallery.title like ?", "%" . $data["title"] . "%");
        }
        if( empty( $data["created_at"] ) == false ) {
        	$data["created_at"] = $commonObj->quoteLike( $data["created_at"] );
        	$select = $select->where( "DATE(gallery.created_at) =?", $data["created_at"] );
        }
        if( empty( $data["category_id"] ) == false ) {
        	$data["category_id"] = $commonObj->quoteLike( $data["category_id"] );
        	$select = $select->where( "gallery.category_id =?", $data["category_id"] );
        }
        if( empty( $data["updated_at"] ) == false ) {
        	$data["updated_at"] = $commonObj->quoteLike( $data["updated_at"] );
        	$select = $select->where( "DATE(gallery.updated_at) =?", $data["updated_at"] );
        }
        if ( empty($data['search-key']) == false ){
        	$select->where( "gallery.title like '%".$data['search-key']."%' or gallery.created_by like '%".$data['search-key']."%' or gallery.created_by like '%".$data['search-key']."%'
        			 or gallery.updated_by like '%".$data['search-key']."%'");
        }
        //check count only purpose
        if( empty( $data['count_only'] ) == true || $data['count_only'] != 1 ) {
        	if ( empty( $data["order"] ) == false ) {
        		$order = $data["order"]["column"] . " " . $data["order"]["dir"];
        		$select = $select->order( $order );
        	}
        	$start = ( empty( $data['start'] ) == false ) ? $data['start'] : 0;
        	$length = ( empty( $data['length'] ) == false ) ? $data['length'] : 0;
        	$select = $select->limit( $length, $start );
        }
        $result = $this->getAdapter()->fetchAll( $select );
        if( empty( $data['count_only'] ) == false && $data['count_only'] == 1 ) {
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
    public function fetchGalleryById( $id ) {
    	$select     = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
    	$select = $select->joinLeft( 'media', 'media.media_id = gallery.main_img_id and media.status = '.STATUS_ACTIVE,array('media.url','media.url_thumnail'));
    	$commonObj = new My_Controller_Action_Helper_Common();
    	$id = $commonObj->quoteLike( $id );
    	$select = $select->where( "gallery.gallery_id =?", $id );
    	$select = $select->where( "gallery.status <>?", STATUS_DELETE );
    	$result =  $this->getAdapter()->fetchRow( $select );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	return $result;
    }
    public function fetchGalleryByParentId( $id, $lang = '' ) {
    	$select     = $this->getAdapter()->select();
    	$select = $select->from($this->_name);
    	$select = $select->joinLeft( 'media', 'media.media_id = gallery.main_img_id and media.status = '.STATUS_ACTIVE,array('media.url','media.url_thumnail'));
    	$commonObj = new My_Controller_Action_Helper_Common();
    	$id = $commonObj->quoteLike( $id );
    	$select = $select->where( "gallery.gallery_parent_id =?", $id );
    	if( $lang == ''){
    		$select = $select->where( "gallery.gallery_id <>?", $id );
    	} else {
    		$select = $select->where( "gallery.language = ?", $lang );
    		$select = $select->where( "gallery.gallery_id = ?", $id );
    	}
    	$select = $select->where( "gallery.status <>?", STATUS_DELETE );
    	$result =  $this->getAdapter()->fetchRow( $select );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	return $result;
    	
    	
    }
    /**
     * 
     * @param unknown $id
     * @return unknown
     */
    public function checkExistGalleryUrl( $url_name , $id ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "url_name = ?", $url_name);
    	$where[] = $db->quoteInto( "status <>  ?", STATUS_DELETE);
    	if( empty($id) == false  && $id > 0 ){
    		$where[] = $db->quoteInto( "gallery_parent_id <>?", $id, Zend_Db::INT_TYPE );
    	}
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
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
    public function saveGallery( $data, $id = 0) {
    	if ( empty( $id ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "gallery_id = ?", $id, Zend_Db::INT_TYPE );
    		$where[] = $this->getAdapter()->quoteInto( "status <> ?", STATUS_DELETE );
    		return $this->update( $data, $where );
    	} else {
    		return $this->insert( $data );
    	}
    }
	public function deleteGallery( $id , $deleteAll = false ){
		$data = array('status' => STATUS_DELETE);
// 		$where[] = $this->getAdapter()->quoteInto( "gallery_parent_id = ?", $id, Zend_Db::INT_TYPE );
// 		return $this->update( $data, $where );
		if( $deleteAll == true ){
			$where[] = $this->getAdapter()->quoteInto( "gallery_parent_id = ?", $id, Zend_Db::INT_TYPE );
		} else {
			$where[] = $this->getAdapter()->quoteInto( "gallery_id = ?", $id, Zend_Db::INT_TYPE );
		}
		return $this->update( $data, $where );
	}
}
