<?php
/**
 * Setting management 
 * created by @TinPham
 */
class Admin_SettingController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/libs/ckeditor/ckeditor.js', 'text/javascript'));
        $this->loadJs(array('setting'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $mdlSetting = new Setting();
        $list = $mdlSetting->fetchAllSetting();
        $this->view->list = $list;
    }

    /**
     * detail page
     */
    public function detailAction(){
    	$models = new Setting();
    	$info = array();
    	$error = array();
    	$data = $this->post_data;
    	$isEdit = false;
    	$key = '';
    	$flag = false;
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    		$xml = APPLICATION_PATH.'/xml/setting.xml';
    		$error = $this->checkInputData( $xml, $this->post_data);
    		if( empty( $data['isEdit'] ) == true ){
    			$check = $models->fetchSettingByKey( $data['key'] );
    			if( empty( $check ) == false ){
    				$flag = true;
    				$error['existed-key'] = $this->translate('existed-key');
    			}
    		}
    		if( empty($error) == true ){
    			$models->saveSetting( $data );
    			$this->_redirect('/admin/'.$this->controller );
    		} else {
    			$info = $this->post_data;
    		}
    	}
    	if( empty( $this->post_data ['key'] ) == false ) {
    		$key = $this->post_data ['key'];
    		$isEdit = true;
    		if( $flag == true ){
    			$isEdit = false;
    		} else {
	    		$info = $models->fetchSettingByKey( $key );
	    		if( empty( $info ) == true ) {
	    			$this->_redirect( '/admin/'.$this->controller );
	    		}
    		}
    	}
    	
    	$this->view->isEdit = $isEdit;
    	$this->view->info = $info;
    	$this->view->error = $error;
    }
    /**
     * delete
     */
    public function deleteAction() {
    	$this->isAjax();
    	if (empty($this->post_data['key']) == false) {
    		$modal = new Setting();
    		$reponse = $modal->deleteSetting($this->post_data['key']);
    		if ($reponse >= 0) {
    			$this->ajaxResponse(CODE_SUCCESS);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
}