
<?php
class Admin_PostController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/libs/ckeditor/ckeditor.js', 'text/javascript'));
        $this->loadJs('post');
    }

    /**
     * Search page
     */
    public function indexAction() {
    }
	public function detailAction(){
		$modelCategory = new Category();
		$model = new Post();
		$info = array();
		$error = array();
		$params["type_of_category"] = CATEGORY_TYPE_POST;
		$listCategory = $modelCategory->listAllCategory( $params );
		//
		$id = 0;
		// get post card information if there is postcard'id available
		if( empty( $this->post_data ['id'] ) == false ) {
			$id = intval(@$this->post_data ['id']);
			if( $id > 0 ){
				$info = $model->fetchPostById( $id );
				if( empty( $info ) == true ) {
					$this->_redirect( '/admin/'.$this->controller);
				}
			} else {
				$this->_redirect( '/admin/'.$this->controller);
			}
		}
		if( $this->request->isPost() ) {
			$data = $this->post_data;
			$xml = APPLICATION_PATH.'/xml/post.xml';
			$error = $this->checkInputData( $xml, $data);
			if( empty($error) == true ){
				if( is_numeric($data['priority']) == false){
					$data['priority'] = 0;
				}
				//
				$data['url_name'] = Commons::url_slug($data['url_name']);
				$check = $model->checkExistPostUrl($data['url_name'],$id);
				if( empty($check) == false ){
					$error[] = UtilTranslator::translate('exists-post-url-name');
				}
			}
			// add data vi first
			if( empty($error) == true  ){
				if( $id == 0 ){
					$idAdd  = $this->addPost($data);
					//
					if( $idAdd > 0 ){
						$this->_redirect( '/admin/'.$this->controller);
					} else {
						$error = UtilTranslator::translate('add-post-fail');
					}
				} else {
					$idUp  = $this->updatePost($data, $id);
					if( $idUp >=0 ){
						$this->_redirect( '/admin/'.$this->controller);
					} else {
						$error = UtilTranslator::translate('update-post-fail');
					}
				}
				
			}
			$info = $data;
		}
		$this->view->listCategory = $listCategory;
		$this->view->info = $info;
		$this->view->error = $error;
		$this->view->id = $id;
	}
	private function updatePost($data, $id){
		if( empty($data['status']) == true){
			$data['status'] = STATUS_IN_ACTIVE;
		}
		$dataUpdated = $this->getUpdated();
		$model = new Post();
		$data = array_merge($data, $dataUpdated);
		
		if( empty($_FILES['image_id']['name']) == false ){
			$public_path = UPLOAD_PATH;
			$nowdir = 'img_'.date('d_m_Y');
			$ext = pathinfo($_FILES['image_id']['name'], PATHINFO_EXTENSION);
			$newname = rand(0,1000000).'_'.uniqid('', true).'.'.$ext;
			Commons::makedirs($public_path.'/images/'.$nowdir);
			if( move_uploaded_file($_FILES["image_id"]["tmp_name"], $public_path.'/images/'.$nowdir.'/' . $newname)){
				if( empty($data['image_id']) == false ){
					$full = $public_path.'/images/'.$data['image_id'];
					if ( file_exists($full) ) {
						unlink($full);
					}
				}
				$data['image_id']= $nowdir.'/'.$newname;
			}
		}
		$rs = $model->savePost($data, $id );
		return $rs;
	}
	private function addPost($data){
		if( empty($data['status']) == true){
			$data['status'] = STATUS_IN_ACTIVE;
		}
		$model = new Post();
		$dataCreated = $this->getCreated();
		$data = array_merge($data, $dataCreated);
		
		$dataUpdate = $this->getUpdated();
		$data = array_merge($data, $dataUpdate);
		// upload path
		if( empty($_FILES['image_id']['name']) == false ){
			$public_path = UPLOAD_PATH;
			$nowdir = 'img_'.date('d_m_Y');
			$ext = pathinfo($_FILES['image_id']['name'], PATHINFO_EXTENSION);
			$newname = rand(0,1000000).'_'.uniqid('', true).'.'.$ext;
			Commons::makedirs($public_path.'/images/'.$nowdir);
			if( move_uploaded_file($_FILES["image_id"]["tmp_name"], $public_path.'/images/'.$nowdir.'/' . $newname)){
				$data['image_id']= $nowdir.'/'.$newname;
			}
		}
		$id = $model->savePost($data);
		return $id;
	}
	public function mediaAction(){
		$this->_helper->layout->disableLayout( true );
		$this->loadJs('post');
		$mdlMedia = new Media();
		$data = array();
		$data['length'] = MAX_ITEM_IMAGE;
		$list = $mdlMedia->fetchAllMedia( $data );
		$this->view->listMedia = $list;
		$this->view->functionNum = $this->post_data["CKEditorFuncNum"];
	}
    /**
     * Search page
     */
    public function listAction() {
       	$this->isAjax();
    	$draw = $this->post_data['draw']; // 
    	$model = new Post();
    	//define columns
    	$columns = array( // 
    			0 => "post_id",
                1 => "title",
    			2=> "url_name",
    			3 => "created_at",
                4 => 'created_by',
    			5 => "updated_at",
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
    	if( empty($this->post_data['category']) == false ){
    		$this->post_data['category_id'] = $this->post_data['category'];
    	}
    	if( empty($this->post_data['search']) == false && empty($this->post_data['search']['value']) == false ){
    		$this->post_data['search-key'] = $this->post_data['search']['value'];
    	}
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $model->fetchAllPost( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    	$list = $model->fetchAllPost( $this->post_data );
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
    		$modal = new Post();
    		$id = intval($this->post_data['id']);
    		if( $id > 0 ){
    			$reponse = $modal->deletePost($id);
    			if ($reponse >= 0) {
    				$this->ajaxResponse(CODE_SUCCESS);
    			}
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
    /**
     * get list post by ajax
     */
    public function getListPostAction() {
    	$this->isAjax();
    
    	$categoryRearrange = array();
    	$categoryMdl = new Category();
    	$params["type_of_category"] = CATEGORY_TYPE_POST;
    	$listCategory = $categoryMdl->listAllCategory( $params );
    
    	if (empty($listCategory) == false) {
    		foreach ($listCategory as $value) {
    			$categoryRearrange[$value["id"]] = $value["name"];
    		}
    	}
    	$postMdl = new Post();
    	$postList = $postMdl->fetchAllPost();
    	$postMapping = array();
    	if (empty($postList) == false && empty($categoryRearrange) == false) {
    		foreach ($postList as $value) {
    			if (empty($this->post_data["id"]) == false && $this->post_data["id"] == $value["id"]) {
    				continue;
    			}
    			$postMapping[$categoryRearrange[$value["id_category"]]][] = $value;
    		}
    	}
    	$selectedRelativePost = array();
    	if( empty($this->post_data["selectRelativePost"]) == false){
    		$selectedRelativePost = explode(',', $this->post_data["selectRelativePost"]);
    	}
    	$this->view->selectedRelativePost = $selectedRelativePost;
    	$this->view->postMapping = $postMapping;
    	$html = $this->view->render("/post/_relative-post.phtml");
    	$this->ajaxResponse(CODE_SUCCESS, '', $html);
    }
}
