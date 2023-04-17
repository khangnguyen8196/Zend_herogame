<?php
/**
 * Setting management 
 * created by @Phuong Nguyen
 */
class Admin_ProductColorController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs(array('product-color'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $model = new ProductColor();
        $this->view->list = $model->fetchAllColor();
    }

    /**
     * detail page
     */
    public function detailAction(){
        $models = new ProductColor();
    	$info = array();
    	$error = array();
    	$data = $this->post_data;
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    	    $id = intval($this->post_data ['id']);
    	    $info = $models->fetchColorById( $id );
    	    if( empty( $info ) == true ) {
    	        $this->_redirect( '/'.$this->module.'/'.$this->controller );
    	    }
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    	    if( empty($data['color_name']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập Tên Màu';
    	    }
    		if( empty($error) == true ){
    		    $dataInsert['color_name'] = $data['color_name'];
    		    $dataInsert['status'] = STATUS_ACTIVE;
    		    $models->saveColor( $dataInsert, $id );
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
    	    $modal = new ProductColor();
    	    $reponse = $modal->deleteColor($this->post_data['id']);
    		if ($reponse >= 0) {
    			$this->ajaxResponse(CODE_SUCCESS);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
}