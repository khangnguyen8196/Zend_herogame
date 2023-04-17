<?php
/**
 * Gallery
 */
class Admin_GalleryController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs('gallery');
    }

    /**
     * Search page
     */
    public function indexAction() {
    	$menu = 9;// gallery
    	$categoryModel = new Category();
    	$listCategory = $categoryModel->listAllCategoryByMenu($menu);
    	$this->view->category = $listCategory;
    }
    /**
     * phan hoac ra 2 list vi va en
     * neu la add ( id == 0)
     * check input data vi neu co en thi check en neu xay ra loi thi tra ve
     * neu add data success thi lay idAdd ra
     */
	public function detailAction(){
		$menu = 9;// gallery
		$categoryModel = new Category();
		$listCategory = $categoryModel->listAllCategoryByMenu($menu);
		$this->view->category = $listCategory;
		$language = new Language();
		$listLanguage = $language->listAllLanguage();
		$this->view->listlanguage = $listLanguage;
		$model = new Gallery();
		$info = array('vi' => array(),'en' => array());
		$error = array('vi' => array(),'en' => array());
		$dataVi = array();
		$dataEn = array();
		//
		$id = 0;
		$parentGalleryId = 0;
		// get post card information if there is postcard'id available
		if( empty( $this->post_data ['id'] ) == false ) {
			$id = intval($this->post_data ['id']);
			if( $id > 0 ){
				$tempInfo = $model->fetchGalleryById($id );
				if( empty( $info ) == true ) {
					$this->_redirect( '/'.$this->controller);
				} else {
					$parentGalleryId = intval(@$tempInfo['gallery_parent_id']);
					if( $parentGalleryId > 0 ){
						if( @$tempInfo['language'] == 2 ){
							$info['en'] = $tempInfo;
							$info['vi'] = $model->fetchGalleryByParentId( $parentGalleryId );
						} else {
							$info['vi'] = $tempInfo;
							$info['en'] = $model->fetchGalleryByParentId( @$tempInfo['gallery_parent_id'], 2 );
						}
					} else {
						$this->_redirect( '/'.$this->controller);
					}
				}
			} else {
				$this->_redirect( '/'.$this->controller);
			}
			
		}
		$dataVi = $info['vi'];
		$dataEn = $info['en'];
		if( $this->request->isPost() ) {
			$data = $this->post_data;
			// prepaid data
			foreach ($data as $key => $value){
				if( strpos($key, '_vi') !== false){
					$key = str_replace('_vi','',$key);
					$dataVi[$key] = $value;
				}else if( strpos($key, '_en') !== false){
					$key = str_replace('_en','',$key);
					$dataEn[$key] = $value;
				}
			}
			$xml = APPLICATION_PATH.'/xml/gallery.xml';
			$error['en'] = $this->checkInputData( $xml, $dataEn);
			if( empty($error['en']) == true ){
				// check number
				if( is_numeric($dataVi['priority']) == false){
					$dataEn['priority'] = 0;
				}
				//
				$dataEn['url_name'] = Commons::url_slug($dataEn['url_name']);
				$check = $model->checkExistGalleryUrl($dataEn['url_name'],$parentGalleryId);
				if( empty($check) == false ){
					$error['en'][] = UtilTranslator::translate('exists-gallery-url-name');
				}
				if( empty($data['media_id']) == false ){
					$dataEn['list_image'] = implode(',',$data['media_id']);
				}
				
			}
			// check input data en
			if( empty($dataVi) == false && $dataVi['title']!=''){
				$dataVi['category_id'] = $dataEn['category_id'];
				$dataVi['list_image'] = @$dataEn['list_image'];
				$error['vi'] = $this->checkInputData( $xml, $dataVi);
				if( empty($error['vi']) == true ){
					if( is_numeric($dataVi['priority']) == false){
						$dataVi['priority'] = 0;
					}
					$dataVi['url_name'] = Commons::url_slug($dataVi['url_name']);
					$check = $model->checkExistGalleryUrl($dataVi['url_name'],$parentGalleryId );
					if( empty($check) == false ){
						$error['vi'][] = UtilTranslator::translate('exists-gallery-url-name');
					}
				}
			}
			// add data vi first
			if( empty($error['en']) == true && empty($error['vi']) == true ){
				if( $id == 0 ){
					$dataEn['language'] = 2;
					$idAdd  = $this->addGallery($dataEn, 'en');
					//
					if( $idAdd > 0 ){
						if( empty($dataVi ) == false && $dataVi['title']!=''){
							$dataVi['language'] = 1;
							$dataVi['gallery_parent_id'] = $idAdd;
							$this->addGallery($dataVi, 'vi');
						}
						$this->_redirect( '/'.$this->controller);
					} else {
						$error['vi'] = UtilTranslator::translate('add-gallery-fail');
					}
				} else {
					$idUp  = $this->updateGallery($dataEn, 'en', $parentGalleryId);
					if( $idUp >=0 ){
						if( empty($dataVi ) == false && $dataVi['title']!=''){
							if( empty($info['vi']) == false ){
								$this->updateGallery($dataVi, 'vi', $info['vi']['gallery_id']);
							} else {
								$dataVi['gallery_parent_id'] = $id;
								$this->addGallery($dataVi, 'vi');
							}
						}
						$this->_redirect( '/'.$this->controller);
					} else {
						$error['vi'] = UtilTranslator::translate('update-gallery-fail');
					}
				}
			}
			$info['vi'] = $dataVi;
			$info['en'] = $dataEn;
		}
		if( empty($dataEn['list_image']) == false ){
			$listImg = explode(',',$dataEn['list_image']);
			$mediaModel = new Media();
			$listFullImg = $mediaModel->getListImage($listImg);
			$this->view->listImg = $listFullImg;
		}
		$this->view->info = $info;
		$this->view->error = $error;
		$this->view->id = $id;
	}
	private function updateGallery($data,$lang, $id){
		unset($data['url']);
		unset($data['url_thumnail']);
		if( empty($data['status']) == true){
			$data['status'] = STATUS_IN_ACTIVE;
		}
		$dataUpdated = $this->getUpdated();
		$model = new Gallery();
		$data = array_merge($data, $dataUpdated);
		$rs = $model->saveGallery($data, $id );
		if( $rs >=0 ){
			if( empty($_FILES['main_img_id_'.$lang]['name']) == false){
				$public_path = UPLOAD_PATH;
				$detailImg = Commons::getWidthHeightImg( 'main_img_id'.$lang );
				$upload_img = Commons::cwUpload('main_img_id_'.$lang,$public_path.'/images/full/','',TRUE,$public_path.'/images/thumnail/',$detailImg['width'], $detailImg['height']);
				$modelMedia = new Media();
				$dataImg['url'] = '/full/'.$upload_img;
				$dataImg['url_thumnail'] = '/thumnail/'.$upload_img;
				$dataImg['status'] = STATUS_ACTIVE;
				$dataImg['type'] = IMG_TYPE;
				if( empty($data['main_img_id']) == false ){
					// img info 
					$mediaInfo = $modelMedia->fetchMediaById($data['main_img_id']);
					if( empty($mediaInfo) == false ){
						$full = $public_path.'/images'.$mediaInfo['url'];
						$thumb = $public_path.'/images'.$mediaInfo['url_thumnail'];
						if ( file_exists($full) ) {
							unlink($full);
						}
						if ( file_exists($thumb) ) {
							unlink($thumb);
						}
					}
					$dataImg = array_merge($dataImg, $dataUpdated);
					$idImg = $modelMedia->saveMedia($dataImg, $data['main_img_id']);
					
				} else {
					$dataCreated= $this->getCreated();
					$dataImg = array_merge($dataImg, $dataCreated);
					$idImg = $modelMedia->saveMedia($dataImg);
					if( $idImg > 0 ){
						$dataUp['main_img_id'] = $idImg;
						$model->saveGallery($dataUp, $id);
					}
				}
			}
		}
		
		return $rs;
	}
	private function addGallery($data,$lang){
		$model = new Gallery();
		if( empty($data['status']) == true){
			$data['status'] = STATUS_IN_ACTIVE;
		}
		$dataCreated = $this->getCreated();
		$data = array_merge($data, $dataCreated);
		$dataUpdate = $this->getUpdated();
		$data = array_merge($data, $dataUpdate);
		$id = $model->saveGallery($data);
		if( $id > 0 ){
			// update post parent id if it is vi
			if( $lang == 'en'){
				$dataUpdate = array('gallery_parent_id' => $id);
				$model->saveGallery($dataUpdate,$id);
			}
			//
			if( empty($_FILES['main_img_id_'.$lang]['name']) == false){
				$public_path = UPLOAD_PATH;
				$detailImg = Commons::getWidthHeightImg( 'main_img_id_'.$lang );
				$upload_img = Commons::cwUpload('main_img_id_'.$lang,$public_path.'/images/full/','',TRUE,$public_path.'/images/thumnail/',$detailImg['width'], $detailImg['height']);
				$modelMedia = new Media();
				$dataImg['url'] = '/full/'.$upload_img;
				$dataImg['url_thumnail'] = '/thumnail/'.$upload_img;
				$dataImg['status'] = STATUS_ACTIVE;
				$dataImg['type'] = IMG_TYPE;
				$dataImg = array_merge($dataImg, $dataCreated);
				$idImg = $modelMedia->saveMedia($dataImg);
				if( $idImg > 0 ){
					$dataUp['main_img_id'] = $idImg;
					$model->saveGallery($dataUp, $id);
				}
			}
		}
		return $id;
	}
	public function mediaAction(){
		$this->_helper->layout->disableLayout( true );
		$this->loadJs('post');
		$mdlMedia = new Media();
		$list = $mdlMedia->fetchAllMedia();
		$this->view->listMedia = $list;
		$this->view->functionNum = $this->post_data["CKEditorFuncNum"];
	}
	public function uploadAction(){
		$public_path = BASE_PATH. '/public';
		if(!empty($_FILES['upload']['name'])){
			//call thumbnail creation function and store thumbnail name
			$upload_img = Commons::cwUpload('upload',$public_path.'/upload/images/full/','',TRUE,$public_path.'/upload/images/thumnail/','200','160');
		
			//full path of the thumbnail image
			$thumb_src = 'uploads/images/thumnail/'.$upload_img;
		
			//set success and error messages
			echo $thumb_src;
		}else{
		
			//if form is not submitted, below variable should be blank
			$thumb_src = '';
			$message = '';
		}
		exit;
	}
    /**
     * Search page
     */
    public function listAction() {
       	$this->isAjax();
    	$draw = $this->post_data['draw']; // 
    	$model = new Gallery();
    	//define columns
    	$columns = array( // 
    			0 => "gallery_id",
                1 => "title",
    			2=> "url_name",
    			3=> "category_id",
    			4 => "created_at",
                5 => 'created_by',
    			6 => "updated_at",
                7 => 'updated_by',
                8 => 'status',
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
    	if( empty($this->post_data['category']) == false ){
    		$this->post_data['category_id'] = $this->post_data['category'];
    	}
    	if( empty($this->post_data['search']) == false && empty($this->post_data['search']['value']) == false ){
    		$this->post_data['search-key'] = $this->post_data['search']['value'];
    	}
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $model->fetchAllGallery( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    	$list = $model->fetchAllGallery( $this->post_data );
    	$response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json( $this->returnResponseDatatable( $response ) );
    	exit;
    }
    /**
     * 
     */
    public function deleteAction(){
    	$this->isAjax();
    	if (empty($this->post_data['id']) == false) {
    		$id = intval($this->post_data['id']);
    		if( $id > 0 ){
    			$modal = new Gallery();
    			$info = $modal->fetchGalleryById( $id );
    			if( empty($info) == false ){
    				$deleteAll = false;
    				$idDelete = $id;
    				if( $info['language'] == 2 ){
    					// xoa tieng anh thi delete het cac bai lien quan
    					$deleteAll = true;
    					$idDelete = @$info['gallery_parent_id'];
    				}
    				$reponse = $modal->deleteGallery($idDelete, $deleteAll);
    				if ($reponse >= 0) {
    					$this->ajaxResponse(CODE_SUCCESS);
    				}
    			}
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
}
