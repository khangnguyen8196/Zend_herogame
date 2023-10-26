<?php
/**
 * Setting management 
 * created by @Phuong Nguyen
 */
class Admin_ComboProductController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/libs/ckeditor/ckeditor.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/core/libraries/color-picker/js/bootstrap-colorpicker.js', 'text/javascript'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/bootstrap-datetimepicker.min.css"));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/pickers/bootstrap-datetimepicker.min.js', 'text/javascript'));
        $this->loadJs('combo-product');
        // $this->loadJs(array('combo-product'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $model = new ComboProduct();
        $this->view->list = $model->fetchAllComboProduct();
    }

    /**
     * detail page
     */
    public function detailAction(){
        $models = new ComboProduct();
        $mdComboDetail = new ComboDetail();
        $mdProduct = new Product();
    	$info = array();
    	$error = array();
    	$data = $this->post_data;
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    	    $id = intval($this->post_data ['id']);
    	    $info = $models->fetchComboById( $id );
    	    if( empty( $info ) == true ) {
    	        $this->_redirect( '/'.$this->module.'/'.$this->controller );
    	    }
    	}
      
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    	    if( empty($data['title']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập Tên Combo ';
    	    }
			if( empty($data['total_discount']) == TRUE ){
    	        $error[] = 'Vui Lòng Nhập Giá Combo Sản Phẩm ';
    	    }if( empty($data['total_price']) == TRUE ){
    	        $error[] = 'Giá tổng sản phẩm không được rỗng ';
    	    }
            if( empty($data['product_id'])==false) {
                foreach($data['product_id'] as $key =>$value){
                    if(empty($data['product_id'][$key])){
                        $error[] = ' Sản phẩm không được rổng';
                        break;
                    }
                }
            }
    		if( empty($error) == true ){
    		    $dataInsert['title'] = $data['title'];
    		    $dataInsert['total_discount'] = $data['total_discount'];
                $dataInsert['total_price'] = $data['total_price'];
                $dataUpdated = $this->getUpdated();
                $dataInsert = array_merge($data, $dataUpdated);
                $dataCreated = $this->getCreated();
                $dataInsert = array_merge($data, $dataCreated);

                $public_path = UPLOAD_PATH;
                if (empty($_FILES['image_combo']) == false && $_FILES['image_combo']['tmp_name']) {
                    $upload_img = '';
                    $nowdir = 'img_'.date('d_m_Y');
                    $ext = pathinfo($_FILES['image_combo']['name'], PATHINFO_EXTENSION);
                    $fileName = pathinfo($_FILES['image_combo']['name'], PATHINFO_FILENAME);
                    $fileName = str_replace(' ','-', $fileName);
                    $newname = $fileName.'_'.rand(0,1000000).'_'.uniqid('', true).'.'.$ext;
                    Commons::makedirs($public_path.'/images/'.$nowdir);
                    if( move_uploaded_file($_FILES["image_combo"]["tmp_name"], $public_path.'/images/'.$nowdir.'/' . $newname)){
                        $upload_img = '/'.$nowdir.'/'.$newname;
                    }
                    $dataInsert['image_cb'] = $upload_img;
                    if (empty($info['image_cb']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['image_cb'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }

    		    $dataInsert['status'] = $data['status'];
    		    $rs=$models->saveComboProduct( $dataInsert, $id );


                $combo_id = isset($_POST['id']) ? $_POST['id'] : $rs;
                $deleted = false;
                if (!empty($_POST['combo_id_delete'])) {
                    $list_combo_detail = $mdComboDetail->getAllComboDetailIdsByComboId($info['id']);
                    if (!empty($list_combo_detail)) {
                        foreach ($_POST['combo_id_delete'] as $valued) {
                            if (in_array($valued, $list_combo_detail)) {
                                $mdComboDetail->deleteComboDetail($valued);
                                $deleted = true; // Đánh dấu đã xoá combo_detail
                            }
                        }
                    }
                }
                if (!$deleted) {
                    if( !empty($_POST['product_id'])){
                        foreach($_POST['product_id'] as $key => $value){
                            $product= $mdProduct->fetchProductById($value);
                            $pro = [
                                'product_id' => $value,
                                'combo_id' => $rs,
                                'price' => $product['price'],
                                'status' => STATUS_ACTIVE,
                                'product_title' =>$product['title'],
                            ];
                            if ($_POST['combo_detail_id'][$key] > 0) {
                                $comboDetail = $mdComboDetail->fetchComboDetailById($_POST['combo_detail_id'][$key]);
                                if (!empty($comboDetail)) {
                                    $pro['combo_id'] = $comboDetail['combo_id'];
                                }
                                $mdComboDetail->saveComboDetail($pro, $_POST['combo_detail_id'][$key]);                   
                            }else {
                                if(!empty($_POST['id'])){
                                    $pro['combo_id'] = $_POST['id'];
                                    $mdComboDetail->saveComboDetail($pro);
                                }else {
                                    $mdComboDetail->saveComboDetail($pro);
                                }
                                
                            }
                        }
                    }
                }
    			if ($id > 0) {
                    if ($rs >= 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Thêm Combo Sản Phẩm Thất Bại';
                    }
                } else {
                    if ($rs > 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Cập Nhật  Combo Sản Phẩm Thất Bại';
                    }
                }
    		} else {
    			$info = $this->post_data;
    		}
    	}
        if( empty( $info ) == false ){
            $listComboDetail = $mdComboDetail->getComboDetailById($info['id']);
            $this->view->listComboDetail = $listComboDetail;
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
    	    $modal = new ComboProduct();
    	    $reponse = $modal->deleteComboProduct($this->post_data['id']);
    		if ($reponse >= 0) {
    			$this->ajaxResponse(CODE_SUCCESS);
    		}
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }

	public function listProductAction() {
        $this->isAjax();
        $draw = $this->post_data['draw']; //
        $model = new Product();
        //define columns
        $columns = array(//
            0 => "id",
            1 => "title",
            2 => "image",
            3 => "price",
            4 => 'status',
            5 => 'id_category'
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
        $count = $model->fetchAllProduct($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllProduct($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }

	public function getListProductAction() {
        $this->isAjax();
        $categoryRearrange = array();
        $categoryMdl = new Category();
        $params["type_of_category"] = 1;
        $listCategory = $categoryMdl->listAllCategory($params);

        if (empty($listCategory) == false) {
            foreach ($listCategory as $value) {
                $categoryRearrange[$value["id"]] = $value["name"];
            }
        }
        $productMdl = new Product();
        $productList = $productMdl->fetchAllProduct();
        $productMapping = array();
        if (empty($productList) == false && empty($categoryRearrange) == false) {
            foreach ($productList as $value) {
                if (empty($this->post_data["id"]) == false && $this->post_data["id"] == $value["id"]) {
                    continue;
                }
                $productMapping[$categoryRearrange[$value["id_category"]]][] = $value;
            }
        }
        $selectedRelativeProduct = array();
        if( empty($this->post_data["selectRelativeProduct"]) == false){
            $selectedRelativeProduct = explode(',', $this->post_data["selectRelativeProduct"]);
        }
        $this->view->selectedRelativeProduct = $selectedRelativeProduct;
        $this->view->productMapping = $productMapping;
        $html = $this->view->render("/product/_relative-product.phtml");
        $this->ajaxResponse(CODE_SUCCESS, '', $html);
    }

    public function listComboAction() {
        $this->isAjax();
        $draw = $this->post_data['draw']; // 
        $model = new ComboProduct();
        //define columns
        $columns = array(// 
            0 => "id",
            1 => "title",
            2 => "image_cb",
            3 => "total_discount",
            4 => "status",
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
        $count = $model->fetchAllCombo($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllCombo($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }
}