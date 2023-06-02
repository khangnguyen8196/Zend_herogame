<?php
/**
 * Setting management 
 * created by @Phuong Nguyen
 */
class Admin_ProductVariantController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs(array('product-variant'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $model = new ProductVariant();
        $this->view->list = $model->fetchAllVariant();
    }

    /**
     * detail page
     */
    public function detailAction(){
        $models = new ProductVariant();
    	$info = array();
    	$error = array();
    	$data = $this->post_data;
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    	    $id = intval($this->post_data ['id']);
    	    $info = $models->fetchVariantById( $id );
    	    if( empty( $info ) == true ) {
    	        $this->_redirect( '/'.$this->module.'/'.$this->controller );
    	    }
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    	    if( empty($data['variant_name']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập Tên Loại ';
    	    }
			if( empty($data['variant_price']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập Giá Sản Phẩm ';
    	    }
			if( empty($data['variant_price_sales']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập Giá Sales Sản Phẩm ';
    	    }
			if( empty($data['product_id']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập product ID ';
    	    }
    		if( empty($error) == true ){
    		    $dataInsert['variant_name'] = $data['variant_name'];
    		    $dataInsert['variant_price'] = $data['variant_price'];
    		    $dataInsert['variant_price_sales'] = $data['variant_price_sales'];
    		    $dataInsert['product_id'] = $data['product_id'];
    		    $dataInsert['status'] = STATUS_ACTIVE;
    		    $models->saveVariant( $dataInsert, $id );
    			$this->_redirect('/admin/'.$this->controller );
    		} else {
    			$info = $this->post_data;
    		}
    	}
    	$this->view->id = $id; 
    	$this->view->info = $info;
    	$this->view->error = $error;
    }
    /**
     * delete
     */
    public function deleteAction() {
    	$this->isAjax();
    	if (empty($this->post_data['id']) == false) {
    	    $modal = new ProductVariant();
    	    $reponse = $modal->deleteVariant($this->post_data['id']);
    		if ($reponse >= 0) {
    			$this->ajaxResponse(CODE_SUCCESS);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
}