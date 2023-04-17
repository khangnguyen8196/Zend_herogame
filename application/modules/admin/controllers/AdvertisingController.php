<?php
/**
 * Advertising management 
 * created by @TinPham
 */
class Site_AdvertisingController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs(array('advertising'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        
    }
    /**
     * Search page
     */
    public function listAction() {
       	$this->isAjax();
    	$draw = $this->post_data['draw']; // 
    	$model = new Advertising();
    	//define columns
    	$columns = array( // 
    			0 => "advertising_id",
                1 => "title",
    			2 => "url",
    			3 => "position",
    			4 => "media_id",
    			5 => "created_at",
    			6 => "updated_at",
                7 => 'created_by',
                8 => 'updated_by',
                9 => 'status'
    	);

    	//order function
    	if ( empty( $this->post_data["order"] ) == false ) {
    		$this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
    		$this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
    	} else {
    		$this->post_data["order"]["column"] = "updated_at";
    		$this->post_data["order"]["dir"] = "desc";
    	}
    	//search function
    	if ( empty( $this->post_data["columns"] ) == false && is_array( $this->post_data["columns"] ) ) {
    		foreach ( $this->post_data["columns"] as $column ) {
    			if ( $column["searchable"] == true && empty( $column["search"] ) == false && $column["search"]["value"] != "" ) {
    				$this->post_data[$column["data"]] = $column["search"]["value"];
    			}
    		}
    	}
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $model->fetchAllAdvertising( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$list = $model->fetchAllAdvertising( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $list;
    	$this->_helper->json( $return );
    	exit;
    }

    /**
     * detail page
     */
    public function detailAction(){
    	$models = new Advertising();
    	$info = array();
    	$error = array();
    	$id = 0;
    	$data = $this->post_data;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = intval($this->post_data ['id']);
    		$info = $models->fetchAdvertisingById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    		$xml = APPLICATION_PATH.'/xml/advertising.xml';
    		$error = $this->checkInputData( $xml, $this->post_data);
    		if( empty($error) == true ){
    			$dataIn = array(
    					'media_id' => $data['media_id'],
    					'title' => $data['title'],
    					'url' => Commons::url_slug($this->post_data['url']),
    					'position' => $data['position'],
    					'external_url' => $data['external_url'],
    					'status' => STATUS_ACTIVE
    			);
    			if( $id > 0 ){ // update
    				$dataUpdated = $this->getUpdated();
    				$data_in = array_merge( $dataIn, $dataUpdated);
    			} else { // create
    				$dataCreated = $this->getCreated();
    				$data_in = array_merge( $dataIn, $dataCreated);
    			}
    			$models->saveAdvertising( $data_in, $id);
    			$this->_redirect('/'.$this->controller );
    		}
    	}
    	if( $id > 0 && empty( $info['media_id'] ) == false && intval( $info['media_id'] ) > 0 ){
    		$mdlMedia = new Media();
    		$mediaInfo = $mdlMedia->fetchMediaById( $info['media_id'] );
    		if( empty( $mediaInfo ) == false ){
    			$this->view->mediaInfo = $mediaInfo;
    		}
    	}
    	$this->view->info = $info;
    	$this->view->id = $id;
    	$this->view->error = $error;
    }
    /**
     * delete
     */
    public function deleteAction() {
    	$this->isAjax();
    	if (empty($this->post_data['id']) == false) {
    		$modal = new Advertising();
    		$reponse = $modal->deleteAdvertising($this->post_data['id']);
    		if ($reponse >= 0) {
    			$this->ajaxResponse(CODE_SUCCESS);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
    /**
     * get postion ads of page
     */
    public function getListPositionAction(){
    	$this->isAjax();
    	if( empty( $this->post_data['page'] ) == false ){
    		$listPos = Commons::positionAdvertising();
    		if( array_key_exists( $this->post_data['page'], $listPos ) ){
    			$this->view->listPos = $listPos[$this->post_data['page']];
    			$this->loadTemplate( '/advertising/_list-pos.phtml');
    		} else {
    			$this->ajaxResponse(CODE_HAS_ERROR);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
}