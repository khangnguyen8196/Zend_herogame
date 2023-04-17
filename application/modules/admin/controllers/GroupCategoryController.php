<?php
/**
 * Category
 */
class Admin_GroupCategoryController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs('group-category');
    }

    /**
     * Search page
     */
    public function indexAction() {
    	
    }
	public function detailAction(){
		$models = new GroupCategory();
		$info = array();
		$error = array();
		$id = 0;
		// get post card information if there is postcard'id available
		if( empty( $this->post_data ['id'] ) == false ) {
			$id = intval($this->post_data ['id']);
			$info = $models->fetchGroupCategoryById( $id );
			if( empty( $info ) == true ) {
				$this->_redirect( '/admin/'.$this->controller );
			}
		}
		// check request is POST or GET
		if( $this->request->isPost() ) {
			$xml = APPLICATION_PATH.'/xml/groupcategory.xml';
			$this->post_data['url_slug'] = Commons::url_slug($this->post_data['url_slug']);
			$error = $this->checkInputData( $xml, $this->post_data);
			$check = $models->fetchGroupCateGoryByUrl($this->post_data['url_slug'],$id);
			if( empty($check) == false ){
				$error[] = 'Đường dẫn đã tồn tại';
			}
			if( empty($error) == true ){
				$data_in['name'] = $this->post_data['name'];
				$data_in['url_slug'] = $this->post_data['url_slug'];
				$data_in['url_menu'] = $this->post_data['url_menu'];
				if( empty($_FILES['image']) == false && $_FILES['image']['tmp_name']){
					$public_path = UPLOAD_PATH;
					$upload_img = Commons::cwUpload('image',$public_path.'/images/full/','',FALSE,$public_path.'/images/thumnail/',$detailImg['width'], $detailImg['height']);
					$data_in['image'] = '/full/'.$upload_img;
					if( empty($info['image'] ) == false ){
						// img info
						$full = $public_path.'/images'.$info['image'];
						if ( file_exists($full) ) {
							unlink($full);
						}
					}
				}
				$rs = $models->saveGroupCategory($data_in, $id);
				if( $id > 0 ){
					if( $rs >=0 ){
						$this->_redirect( '/admin/'.$this->controller );
					} else {
						$error[] = 'Update Group Category Fail';
					}
				} else {
					if( $rs > 0 ){
						$this->_redirect( '/admin/'.$this->controller );
					} else {
						$error[] = 'Add Group Category Fail';
					}
				}
			} else {
				$info = $this->post_data;
			}
		}
		$model = new Menu();
		$listMenu = $model->fetchMenu();
		$this->view->listMenu = $listMenu;
		$this->view->info = $info;
		$this->view->id = $id;
		$this->view->error = $error;
	}
    /**
     * Search page
     */
    public function listAction() {
       	$this->isAjax();
    	$draw = $this->post_data['draw']; // 
    	$model = new GroupCategory();
    	//define columns
    	$columns = array( // 
    			0 => "id",
                1 => "name",
    			2 => "image",
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
    	if( empty($this->post_data['search']) == false && empty($this->post_data['search']['value']) == false ){
    		$this->post_data['search-key'] = $this->post_data['search']['value'];
    	}
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $model->fetchAllGroupCategory( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    	$list = $model->fetchAllGroupCategory( $this->post_data );
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
    		$modal = new GroupCategory();
    		$reponse = $modal->deleteCategory($this->post_data['id']);
    		if ($reponse >= 0) {
    			$this->ajaxResponse(CODE_SUCCESS);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
}
