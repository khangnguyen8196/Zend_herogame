
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
        $mdComboDetail = new ComboDetail();
        $mdFlashSaleProductVariant = new FlashSaleProductVariant();
        $mdFlashSaleProduct = new FlashSaleProduct();
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
            if( isset($data['variant_name']) && empty($data['variant_name']) == false) {
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
                if($id==0){
                    $dataCreated = $this->getCreated();
                    $data = array_merge($data, $dataCreated);
                }
                $dataUpdated = $this->getUpdated();
                $data = array_merge($data, $dataUpdated);

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
                }else{
                    $data["relative_product"]='';
                }

                if( empty($data["order_with_product"]) == false){
                    $data["order_with_product"] = implode(',', $data["order_with_product"]);
                }else{
                    $data["order_with_product"]='';
                }
                if( empty($data["product_color"]) == false){
                    $data["product_color"] = implode(',', $data["product_color"]);
                }
                
                /*if( empty($data["image_color"]) == false){
                    $data["image_color"] = implode(',', $data["image_color"]);
                }*/ 

                // $listFlashSaleProductVariant = $mdFlashSaleProductVariant->getAllFlashSaleProductVariantBy($id);
                // foreach($listFlashSaleProductVariant as  $flashSaleProductVariant){
                //     echo '<pre>';
                //     print_r($listFlashSaleProductVariant);
                //     exit;
                // }
                
                $listCombo = $modelCombo->getAllComboProduct($id);
                if ($listCombo) {
                    foreach ($listCombo as $key => $combo) {
                        if($data['price_sales'] == 0 ){
                            $combo['total_price'] = $combo['total_price'] - $combo['price'] + $data['price'];
                            $combo['total_discount'] = $combo['total_price'] - $combo['price_discount'];
                            $combo['price'] = $data['price'];
                        }else{
                            $combo['total_price'] = $combo['total_price'] - $combo['price'] + $data['price_sales'];
                            $combo['total_discount'] = $combo['total_price'] - $combo['price_discount'];
                            $combo['price'] = $data['price_sales'];
                        }
                        $mdComboDetail->updateComboDetail(["price" => $combo['price']],$id);
                        $modelCombo->saveComboProduct(
                            [
                                "total_price" => $combo['total_price'],
                                "total_discount" =>  $combo['total_discount'],

                    
                        ], $combo['id'] );
                    }
                }
                $rs = $model->saveProduct($data, $id);
                
                
                $product_id = isset($_POST['id']) ? $_POST['id'] : $rs;
                $variant_id = isset($_POST['variant_id']) ? $_POST['variant_id'] : '';
                $new_variant_ids = array();
                $edit =FALSE;
                if (isset($variant_id) && isset($product_id) && $id !=0) {
                    $deletedVariantIds = $_POST['variant_delete'];
                    $newVariantIds = array_filter($_POST['variant_id'], function ($var_id) {
                        return $var_id == 0;
                    });
                    $variantIdOlds = array_filter($_POST['variant_id'], function ($var_id) use ($deletedVariantIds) {
                        return isset($deletedVariantIds) ? (!in_array($var_id, $deletedVariantIds) && $var_id != 0) : ($var_id != 0);
                    });
                    
                    $countDel = count($_POST['variant_delete']);
                    $listVariant = $modelVariant->getProductVariants($product_id);
                    if($listVariant){
                        $countVarianat = count($listVariant) - $countDel;
                    }
                    $countFirst = count($newVariantIds);
                    if (!empty($deletedVariantIds)) {
                        foreach ($deletedVariantIds as $valued) {
                            $existingVariant = $modelVariant->fetchVariantById($valued);
                            if ($existingVariant) {
                                $modelVariant->deleteVariant($valued);
                                $mdFlashSaleProductVariant->deleteFlashProductVariant($product_id,$valued);
                            }
                            $listImgId = $modelVariantImg->getAllImageIdsByVariantId($valued);
                            self::deleteImages($listImgId, $modelVariantImg, $public_path);
                        }
                    }
                    if (!empty($newVariantIds) && $deletedVariantIds) {
                        $newVariantIds = array_values($newVariantIds);
                        foreach ($newVariantIds as $i => $new_var_id) {
                            $index = $i + $countVarianat;
                            if (isset($_POST['variant_name'][$index], $_POST['variant_price'][$index], $_POST['variant_price_sales'][$index])) {
                                $data_variant = [
                                    'variant_name' => $_POST['variant_name'][$index],
                                    'variant_price' => $_POST['variant_price'][$index],
                                    'variant_price_sales' => $_POST['variant_price_sales'][$index],
                                    'product_id' => $product_id,
                                    'status' => STATUS_ACTIVE
                                ];
                                $result = $modelVariant->saveVariant($data_variant);
                                $listProFlashSale = $mdFlashSaleProduct->getProductFlashSaleByProductId($id);
                                if($listProFlashSale){
                                    foreach ($listProFlashSale as $ind => $pro){
                                        $data_var = [
                                            'variant_price_flash_sale' => $_POST['variant_price'][$i] - ($_POST['variant_price'][$i]*$pro['percent_flash_sale']/100),
                                            'variant_price' => $_POST['variant_price'][$i],
                                            'variant_price_sales' => $_POST['variant_price_sales'][$i],
                                            'product_id' => $product_id,
                                            'percent_flash_sale' => $pro['percent_flash_sale'],
                                            'flash_sale_id' => $pro['flash_sale_id'],
                                            'variant_id' =>$result,
                                        ];
                                        $mdFlashSaleProductVariant->saveFlashSaleProductVariant($data_var);
                                    }
                                }
                            }
                            $new_variant_ids[] = $result;
                            self::uploadVariantImages($new_variant_ids,$countFirst,$edit,$public_path);
                        }
                        if (!empty($_POST['url_image_delete'])) {
                            self::deleteImages($_POST['url_image_delete'], $modelVariantImg, $public_path);
                        }
                    }else{
                        foreach ($newVariantIds as $i => $new_var_id) {
                            if (isset($_POST['variant_name'][$i], $_POST['variant_price'][$i], $_POST['variant_price_sales'][$i])) {
                                $data_variant = [
                                    'variant_name' => $_POST['variant_name'][$i],
                                    'variant_price' => $_POST['variant_price'][$i],
                                    'variant_price_sales' => $_POST['variant_price_sales'][$i],
                                    'product_id' => $product_id,
                                    'status' => STATUS_ACTIVE
                                ];
                                $result = $modelVariant->saveVariant($data_variant);
                                $listProFlashSale = $mdFlashSaleProduct->getProductFlashSaleByProductId($id);
                                if($listProFlashSale){
                                    foreach ($listProFlashSale as $ind => $pro){
                                        $data_var = [
                                            'variant_price_flash_sale' => $_POST['variant_price'][$i] - ($_POST['variant_price'][$i]*$pro['percent_flash_sale']/100),
                                            'variant_price' => $_POST['variant_price'][$i],
                                            'variant_price_sales' => $_POST['variant_price_sales'][$i],
                                            'product_id' => $product_id,
                                            'percent_flash_sale' => $pro['percent_flash_sale'],
                                            'flash_sale_id' => $pro['flash_sale_id'],
                                            'variant_id' =>$result,
                                        ];
                                        $mdFlashSaleProductVariant->saveFlashSaleProductVariant($data_var);
                                    }
                                }
                            }
                            $new_variant_ids[] = $result;
                            self::uploadVariantImages($new_variant_ids,$countFirst,$edit,$public_path);
                        }
                    }
                    if (!empty($variantIdOlds) && $deletedVariantIds) {
                        if(($variantIdOlds[0])){
                            foreach ($variantIdOlds as $key => $var_id_old) {
                                    $index = $key ;
                                if (isset($_POST['variant_name'][$index], $_POST['variant_price'][$index], $_POST['variant_price_sales'][$index])) {
                                    $data_variant = [
                                        'variant_name' => $_POST['variant_name'][$index],
                                        'variant_price' => $_POST['variant_price'][$index],
                                        'variant_price_sales' => $_POST['variant_price_sales'][$index],
                                        'product_id' => $product_id,
                                    ];
                                    $result = $modelVariant->updateVariant($data_variant, $var_id_old);
                                }
                                $new_variant_ids[] = $var_id_old;
                                self::uploadVariantImages($new_variant_ids,0,$edit=TRUE,$public_path);
                            }
                        }else{
                            $variantIdOlds = array_values($variantIdOlds);
                            foreach ($variantIdOlds as $key => $var_id_old) {
                                if (isset($_POST['variant_name'][$key], $_POST['variant_price'][$key], $_POST['variant_price_sales'][$key])) {
                                    $data_variant = [
                                        'variant_name' => $_POST['variant_name'][$key],
                                        'variant_price' => $_POST['variant_price'][$key],
                                        'variant_price_sales' => $_POST['variant_price_sales'][$key],
                                        'product_id' => $product_id,
                                    ];
                                    $result = $modelVariant->updateVariant($data_variant, $var_id_old);
                                }
                                $new_variant_ids[] = $var_id_old;
                                self::uploadVariantImages($new_variant_ids,0,$edit=TRUE,$public_path);
                            }
                        }
                        if (isset($variantIdOlds)) {
                            self::updateProductVariant0($model, $product_id, $_POST['variant_price'][0], $_POST['variant_price_sales'][0]);
                        }
                        if (!empty($_POST['url_image_delete'])) {
                            self::deleteImages($_POST['url_image_delete'], $modelVariantImg, $public_path);
                        }
                    }else{
                        foreach ($variantIdOlds as $key => $var_id_old) {
                            if (isset($_POST['variant_name'][$key], $_POST['variant_price'][$key], $_POST['variant_price_sales'][$key])) {
                                $data_variant = [
                                    'variant_name' => $_POST['variant_name'][$key],
                                    'variant_price' => $_POST['variant_price'][$key],
                                    'variant_price_sales' => $_POST['variant_price_sales'][$key],
                                    'product_id' => $product_id,
                                ];
                                $result = $modelVariant->updateVariant($data_variant, $var_id_old);
                            }
                            $new_variant_ids[] = $var_id_old;
                            self::uploadVariantImages($new_variant_ids,0,$edit=TRUE,$public_path);
                        }
                        if (!empty($_POST['url_image_delete'])) {
                            self::deleteImages($_POST['url_image_delete'], $modelVariantImg, $public_path);
                        }
                        if (isset($variantIdOlds[0])) {
                            self::updateProductVariant0($model, $product_id, $_POST['variant_price'][0], $_POST['variant_price_sales'][0]);                      
                        }
                    }
                }else{
                    if (!empty($_POST['variant_name'])) {
                        foreach ($_POST['variant_name'] as $key => $variation) {
                            $var = [
                                'variant_name' => $variation,
                                'variant_price' => $_POST['variant_price'][$key],
                                'variant_price_sales' => $_POST['variant_price_sales'][$key],
                                'product_id' => $rs,
                                'status' => STATUS_ACTIVE
                            ];
                            $id_variant = $modelVariant->saveVariant($var);
                            $new_variant_ids[] = $id_variant;
                            self::uploadVariantImages($new_variant_ids,0,$edit, $public_path);
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
            $listCombo = $modelCombo->getAllComboProduct($info['id']);
            $this->view->listCombo = $listCombo;
        }

        $this->view->listColor = $arrColor;
        $this->view->listCategory = $listCategory;
        $this->view->info = $info;
        $this->view->error = $error;
        $this->view->id = $id;
    }

    function uploadVariantImages($new_variant_ids,$count,$edit,$public_path){
        $modelVariantImg = new VariantImage();
        $nowdir = 'img_' . date('d_m_Y');
        $variant_images = array();
        foreach ($new_variant_ids as $key => $variant_id) {

            if($edit == FALSE){
                if($count!=0){
                        $index = $key + 1;  
                }
                else{
                    $index = $key + $count;
                }
            }elseif($edit == TRUE){
                $index = $variant_id;
            }
            foreach ($_FILES['url_image']['name'][$index] as $j => $name) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $fileName = pathinfo($name, PATHINFO_FILENAME);
                $fileName = str_replace(' ', '-', $fileName);
                $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                Commons::makedirs($public_path . '/images/' . $nowdir);

                if (move_uploaded_file($_FILES['url_image']['tmp_name'][$index][$j], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                    $variant_images[] = array(
                        'product_variant_id' => $variant_id,
                        'url_image' => $nowdir . '/' . $newname,
                        'status' => STATUS_ACTIVE
                    );
                }
                
            }
        }
        if (!empty($variant_images)) {
            $modelVariantImg->saveVariantImage($variant_images);
        }
    }

    function deleteImages($imageIds, $modelVariantImg, $public_path) {
        foreach ($imageIds as $valued) {
            if ($image = $modelVariantImg->fetchVariantImageById($valued)) {
                $modelVariantImg->deleteVariantImage($valued);
                $full = $public_path . '/images/' . $image['url_image'];
                if (file_exists($full)) unlink($full);
            }
        }
    }

    function updateProductVariant0($model, $product_id, $variantPrice, $variantPriceSales) {
        $modelCombo = new ComboProduct();
        $mdComboDetail = new ComboDetail();
        if (isset($product_id)) {
            $product = $model->getProductInfoById($product_id);
            $product['price'] = $variantPrice;
            $product['price_sales'] = $variantPriceSales;
    
            $updateData = [
                'price' => $product['price'],
                'price_sales' => $product['price_sales'],
            ];
    
            $model->updateProduct($updateData, $product_id);
            $listCombo = $modelCombo->getAllComboProduct($product_id);
            if ($listCombo) {
                foreach ($listCombo as $key => $combo) {
                    if($variantPriceSales == 0 ){
                        $combo['total_price'] = $combo['total_price'] - $combo['price'] + $variantPrice;
                        $combo['total_discount'] = $combo['total_price'] - $combo['price_discount'];
                        $combo['price'] = $variantPrice;
                    }else{
                        $combo['total_price'] = $combo['total_price'] - $combo['price'] + $variantPriceSales;
                        $combo['total_discount'] = $combo['total_price'] - $combo['price_discount'];
                        $combo['price'] = $variantPriceSales;
                    }
                    $mdComboDetail->updateComboDetail(["price" => $combo['price']],$product_id);
                    $modelCombo->saveComboProduct(
                        [
                            "total_price" => $combo['total_price'],
                            "total_discount" =>  $combo['total_discount'],

                
                    ], $combo['id'] );
                }
            }
            self::updateProductFlashSale($product_id, $_POST['variant_price'][0], $_POST['variant_price_sales'][0]);
        }
    }

    function updateProductFlashSale($product_id, $price, $priceSales){
        $mdFlashSaleProduct = new FlashSaleProduct();
        $modelFlashSaleProductVariant = new FlashSaleProductVariant;
        $modelVariant = new ProductVariant();
        $listProduct = $mdFlashSaleProduct->getProductFlashSaleByProductId($product_id);
        if($listProduct){
            foreach ($listProduct as $index => &$value) {
                $value['price'] = $price;
                $value['price_sales'] = $priceSales;
                $value['price_flash_sale'] = $price - $price * $value['percent_flash_sale'] / 100;
                $mdFlashSaleProduct->updateFlashSaleProduct([
                    'price' => $value['price'],
                    'price_sales' => $value['price_sales'],
                    'price_flash_sale' => $value['price_flash_sale'],
                ],$value['flash_sale_id'],$product_id);
                
                $productVariant = $modelVariant->getProductVariants($product_id);
                if($productVariant){
                    foreach ($productVariant as $j => $variant){
                        $dataProductVariant['percent_flash_sale'] = $value['percent_flash_sale'];
                        $dataProductVariant['variant_price'] = $variant['variant_price'];
                        $dataProductVariant['variant_price_sales'] = $variant['variant_price_sales'];
                        $dataProductVariant['variant_price_flash_sale'] = $variant['variant_price']-($variant['variant_price']*($value['percent_flash_sale']/100));
                        $modelFlashSaleProductVariant->updateFlashSaleProductVariant($dataProductVariant, $value['flash_sale_id'], $product_id, $variant['id']); 
                    }
                }
            }
        }
    }
    
    // Example usage  
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
        $public_path = UPLOAD_PATH;
        $modelCombo = new ComboProduct();
        $mdComboDetail = new ComboDetail();
        $modelVariant = new ProductVariant();
        $mdFlashSaleProduct = new FlashSaleProduct();
        $mdFlashSaleProductVariant = new  FlashSaleProductVariant();
        $modelVariantImg = new VariantImage();
        if (!empty($this->post_data['id'])) {
            $model = new Product();
            $id = intval($this->post_data['id']);
            if ($id > 0) {
                $product = $model->fetchProductById($id);
                $image = $product['image'];
                $listCombo = $modelCombo->getAllComboProduct($id);
                if ($listCombo) {
                    foreach ($listCombo as $key => $combo) {
                        $combo['total_price'] = $combo['total_price'] - $product['price_sales'];
                        $combo['total_discount'] = $combo['total_price'] - $combo['price_discount'];
                        $modelCombo->saveComboProduct(
                            [
                                "total_price" => $combo['total_price'],
                                "total_discount" =>  $combo['total_discount'],
                            ], 
                            $combo['id']
                        );
                    }
                }
                $reponse = $model->deleteProduct($id);
                if ($reponse >= 0) {
                    $list = $modelVariantImg->getProductImages($id);
                    foreach ($list as $imageArray) {
                        foreach ($imageArray as $imageInfo) {
                            $url = $imageInfo['url'];
                            $imageId = $imageInfo['id'];
                            $modelVariantImg->deleteVariantImage($imageId);
                            if($url){
                                $full = $public_path . '/images/' . $url;
                                if (file_exists($full)) unlink($full);
                            }
                        }
                    }
                    self::deleteImageProduct($image,$public_path);
                    $mdComboDetail->deleteComboDetailByProductId($id);
                    $modelVariant->deleteVariantByProductId($id);
                    $mdFlashSaleProductVariant->deleteFlashSaleProductVariantByProductId($id);
                    $mdFlashSaleProduct->deleteFlashSaleProductByProductId($id);
                    $this->ajaxResponse(CODE_SUCCESS);
                }else{
                    $this->ajaxResponse(CODE_HAS_ERROR);
                }
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }

    public function deleteImageProduct($image,$public_path) {
        if($image){
            $full = $public_path . '/images' . $image;
            if (file_exists($full)) unlink($full);
        }
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
    public function getListWithProductAction() {
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
        if( empty($this->post_data["selectOrderWithProduct"]) == false){
            $selectedRelativeProduct = explode(',', $this->post_data["selectOrderWithProduct"]);
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
