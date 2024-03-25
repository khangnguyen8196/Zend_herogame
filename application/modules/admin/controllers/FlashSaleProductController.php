<?php
/**
 * Setting management 
 * created by @Phuong Nguyen
 */
class Admin_FlashSaleProductController extends FrontBaseAction {

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
        $this->loadJs('flash-sale-product');
        // $this->loadJs(array('combo-product'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $model = new FlashSale();
        $this->view->list = $model->getAllFlashSale();
    }

    /**
     * detail page
     */
    public function detailAction(){
        $modelFlashSale = new FlashSale;
        $modelCategory = new Category();
        $modelFlashSaleProduct = new FlashSaleProduct;
        $modelFlashSaleProductVariant = new FlashSaleProductVariant;
        $listCategory = $modelCategory->listAllCategory();
        $modelCombo = new ComboProduct();
        $mdComboDetail = new ComboDetail();
        $modelVariant = new ProductVariant();
        $mdProduct = new Product();
    	$success = array();
        $info = array();
    	$error = array();
    	$data = $this->post_data;
    	$id = 0;
       
        if( empty($this->post_data ['id']) == false ) {
    	    $id = intval($this->post_data ['id']);
    	    $info = $modelFlashSale->getFlashSaleById($id );
    	    if( empty( $info ) == true ) {
    	        $this->_redirect( '/'.$this->module.'/'.$this->controller );
    	    }
    	}
    	if( $this->request->isPost() ) {
            if( empty($data['product_id'])==false) {
                foreach($data['product_id'] as $key =>$value){
                    if(empty($data['product_id'][$key])){
                        $error[] = ' Sản phẩm không được rổng';
                        break;
                    }
                }
            }
            if(empty($data['title_flash_sale'])==TRUE) {
                    $error[] = 'Tiêu đề không được bỏ trống';
            }
            if(empty($data['count_time_start'])==TRUE) {
                $error[] = 'Hãy chọn thời gian bắt đầu';
        }
            if(empty($data['count_time_end'])==TRUE) {
                $error[] = 'Hãy chọn thời gian kết thúc';
            }
            if($data['count_time_end'] <= $data['count_time_start']){
                $error[] = 'Thời gian kết thúc không được nhỏ hơn thời gian bắt đầu';
            }
           
    		if( empty($error) == true ){
                if($id==0){
                    $dataCreated = $this->getCreated();
                    $dataInsert = array_merge($data, $dataCreated);
                } else {
                    $dataUpdated = $this->getUpdated();
                    $dataInsert = array_merge($data, $dataUpdated);
                }
                $dataInsert['title_flash_sale'] = $data['title_flash_sale'];
                $dataInsert['status'] = $data['status'];
                $rs=$modelFlashSale->saveFlashSale( $dataInsert,$id);
                // $edit =FALSE;
                // $flash_sale_id = isset($_POST['flash_sale_id']) ? $_POST['flash_sale_id'] : $rs;
                $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
                if($id !=0 && isset($product_id)) {
                    $deletedProductIds = $_POST['flash_sale_product_delete'];
                    $countDel = count($_POST['flash_sale_product_delete']);
                    $listProductSaleId = $modelFlashSaleProduct->getFlashSaleProductId($id);
                    $listProduct = $_POST['product_id'];
                    
                    if($listProductSaleId){
                        $countProduct = count($listProductSaleId) - $countDel;
                        $countProductCur = count($listProductSaleId);
                    }
                    if (!empty($deletedProductIds)) {
                        foreach ($deletedProductIds as $valued) {
                            $existingVariant = $modelFlashSaleProduct->fetchFlashSaleProductById($valued);
                            if ($existingVariant) {
                                $modelFlashSaleProduct->deleteFlashSaleProductById($valued);
                                $modelFlashSaleProductVariant->deleteFlashSaleProductVariantBy($id,$existingVariant['product_id']);
                            }
                        }
                    }
                    if (!empty($listProduct) && $deletedProductIds) {
                        $listProduct = array_values($listProduct);
                        $currentProductIds = $modelFlashSaleProduct->getFlashSaleProductId($id);
                        foreach ($listProduct as  $index => $pro_id) {
                            if (isset($_POST['product_name'][$index], $_POST['price'][$index],$_POST['price_sales'][$index],$_POST['price_flash_sale'][$index], $_POST['percent_flash_sale'][$index])) {
                                $data = [
                                    'product_name' => $_POST['product_name'][$index],
                                    'price' => $_POST['price'][$index],
                                    'price_sales' => $_POST['price_sales'][$index],
                                    'price_flash_sale' => $_POST['price_flash_sale'][$index],
                                    'percent_flash_sale' => $_POST['percent_flash_sale'][$index],
                                    'product_id' =>  $pro_id,
                                    'flash_sale_id' => $id,
                                ];
                                if (in_array($pro_id, $currentProductIds)) {
                                    $modelFlashSaleProduct->updateFlashSaleProduct($data,$id,$pro_id);
                                    $productVariant = $modelVariant->getProductVariants($pro_id);
                                    if($productVariant){
                                        foreach ($productVariant as $j => $variant){
                                            $dataProductVariant['percent_flash_sale'] = $_POST['percent_flash_sale'][$index];
                                            $dataProductVariant['variant_price'] = $variant['variant_price'];
                                            $dataProductVariant['variant_price_sales'] = $variant['variant_price_sales'];
                                            $dataProductVariant['variant_price_flash_sale'] = $variant['variant_price']-($variant['variant_price']*($_POST['percent_flash_sale'][$index]/100));
                                            $dataProductVariant['product_id'] = $pro_id;
                                            $dataProductVariant['variant_id'] = $variant['id'];
                                            $dataProductVariant['flash_sale_id'] = $id;
                                            $modelFlashSaleProductVariant->updateFlashSaleProductVariant($dataProductVariant, $id, $pro_id, $variant['id']); 
                                        }
                                    }
                                } else {
                                    $modelFlashSaleProduct->saveFlashSaleProduct($data);
                                    $productVariant = $modelVariant->getProductVariants($pro_id);
                                    if($productVariant){
                                        foreach ($productVariant as $j => $variant){
                                            $dataProductVariant['percent_flash_sale'] = $_POST['percent_flash_sale'][$index];
                                            $dataProductVariant['variant_price'] = $variant['variant_price'];
                                            $dataProductVariant['variant_price_sales'] = $variant['variant_price_sales'];
                                            $dataProductVariant['variant_price_flash_sale'] = $variant['variant_price']-($variant['variant_price']*($_POST['percent_flash_sale'][$index]/100));
                                            $dataProductVariant['product_id'] = $pro_id;
                                            $dataProductVariant['variant_id'] = $variant['id'];
                                            $dataProductVariant['flash_sale_id'] = $id;
                                            $modelFlashSaleProductVariant->saveFlashSaleProductVariant($dataProductVariant); 
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $listProduct = array_values($listProduct);
                        $currentProductIds = $modelFlashSaleProduct->getFlashSaleProductId($id);
                        foreach ($listProduct as $index => $pro_id) {
                            if (isset($_POST['product_name'][$index], $_POST['price'][$index],$_POST['price_sales'][$index],$_POST['price_flash_sale'][$index], $_POST['percent_flash_sale'][$index])) {
                                $data = [
                                    'product_name' => $_POST['product_name'][$index],
                                    'price' => $_POST['price'][$index],
                                    'price_sales' => $_POST['price_sales'][$index],
                                    'price_flash_sale' => $_POST['price_flash_sale'][$index],
                                    'percent_flash_sale' => $_POST['percent_flash_sale'][$index],
                                    'product_id' => $pro_id,
                                    'flash_sale_id' => $id,
                                ];
                                if (in_array($pro_id, $currentProductIds)) {
                                    $modelFlashSaleProduct->updateFlashSaleProduct($data,$id,$pro_id);
                                    $productVariant = $modelVariant->getProductVariants($pro_id);
                                    if($productVariant){
                                        foreach ($productVariant as $j => $variant){
                                            $dataProductVariant['percent_flash_sale'] = $_POST['percent_flash_sale'][$index];
                                            $dataProductVariant['variant_price'] = $variant['variant_price'];
                                            $dataProductVariant['variant_price_sales'] = $variant['variant_price_sales'];
                                            $dataProductVariant['variant_price_flash_sale'] = $variant['variant_price']-($variant['variant_price']*($_POST['percent_flash_sale'][$index]/100));
                                            $dataProductVariant['product_id'] = $pro_id;
                                            $dataProductVariant['variant_id'] = $variant['id'];
                                            $dataProductVariant['flash_sale_id'] = $id;
                                            $modelFlashSaleProductVariant->updateFlashSaleProductVariant($dataProductVariant, $id, $pro_id, $variant['id']); 
                                        }
                                    }
                                } else {
                                    $modelFlashSaleProduct->saveFlashSaleProduct($data);
                                    $productVariant = $modelVariant->getProductVariants($pro_id);
                                    if($productVariant){
                                        foreach ($productVariant as $j => $variant){
                                            $dataProductVariant['percent_flash_sale'] = $_POST['percent_flash_sale'][$index];
                                            $dataProductVariant['variant_price'] = $variant['variant_price'];
                                            $dataProductVariant['variant_price_sales'] = $variant['variant_price_sales'];
                                            $dataProductVariant['variant_price_flash_sale'] = $variant['variant_price']-($variant['variant_price']*($_POST['percent_flash_sale'][$index]/100));
                                            $dataProductVariant['product_id'] = $pro_id;
                                            $dataProductVariant['variant_id'] = $variant['id'];
                                            $dataProductVariant['flash_sale_id'] = $id;
                                            $modelFlashSaleProductVariant->saveFlashSaleProductVariant($dataProductVariant); 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    $listProduct = $data['product_id'];
                    foreach ($listProduct as $key => $productId) {
                        $product = $mdProduct->getProductInfoById($productId);
                        $productVariant = $modelVariant->getProductVariants($productId);
                        if($productVariant){
                            foreach ($productVariant as $j => $variant){
                                $dataProductVariant['percent_flash_sale'] = $_POST['percent_flash_sale'][$key];
                                $dataProductVariant['variant_price'] = $variant['variant_price'];
                                $dataProductVariant['variant_price_sales'] = $variant['variant_price_sales'];
                                $dataProductVariant['variant_price_flash_sale'] = $variant['variant_price']-($variant['variant_price']*($_POST['percent_flash_sale'][$key]/100));
                                $dataProductVariant['product_id'] = $productId;
                                $dataProductVariant['variant_id'] = $variant['id'];
                                $dataProductVariant['flash_sale_id'] = $rs;
                                $modelFlashSaleProductVariant->saveFlashSaleProductVariant($dataProductVariant); 
                            }
                        }
                        if ($product) {
                            $dataProduct['price'] = $_POST['price'][$key];
                            $dataProduct['price_sales'] = $_POST['price_sales'][$key];
                            $dataProduct['price_flash_sale'] = $_POST['price_flash_sale'][$key];
                            $dataProduct['percent_flash_sale'] = $_POST['percent_flash_sale'][$key];
                            $dataProduct['product_name'] = $product['title'];
                            $dataProduct['product_id'] = $productId;
                            $dataProduct['flash_sale_id'] = $rs;
                            $modelFlashSaleProduct->saveFlashSaleProduct($dataProduct);    
                        } 
                    }
                }
                if ($id > 0) {
                    if ($rs >= 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Thêm Flash sale thất bại';
                    }
                } else {
                    if ($rs > 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Cập Nhật  Flash sale thất bại';
                    }
                }
    		}else {
    			$info = $this->post_data;
    		} 
    	}
        if(empty($info) == false ){
            $listFlashSale = $modelFlashSaleProduct->getFlashSaleProductById($info['flash_sale_id']);
            $this->view->listFlashSale = $listFlashSale;
        }
        $this->view->info = $info;
    	$this->view->success = $success;
    	$this->view->error = $error;
        $this->view->listCategory = $listCategory;
}
    
    /**
     * delete
     */
    public function deleteAction() {
    	$this->isAjax();
    	if (empty($this->post_data['id']) == false) {
            $id = intval($this->post_data['id']);
            $model = new FlashSale();
            $modelFlashSaleProduct = new FlashSaleProduct;
            $modelFlashSaleProductVariant = new FlashSaleProductVariant;
    	    $reponse = $model->deleteFlashSale($id);
    		if ($reponse >= 0) {
                $modelFlashSaleProduct->deleteFlashSaleProduct($id);
                $modelFlashSaleProductVariant->deleteFlashSaleProductVariant($id);
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
            3 => 'id_category',
            4 => "price",
            5 => "price_sales",
            6 => 'status',
            
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
        $count = $model->getAllProduct($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->getAllProduct($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }

	public function listFlashSaleAction() {
        $this->isAjax();
        $draw = $this->post_data['draw']; 
        $model = new FlashSale();
        $columns = array(// 
            0 => "flash_sale_id",
            1 => "title_flash_sale",
            2 => "count_time_start",
            3 => "count_time_end",
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
        $count = $model->fetchAllFlashSale($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllFlashSale($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }
}