
<?php

class Admin_ProductController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/libs/ckeditor/ckeditor.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/core/libraries/color-picker/js/bootstrap-colorpicker.js', 'text/javascript'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/bootstrap-datetimepicker.min.css"));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/pickers/bootstrap-datetimepicker.min.js', 'text/javascript'));
        $this->loadJs('product');
    }

    /**
     * Search page
     */
    public function indexAction() {
        $modelCategory = new Category();
        $listCategory = $modelCategory->listAllCategory();
        $this->view->listCategory = $listCategory;
    }

    public function detailAction() {
        $model = new Product();
        $modelVariant = new ProductVariant();
        $modelVariantImg = new VariantImage();
        $modelCategory = new Category();
        $listCategory = $modelCategory->listAllCategory();
        $modelCombo = new ComboProduct();
        $info = array();
        $error = array();
        //
        $id = 0;
        // get post card information if there is postcard'id available
        if (empty($this->post_data ['id']) == false) {
            $id = intval(@$this->post_data ['id']);
            if ($id > 0) {
                $info = $model->fetchProductById($id);
                if (empty($info) == true) {
                    $this->_redirect('/admin/' . $this->controller);
                }
            } else {
                $this->_redirect('/admin/' . $this->controller);
            }
        }
        // auto insert image color default
        if( empty( $info['image_color'] ) == true ){
            if( empty( $info['gallery'] ) == false ){
                $beforeGallery = explode(',', $info['gallery']);
                $arrImgColor = array();
                foreach ( $beforeGallery as $g ){
                    $arrImgColor[] = '1';
                }
                $info['product_color'] = '1';
                $info['image_color'] = implode(',',$arrImgColor);
                $model->updateProduct(array( 'product_color' => '1' ,'image_color' => $info['image_color']), $id);
                
            }
        }
        if ($this->request->isPost()) {
            $data = $this->post_data;
            $xml = APPLICATION_PATH . '/xml/product.xml';
            $error = $this->checkInputData($xml, $data);
            
            if (empty($error) == true) {
                //
                $data['url_product'] = Commons::url_slug($data['url_product']);
                $check = $model->checkExistProductUrl($data['url_product'], $id);
                if (empty($check) == false) {
                    $error[] = 'Url Này Đã Tồn Tại';
                }
            }
            if( empty($data['variant_name'])==false) {
                foreach($data['variant_name'] as $key =>$variant){
                    if(empty($data['variant_name'][$key])){
                        $error[] = ' Tên Loại không được rỗng';
                        break;
                    }
                    if(empty(is_numeric($data['variant_price'][$key]))){
                        $error[] = ' Giá  không được rỗng';
                        break;
                    }
                        if(empty(is_numeric($data['variant_price_sales'][$key]))){
                        $error[] = 'Giá  không được rỗng';
                        break;
                    }
                }
            }
         
            // add data vi first
            if (empty($error) == true) {
                //
                if (is_numeric($data['priority']) == false) {
                    $data['priority'] = 0;
                }
                if (is_numeric($data['price']) == false) {
                    $data['price'] = 0;
                }
                if (is_numeric($data['price_sales']) == false) {
                    $data['price_sales'] = 0;
                }

                if ($data['price_sales'] == 0) {
                    $data['price_sales'] = $data['price'];
                }
                //
                $dataUpdated = $this->getUpdated();
                $data = array_merge($data, $dataUpdated);
                $dataCreated = $this->getCreated();
                $data = array_merge($data, $dataCreated);
                //
                $public_path = UPLOAD_PATH;
                if (empty($_FILES['image_product']) == false && $_FILES['image_product']['tmp_name']) {
                    $upload_img = '';
                    $nowdir = 'img_'.date('d_m_Y');
                    $ext = pathinfo($_FILES['image_product']['name'], PATHINFO_EXTENSION);
                    $fileName = pathinfo($_FILES['image_product']['name'], PATHINFO_FILENAME);
                    $fileName = str_replace(' ','-', $fileName);
                    $newname = $fileName.'_'.rand(0,1000000).'_'.uniqid('', true).'.'.$ext;
                    Commons::makedirs($public_path.'/images/'.$nowdir);
                    if( move_uploaded_file($_FILES["image_product"]["tmp_name"], $public_path.'/images/'.$nowdir.'/' . $newname)){
                        $upload_img = '/'.$nowdir.'/'.$newname;
                    }
                    $data['image'] = $upload_img;
                    if (empty($info['image']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['image'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }
                if( empty($data['gallery_delete']) == false && empty($info['gallery']) == false ){
                    $gallery = explode(",",$info['gallery']);
                    $imageColor = explode(",",$info['image_color']);
                    foreach ( $data['gallery_delete'] as $keyd => $valued ){
                        foreach ($gallery as $k => $v ){
                            if( $valued == $v){
                                unset($gallery[$k]);
                                unset($imageColor[$k]);
                                $full = $public_path . '/images/' . $valued;
                                if (file_exists($full)) {
                                    unlink($full);
                                }
                                break;
                            }
                        }
                    }
                    if( empty($gallery) == false ){
                        $info['gallery'] = implode(",",$gallery);
                    } else {
                        $info['gallery'] = '';
                    }
                    $data['gallery'] = $info['gallery'];
                    if( empty($imageColor) == false ){
                        $info['image_color'] = implode(",",$imageColor);
                    } else {
                        $info['image_color'] = '';
                    }
                }
                if( empty($_FILES['gallery']) == false ){
                    $nowdir = 'img_'.date('d_m_Y');
                    $listFile = array();
                    $listImageColor = array();
                    foreach ( $_FILES['gallery']['name'] as $key => $value ){
                        if( empty( $value ) == false ){
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            $fileName = pathinfo($value, PATHINFO_FILENAME);
                            $fileName = str_replace(' ','-', $fileName);
                            $newname = $fileName .'_'. rand(0,1000000).'_'.uniqid('', true).'.'.$ext;
                            Commons::makedirs($public_path.'/images/'.$nowdir);
                            if( move_uploaded_file($_FILES["gallery"]["tmp_name"][$key], $public_path.'/images/'.$nowdir.'/' . $newname)){
                                $listFile[] = $nowdir.'/'.$newname;
                                $listImageColor[] = $data['image_color'][$key];
                            }
                        } else {
                            unset( $data['image_color'][$key] );
                        }
                    }
                    if( empty($listFile) == false ){
                        if( empty($info['gallery']) == false ){
                            $data['gallery'] =  $info['gallery'].','.implode(",",$listFile);
                        } else {
                            $data['gallery'] =  implode(",",$listFile);
                        }
                    }
                    if( empty($listImageColor) == false ){
                        if( empty($info['image_color']) == false ){
                            $data['image_color'] =  $info['image_color'].','.implode(",",$listImageColor);
                        } else {
                            $data['image_color'] =  implode(",",$listImageColor);
                        }
                    } else {
                        $data['image_color'] = $info['image_color'];
                    }

                }
                   

                if( empty($data["relative_product"]) == false){
                    $data["relative_product"] = implode(',', $data["relative_product"]);
                }
                if( empty($data["product_color"]) == false){
                    $data["product_color"] = implode(',', $data["product_color"]);
                }
                /*if( empty($data["image_color"]) == false){
                    $data["image_color"] = implode(',', $data["image_color"]);
                }*/ 
                
                $rs = $model->saveProduct($data, $id);
                
                
              
                $product_id = isset($_POST['id']) ? $_POST['id'] : $rs;
                $new_variant_ids = array();
                if( !empty($_POST['variant_name'])){
                    foreach($_POST['variant_name'] as $key => $variation){
                        $var = [
                            'variant_name' => $variation,
                            'variant_price' => $_POST['variant_price'][$key],
                            'variant_price_sales' => $_POST['variant_price_sales'][$key],
                            'product_id' => $rs,
                            'status' => STATUS_ACTIVE
                        ];
                        if ($_POST['variant_id'][$key] > 0) {
                            $variant = $modelVariant->fetchVariantById($_POST['variant_id'][$key]);
                            if (!empty($variant)) {
                                $var['product_id'] = $variant['product_id'];
                                $var['status'] = isset($_POST['variant_status'][$key])&& $_POST['variant_status'][$key] == 0 ? 0 : STATUS_ACTIVE;
                            }
                            $modelVariant->saveVariant($var, $_POST['variant_id'][$key]);
                            //delete  images variant
                            $list_url_image = $modelVariantImg->getAllImages($info['id']);
                            if (!empty($_POST['url_image_delete']) && !empty($list_url_image)) {
                                $url_image = explode(",", $list_url_image);
                                foreach ($_POST['url_image_delete'] as $keyd => $valued ) {
                                    foreach ($url_image as $k => $v ) {
                                        if ($valued == $v) {
                                            $full = $public_path . '/images/' . $valued;
                                            if (file_exists($full)) {
                                                unlink($full);
                                            }
                                            unset($url_image[$k]);
                                            $modelVariantImg->deleteImageByProductUrl($info['id'], $valued);
                                            break;
                                        }
                                    }
                                }         
                            }

                            // 
                            $variants = $modelVariant->getProductVariants($product_id);
                            foreach ($variants as  $variant) {
                                $variant_id = $variant['id'];
                                $variant_images = array();
                                if (!empty($_FILES['url_image']) && $_FILES['url_image']['tmp_name']) {
                                    $nowdir = 'img_' . date('d_m_Y');
                                    foreach ($_FILES['url_image']['name'][$variant_id] as $j => $name) {
                                        if (!empty($name)) {
                                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                                            $fileName = pathinfo($name, PATHINFO_FILENAME);
                                            $fileName = str_replace(' ', '-', $fileName);
                                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                                            Commons::makedirs($public_path . '/images/' . $nowdir);
                                            if (move_uploaded_file($_FILES['url_image']['tmp_name'][$variant_id][$j], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                                $variant_images[] = array(
                                                    'product_variant_id' => $variant_id,
                                                    'url_image' => $nowdir . '/' . $newname,
                                                    'status' => STATUS_ACTIVE
                                                );
                                            }
                                        }
                                    }
                                }
                                if (!empty($variant_images)) {
                                    $modelVariantImg->saveVariantImage($variant_images);
                                }
                            }
                        } else {
                            if (!empty($product_id)) {
                                $var['product_id'] = $product_id;
                                $variant_id = $modelVariant->saveVariant($var);
                                $new_variant_ids[] =$variant_id; 
                                $variant_images = array();
                                if (!empty($_FILES['url_image']) && $_FILES['url_image']['tmp_name']) {
                                    $nowdir = 'img_' . date('d_m_Y');
                                    foreach ($new_variant_ids as $key => $variant_id) {
                                        foreach ($_FILES['url_image']['name'][$key] as $j => $name) {
                                            if (!empty($name[$key])) {
                                                $ext = pathinfo($name, PATHINFO_EXTENSION);
                                                $fileName = pathinfo($name, PATHINFO_FILENAME);
                                                $fileName = str_replace(' ', '-', $fileName);
                                                $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                                                Commons::makedirs($public_path . '/images/' . $nowdir);
                                                if (move_uploaded_file($_FILES['url_image']['tmp_name'][$key][$j], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                                    $variant_images[] = array(
                                                        'product_variant_id' => $variant_id,
                                                        'url_image' => $nowdir . '/' . $newname,
                                                        'status' => STATUS_ACTIVE
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                                if (!empty($variant_images)) {
                                    $modelVariantImg->saveVariantImage($variant_images);
                                }
                            } else {
                                $variant_id = $modelVariant->saveVariant($var);
                            }
                            $new_variant_ids[] = $variant_id;
                            $variant_images = array();
                            if (!empty($_FILES['url_image']) && $_FILES['url_image']['tmp_name']) {
                                $nowdir = 'img_' . date('d_m_Y');
                                foreach ($new_variant_ids as $key => $variant_id) {
                                    foreach ($_FILES['url_image']['name'][$key] as $j => $name) {
                                        if (!empty($name[$key])) {
                                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                                            $fileName = pathinfo($name, PATHINFO_FILENAME);
                                            $fileName = str_replace(' ', '-', $fileName);
                                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                                            Commons::makedirs($public_path . '/images/' . $nowdir);
                                            if (move_uploaded_file($_FILES['url_image']['tmp_name'][$key][$j], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                                $variant_images[] = array(
                                                    'product_variant_id' => $variant_id,
                                                    'url_image' => $nowdir . '/' . $newname,
                                                    'status' => STATUS_ACTIVE
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($variant_images)) {
                                $modelVariantImg->saveVariantImage($variant_images);
                            }
                        }                                                                    
                    }   
                }

                if ($id > 0) {
                    if ($rs >= 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Thêm Sản Phẩm Thất Bại';
                    }
                } else {
                    if ($rs > 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Cập Nhật Sản Phẩm Thất Bại';
                    }
                }
            }
            $info = $data;
        }
        if( empty( $info['image_color'] ) == false ){
            $info['image_color'] = explode(',',  $info['image_color']);
        }
        if( empty( $info['product_color'] ) == false ){
            $info['product_color'] = explode(',',  $info['product_color']);
        }
        $mdlColor = new ProductColor();
        $listColor = $mdlColor->fetchAllColor();
        $arrColor = [];
        if( empty( $listColor ) == false ){
            foreach ( $listColor as $color ){
                $arrColor[$color['id']] = $color['color_name'];
            }
        }
        if( empty( $info ) == false ){
            $listVariant = $modelVariant->getProductVariants($info['id']);
            $this->view->variants = $listVariant;

            $listVariantImg = $modelVariantImg->getProductImages($info['id']);
            $this->view->listVariantImg = $listVariantImg;
            // echo"<pre>";print_r( $listVariantImg);exit;

            $listCombo = $modelCombo->getAllComboProduct($info['id']);
            $this->view->listCombo = $listCombo;
        }

        $this->view->listColor = $arrColor;
        $this->view->listCategory = $listCategory;
        $this->view->info = $info;
        $this->view->error = $error;
        $this->view->id = $id;
    }
    public function mediaAction() {
        $this->_helper->layout->disableLayout(true);
        $this->loadJs('post');
        $mdlMedia = new Media();
        $data = array();
        $data['length'] = MAX_ITEM_IMAGE;
        $list = $mdlMedia->fetchAllMedia($data);
        $this->view->listMedia = $list;
        $this->view->functionNum = $this->post_data["CKEditorFuncNum"];
    }

    /**
     * Search page
     */
    public function listAction() {
        $this->isAjax();
        $draw = $this->post_data['draw']; //
        $model = new Product();
        //define columns
        $columns = array(//
            0 => "id",
            1 => "title",
            2 => "image",
            3 => "price",
            4 => "created_date",
            5 => "updated_date",
            6 => 'updated_by',
            7 => 'status',
            8 => 'id_category'
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

    /**
     *
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['id']) == false) {
            $modal = new Product();
            $id = intval($this->post_data['id']);
            if ($id > 0) {
                $reponse = $modal->deleteProduct($id);
                if ($reponse >= 0) {
                    $this->ajaxResponse(CODE_SUCCESS);
                }
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
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
    public function showPopupEditProductAction(){
        $this->isAjax();
        if (empty($this->post_data ['id']) == false) {
            $id = intval(@$this->post_data ['id']);
            if ($id > 0) {
                $model = new Product();
                $info = $model->fetchProductById($id);
                if ( empty($info) == true) {
                    $this->ajaxResponse( CODE_HAS_ERROR );
                }
            } else {
                $this->ajaxResponse( CODE_HAS_ERROR );
            }
        }
        $this->view->info = $info;
        $this->view->idProduct = $this->post_data['id'];
        $this->view->type = $this->post_data['type'];
        $html = $this->view->render("/product/_dialog-edit-product.phtml");
        $this->ajaxResponse(CODE_SUCCESS, '', $html);
    }
    public function updateProductAction(){
        $this->isAjax();
        $model = new Product();
        $data = array();
        if( $this->post_data['type'] == 1 ){
            $data['title'] = $this->post_data['title'];
        } elseif( $this->post_data['type'] == 2 ){
            $data['price'] = $this->post_data['price'];
        } elseif( $this->post_data['type'] == 3 ){
            $data['price_sales'] = $this->post_data['price_sales'];
        }
        $model->updateProduct($data, $this->post_data['id']);
        $this->ajaxResponse( CODE_SUCCESS );
    }

    public function getListComboAction() {
        $this->isAjax();
        $comboDetail = new ComboDetail();
        $listCombo = $comboDetail->getComboByProductId($this->post_data['id']);
        $arrCombo = array();
        if (!empty($listCombo)) {
            foreach ($listCombo as $combo) {
                $productId = $combo['product_id'];
                if (!isset($arrCombo[$productId])) {
                    $arrCombo[$productId] = array();
                }
                $arrCombo[$productId][] = $combo;
            }
        }
        $selectedCombo = array();
        if( empty($this->post_data["selectCombo"]) == false){
            $selectedCombo = explode(',', $this->post_data["selectCombo"]);
        }
        $this->view->selectedCombo = $selectedCombo;
        $this->view->arrCombo = $arrCombo;
        $html = $this->view->render("/combo-product/_relative-combo.phtml");
        $this->ajaxResponse(CODE_SUCCESS, '', $html);
    }
}
