<?php
/**
 * Media management 
 * created by @TinPham
 */
class Admin_MediaController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs(array('media'));
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
    	$model = new Media();
    	//define columns
    	$columns = array( // 
    			0 => "media_id",
                1 => "url_thumnail",
    			2 => "type",
    			3 => "created_at",
    			4 => "updated_at",
                5 => 'created_by',
                6 => 'updated_by',
                7 => 'status',
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
    	$count = $model->fetchAllMedia( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$list = $model->fetchAllMedia( $this->post_data );
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
    	$models = new Media();
    	$info = array();
    	$error = array();
    	$id = 0;
    	$data = $this->post_data;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = intval($this->post_data ['id']);
    		$info = $models->fetchMediaById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->module.'/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
			$error = array();
    		if( empty($error) == true ){
    			$result = array();
    			if( $id > 0 ){
    				$result = $this->updateMedia( $data, $info );
    				if( $result['code'] > 0) {
    					$this->_redirect('/'.$this->module.'/'.$this->controller);
    				} else {
    					// add failed
    					$error[] = @$result['error'];
    					$info = $data;
    				}
    			} else {
    				if(empty($_FILES['url']['name']) == false ){
    					$result = $this->addMedia( $data );
    					if( $result['code'] > 0) {
    						$this->_redirect('/'.$this->module.'/'.$this->controller);
    					} else {
    						// add faile
    						$error[] = @$result['error'];
    						$info = $data;
    					}
    				} else {
    					$info = $data;
    					$error[] = 'Vui lòng tải lên hình ảnh';
    				}
    			}
    		} else {
    			$info = $this->post_data;
    		}
    	}
    	$this->view->info = $info;
    	$this->view->id = $id;
    	$this->view->error = $error;
    }
    private function addMedia( $data ){
    	$result = array('code' => -1, 'fileName' =>'', 'error' =>'');
    	$checkOk = true;
    	$id = 0;
    	$currentMonth = date('m_Y');
		$dataIn = array();    	
    	if( empty($_FILES['url']['name']) == false ){
			$detailImg = Commons::getWidthHeightImg( 'url' );
    		$public_path = UPLOAD_PATH;
    		$upload_img = Commons::cwUpload('url',$public_path.'/images/full/','',FALSE,$public_path.'/images/thumnail/', '400', '300');
    		$dataIn['url'] = '/full/'.$upload_img;
    		$dataIn['url_thumnail'] = '/full/'.$upload_img;
    	}
    	if( $checkOk == true ){
    		$mdlMedia = new Media();
    		$dataIn['type'] = $data['type'];
    		$dataIn['title'] = $data['title'];
    		$dataIn['status'] = STATUS_ACTIVE;
    		$dataCreated = $this->getCreated();
    		$data_in = array_merge( $dataIn, $dataCreated);
    		 
    		$rs = $mdlMedia->saveMedia( $data_in , $id );
    		if( $rs >= 0 ){
    			$result['code'] = 1;
    		} else {
    			$result['error'] = 'Upload ảnh thất bại';
    		}
    	}
    	return $result;
    }
    private function updateMedia( $data, $info ){
    	$result = array('code' => -1, 'fileName' =>'', 'error' =>'');
    	$checkOk = true;
    	$currentMonth = date('m_Y');
		$dataIn = array();    	
    	if( empty($_FILES['url']['name']) == false ){
    		$public_path = UPLOAD_PATH;
    		$upload_img = Commons::cwUpload('url',$public_path.'/images/full/','',FALSE,$public_path.'/images/thumnail/', '400', '300');
    		$dataIn['url'] = '/full/'.$upload_img;
    	}
    	if( $checkOk == true ){
    		$mdlMedia = new Media();
    		$dataIn['type'] = $data['type'];
    		$dataIn['title'] = $data['title'];
    		$dataIn['status'] = STATUS_ACTIVE;
    		$dataUpdated = $this->getUpdated();
    		$data_in = array_merge( $dataIn, $dataUpdated );
    		 
    		$rs = $mdlMedia->saveMedia( $data_in, $info['media_id'] );
    		if( $rs >= 0 ){
    			$result['code'] = 1;
    			if( empty($_FILES['url']['name']) == false ){
    				$public_path = UPLOAD_PATH;
    				$realUrlPath = $public_path.$info['url'];
    				if ( file_exists( $realUrlPath ) ) {
    					unlink( $realUrlPath );
    				}
    			}
    		} else {
    			$result['error'] = 'Upload ảnh thất bại';
    		}
    	}
    	return $result;
    }
    
    /**
     * delete
     */
    public function deleteAction() {
    	$this->isAjax();
    	$public_path = UPLOAD_PATH;
    	if (empty($this->post_data['id']) == false) {
    		$modal = new Media();
    		$info = $modal->fetchMediaById($this->post_data['id']);
    		if( empty($info) == false ){
    			$reponse = $modal->deleteMedia($this->post_data['id']);
    			if ($reponse >= 0) {
    				$full = $public_path.'/images'.$info['url'];
    				$thumb = $public_path.'/images'.$info['url_thumnail'];
    				if ( file_exists($full) ) {
    					unlink($full);
    				}
    				if ( file_exists($thumb) ) {
    					unlink($thumb);
    				}
    				$this->ajaxResponse(CODE_SUCCESS);
    			}
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
    public function getListMediaAction() {
		$this->isAjax();
		$mdlMedia = new Media();
		$data = array();
		$data['length'] = MAX_ITEM_IMAGE;
		$list = $mdlMedia->fetchAllMedia( $data );
		if (empty( $list ) == true) {
			$this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('media-empty'));
		}
		$this->view->listMedia = $list;
		$this->loadTemplate( '/media/_dialog-media.phtml');
    }
    
    
    public function uploadAction() {
    	$this->isAjax();
    	$data = $this->post_data;
    	if( empty( $_FILES['image']['name'] ) == false ){
    		$result = $this->addMediaDialog( $data );
    		$this->ajaxResponse( CODE_SUCCESS, '', $result);
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
    private function addMediaDialog( $data ){
    	$result = array('code' => -1, 'fileName' =>'', 'error' =>'', 'id' => '', 'info' => array() );
    	$checkOk = true;
    	$id = 0;
    	$currentMonth = date('m_Y');
    	$dataIn = array();
    	if( count($_FILES['image']['name']) > 0 ){
	    	for($i=0; $i<count($_FILES['image']['name']); $i++){
	    		$file_name =  Commons::_createFileName( $_FILES['image']['name'][$i] );
	    		$public_path = UPLOAD_PATH.'/images/full/'; //UPLOAD_PATH || PUBLIC_PATH
	    		$path = $public_path.$file_name;
	    		if( move_uploaded_file($_FILES["image"]["tmp_name"][$i], $path) ) {
		    		$dataIn['url'] = '/full/'.$file_name;
		    		$dataIn['url_thumnail'] = '/full/'.$file_name;
		    		if( $checkOk == true ){
		    			$mdlMedia = new Media();
		    			$dataIn['type'] = 1;
		    			$dataIn['title'] = '';
		    			$dataIn['status'] = STATUS_ACTIVE;
		    			$dataCreated = $this->getCreated();
		    			$data_in = array_merge( $dataIn, $dataCreated);
		    			$rs = $mdlMedia->saveMedia( $data_in, $id );
		    			if( $rs >= 0 ){
		    				$result['info'][] = $mdlMedia->fetchMediaById( $rs );
		    				$result['id'] = $rs;
		    				$result['code'] = 1;
		    			} else {
		    				$result['error'] = 'Upload ảnh thất bại';
		    			}
		    		}
	    		}
	    	}
    	}
    	return $result;
    }
	public function getMoreMediaAction() {
		$this->isAjax();
		$postData = $this->post_data;
		$dataIn = array();
		$isNext = false;
		$mdlMedia = new Media();
		$dataIn['limit'] = MAX_ITEM_IMAGE + 1;
		$dataIn['start'] = @$postData['start'];
		$list = $mdlMedia->fetchMoreMedia( $dataIn );
		$this->ajaxResponse( CODE_SUCCESS, '', $list );
    }
}