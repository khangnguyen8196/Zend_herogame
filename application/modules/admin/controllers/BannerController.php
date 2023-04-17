<?php

/**
 * Banner management 
 * created by @TinPham
 */
class Admin_BannerController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->loadJs(array('banner'));
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
        $model = new Banner();
        //define columns
        $columns = array(// 
            0 => "id",
            1 => "title",
            2 => "type",
            3 => "page",
            4 => 'status',
        );

        //order function
        if (empty($this->post_data["order"]) == false) {
            $this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
            $this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
        } else {
            $this->post_data["order"]["column"] = "updated_at";
            $this->post_data["order"]["dir"] = "desc";
        }
        //search function
        if (empty($this->post_data["columns"]) == false && is_array($this->post_data["columns"])) {
            foreach ($this->post_data["columns"] as $column) {
                if ($column["searchable"] == true && empty($column["search"]) == false && $column["search"]["value"] != "") {
                    $this->post_data[$column["data"]] = $column["search"]["value"];
                }
            }
        }
        if (empty($this->post_data['search']) == false && empty($this->post_data['search']['value']) == false) {
            $this->post_data['search-key'] = $this->post_data['search']['value'];
        }
        //get total data
        $this->post_data['count_only'] = 1;
        $count = $model->fetchAllBanner($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);

        $list = $model->fetchAllBanner($this->post_data);
        //return data
        $return = array();
        $return['draw'] = $draw;
        $return['recordsTotal'] = $count;
        $return['recordsFiltered'] = $count;
        $return['data'] = $list;
        $this->_helper->json($return);
        exit;
    }

    /**
     * detail page
     */
    public function detailAction() {
        $models = new Banner();
        $mdlMedia = new Media();
        $info = array();
        $error = array();
        $id = 0;
        $mediaInfo = array();
        $data = $this->post_data;
        // get post card information if there is postcard'id available
        if (empty($this->post_data ['id']) == false) {
            $id = intval($this->post_data ['id']);
            $info = $models->fetchBannerById($id);
            if (empty($info) == true) {
                $this->_redirect('/admin/' . $this->controller);
            }
        }
        // check request is POST or GET
        if ($this->request->isPost()) {
            $xml = APPLICATION_PATH . '/xml/banner.xml';
            $error = $this->checkInputData($xml, $this->post_data);
            if (empty($error) == true) {
                $isVideo = 0;
                if( empty($this->post_data["is_video"]) == false ){
                    $isVideo = $this->post_data["is_video"];
                }
                $dataIn = array(
                    'title' => $data['title'],
                    'link' => $data['link'],
                    'description' => $data['description'],
                    'type' => $data['type'],
                    'page' => $data['page'],
                    'status' => $data['status'],
                    'video_url' => $data['video_url'],
                    'is_video' => $isVideo,
                );
                $rs = $models->saveBanner($dataIn, $id);
                $public_path = UPLOAD_PATH.'/images/full/';
                if ($id == 0) {
                    if ($rs > 0) {
                    	if( empty($_FILES['image']['name']) == false ){
                    		$filename = uniqid().$_FILES['image']['name'];
                    		$upload_image = $public_path.basename($filename);
                    		if(move_uploaded_file($_FILES['image']['tmp_name'],$upload_image)){
                    			$models->saveBanner(array('image'=>$filename), $rs);
                    		}
                    	}
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Tạo banner thất bại';
                    }
                } else {
                    if ($rs >= 0) {
                    	if( empty($_FILES['image']['name']) == false ){
                    		$filename = uniqid().$_FILES['image']['name'];
                    		$upload_image = $public_path.basename($filename);
                    		if(move_uploaded_file($_FILES['image']['tmp_name'],$upload_image)){
                    			$models->saveBanner(array('image'=>$filename), $id);
                    			// remove
                    			if( empty($info['image']) == false ){
                    				$realUrlPath = $public_path.$info['image'];
                    				if ( file_exists( $realUrlPath ) ) {
                    					unlink( $realUrlPath );
                    				}
                    			}
                    		}
                    	}
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Tạo banner thất bại';
                    }
                }
            }
            $info = $data;
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
            $modal = new Banner();
            $reponse = $modal->deleteBanner($this->post_data['id']);
            if ($reponse >= 0) {
                $this->ajaxResponse(CODE_SUCCESS);
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }

}
