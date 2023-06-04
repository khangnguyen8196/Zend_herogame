<?php

/**
 * Main page
 */
class Site_DonHangController extends FrontEndAction {

    protected $_categoryMdl = "";
    protected $_productMdl = "";
    protected $_orderMdl = "";
    
    
    protected $_exchange_rate_money_to_score = 10000;
    protected $_exchange_rate_score_to_money = 1000;
    protected $_min_price_discount = 1000000;
    protected $_min_percent_discount_over_price = 50;

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->_categoryMdl = new Category();
        $this->_productMdl = new Product();
        $this->_categoryMdl = new Category();
        $this->_orderMdl = new Order();
        
        $exr = Commons::getSettingByKey($this->setting, 'EXCHANGE_RATE');
        if( empty($exr) == false ){
            $this->_exchange_rate_score_to_money = $exr['value'];
        }
        $exc = Commons::getSettingByKey($this->setting, 'EXCHANGE_RATE_MONEY');
        if( empty($exr) == false ){
            $this->_exchange_rate_money_to_score = $exc['value'];
        }
        $mpd = Commons::getSettingByKey($this->setting, 'MIN_PRICE_TO_DISCOUNT');
        if( empty($mpd) == false ){
            $this->_min_price_discount = $mpd['value'];
        }
        $mpdo = Commons::getSettingByKey($this->setting, 'PERCENT_DISCOUNT_OVER_MAX');
        if( empty($mpdo) == false ){
            $this->_min_percent_discount_over_price = $mpdo['value'];
        }
        $this->loadJs('pages/donhang');
    }

    /**
     * My's Orders
     */
    public function indexAction() {
        $this->_redirect("/don-hang/gio-hang");
    }
    
    public function donHangCuaToiAction(){
        // get order history list
        $orderHistory = $this->_orderMdl->getOrders();
        $this->view->order_history = $orderHistory;
    }

    /**
     * Order detail
     */
    public function chiTietDonHangAction() {
        if (empty($this->post_data["orderId"]) == true) {
            $this->_redirect("/");
        }
        $orderInfo = $this->_orderMdl->getOrderDetail($this->post_data["orderId"]);
        $this->view->orderInfo = $orderInfo;
    }

    /**
     * Cart
     */
    public function gioHangAction() {

        $this->view->headTitle()->append('Giỏ hàng');
        $t = $this->token;
        //get cookie
        $cart_list = UtilSession::get($t . "_CART_LIST");
        // echo'<pre>';print_r($cart_list);exit;
        $cart_list_full_info = array();
        $totalMoney = 0;
        if (empty($cart_list) == false && is_array($cart_list)) {
            $cart_list_full_info = self::getProductsFullInfo($cart_list, $totalMoney);
        }
        $this->view->cart_list = $cart_list_full_info;
        $this->view->total_money = $totalMoney;
    }

    public function datHangAction() {
        $this->view->headTitle()->append('Đặt hàng');
        $userId = -1;
        if( empty($this->customer_info) == true ){
//            $this->_redirect("/");
//            $this->customer_info = ['user_id' => -1 ];
            $userId = $this->customer_info['user_id'];
        }
        $user = new Users();
        $this->view->user = $user->getUserById($userId);
        $t = $this->token;
        //get cookie
        $cart_list = UtilSession::get($t . "_CART_LIST");
        $cart_list_full_info = array();
        $totalMoney = 0;
        
        $cart_list_full_info = self::getProductsFullInfo($cart_list, $totalMoney);
        $this->view->cart_list = $cart_list_full_info;
        $this->view->total_money = $totalMoney;
        // echo '<pre>';print_r($cart_list_full_info);exit;
    }
    
    public function checkPromotionAction(){
        $this->isAjax();
        if( empty($this->post_data['t']) == false && empty($this->post_data['code']) == false ){
            $promotionCode = new PromotionCode();
            // check session
            $t = $this->post_data["t"];
            $cart_list = UtilSession::get($t . "_CART_LIST");
            UtilSession::set($t . "_PROMOTION", array() );
            if( empty($cart_list) == false ){
                $code = $this->post_data['code'];
                $promoInfo = $promotionCode->fetchpromotionByCode($code);
               
                if( empty($promoInfo) == false ){
                    $totalMoney = 0;
                    $check = false;
                    if (empty($cart_list) == false && is_array($cart_list)) {
                        if(isset($cart_list['products']) ) {
                            // echo '<pre>';print_r($cart_list['products']);echo '</pre>';exit;
                            foreach ($cart_list['products'] as $key => $value) {
                                $p_full_info = $this->_productMdl->getProductInfoById($key);
                    
                                if (empty($p_full_info)) {
                                    continue;
                                }
                                if( $promoInfo['category'] != 0 ){
                                    if( $p_full_info['id_category'] != $promoInfo['category']){
                                        $check = true;
                                    }
                                }
                                if (!empty($value['variant']) && is_array($value['variant'])) {
                                    $mdlProductVariant= new ProductVariant();
                                    $variant_list = $mdlProductVariant->getProductVariants($key);
                                    foreach ($value['variant'] as $i=>$var) {
                                        foreach ($variant_list as $variant) {
                                            if ($variant['id'] == $i) {

                                                $p_full_info["qty"] = $var['qty'];
                                                $p_full_info["variant_price_sales"] = $variant["variant_price_sales"];
                                                $p_full_info["total_money"] = $var['qty'] * $variant["variant_price_sales"];
                                                $totalMoney += $p_full_info["total_money"];
                                               
                                            }
                                        }
                                    }
                                }else{
                                    if( is_array($value['qty']) == true ){
                                        $p_full_info["qty"] = 0;
                                        foreach ($value['qty'] as $qkey => $qitem) {
                                            $p_full_info["qty"] += $qitem;
                                        }
        
                                    } else {
                                            $p_full_info["qty"] = $value['qty'];
                                    }
                                    $p_full_info["total_money"] = $p_full_info["qty"] * $p_full_info["price_sales"];
                                    $totalMoney += $p_full_info["total_money"];
                                }
                            }
                        }
                        if(!empty($cart_list['combos'])) {
                            foreach($cart_list['combos'] as $key => $value) {
                                $mdlCombo= new ComboProduct();
                                $combo_info = $mdlCombo->fetchComboProductById($key);
                                $combo_detail_list = $value['products'];
                                if(empty($combo_info)) {
                                    continue;
                                }
                                
                                $combo_product = array();
                                $combo_product['qty'] = $value['qty'];
                                $combo_product["price_sales"]= $combo_info["total_discount"];
                                $combo_product["total_money"] = $value['qty'] * $combo_product["price_sales"];
                                $totalMoney += $combo_product["total_money"];
                                $combo_product['products'] = array();
                                foreach($combo_detail_list as $key => $product) {
                                    $combo_detail = $this->_productMdl->getProductInfoById($key);
                                    if( $promoInfo['category'] != 0 ){
                                        if( $combo_detail['id_category'] != $promoInfo['category']){
                                            $check = true;
                                        }
                                    }
                                    $combo_detail["qty"] = $value['qty'];
                                    $combo_detail["price_sales"] = $combo_detail["price_sales"];
                                    $combo_product['products'][] = $combo_detail;
                                }
                            }
                        }
                    }
                    if( $check == true ){
                        $this->ajaxResponse(CODE_HAS_ERROR, 'Tồn Tại Sản Phẩm Không Áp Dụng Mã Giảm Giá Này!');
                    } else {
                        $cacl = ($totalMoney*$promoInfo['percent'])/100;
                        if( $cacl > $promoInfo['max_price']){
                            $cacl = $promoInfo['max_price'];
                        }
                        $rs = array('t'=> $t, 'cTotal' => $totalMoney,'cacl' => $cacl, 'caclText' => number_format($cacl), 'aTotal'=> $totalMoney - $cacl, 'aTotalText' => number_format($totalMoney - $cacl) );
                        $this->ajaxResponse(CODE_SUCCESS, 'Sử Dụng Mã Giảm Giá Thành Công!', $rs);
                    }
                } else {
                      $this->ajaxResponse(CODE_HAS_ERROR, 'Mã Giảm Giá Không Hợp Lệ!');
                }
            } else {
                $this->ajaxResponse(CODE_HAS_ERROR, 'Giỏ Hàng Rỗng!');
            }
        } 
        $this->ajaxResponse(CODE_HAS_ERROR, 'Đã Xảy Ra Lỗi Vui Lòng Thử Lại!');
    }
    public function checkDiscountAction(){
        $this->isAjax();
        if( empty($this->post_data['t']) == false && empty($this->post_data['percent']) == false ){
            // check session
            $t = $this->post_data["t"];
            $cart_list = UtilSession::get($t . "_CART_LIST");
            if( empty($cart_list) == false ){
                $scoreDiscount = $this->post_data['percent'];
                $scoreDiscount = intval(str_replace(',','',$scoreDiscount));
                if( $scoreDiscount <= 0 ){
                    $this->ajaxResponse(CODE_HAS_ERROR, 'Chiết khấu phải lớn hơn 0');
                }
                if( empty($this->customer_info) == false ){
                    $user = new Users();
                    $userInfo = $user->getUserById($this->customer_info['user_id']);
                    $score = $userInfo['score'];
                    if( $score < $scoreDiscount){
                        $this->ajaxResponse(CODE_HAS_ERROR, 'Chiết khấu phải nhỏ hơn hoặc bằng điểm bạn hiện có');
                    }
                    $totalMoney = 0;
                    if (empty($cart_list) == false && is_array($cart_list)) {
                        if(isset($cart_list['products']) ) {
                            //get product info from product id saved in cookie
                            foreach ($cart_list['products'] as $key => $value) {
                                $p_full_info = $this->_productMdl->getProductInfoById($key);
                                
                                if( empty($p_full_info) == true ){
                                    continue;
                                }
                                if (!empty($value['variant']) && is_array($value['variant'])) {
                                    $mdlProductVariant= new ProductVariant();
                                    $variant_list = $mdlProductVariant->getProductVariants($key);
                                    foreach ($value['variant'] as $i=>$var) {
                                        foreach ($variant_list as $variant) {
                                            if ($variant['id'] == $i) {

                                                $p_full_info["qty"] = $var['qty'];
                                                $p_full_info["variant_price_sales"] = $variant["variant_price_sales"];
                                                $p_full_info["total_money"] = $var['qty'] * $variant["variant_price_sales"];
                                                $totalMoney += $p_full_info["total_money"];
                                               
                                            }
                                        }
                                    }
                                } else {
                                    if( is_array($value['qty']) == true ){
                                        $p_full_info["qty"] = 0;
                                        foreach ($value['qty'] as $qkey => $qitem) {
                                            $p_full_info["qty"] += $qitem;
                                        }
                                    } else {
                                        $p_full_info["qty"] = $value['qty'];
                                    }
                                    $p_full_info["total_money"] = $value['qty'] * $p_full_info["price_sales"];
                                    $totalMoney += $p_full_info["total_money"];
                                }
                            }
                        }
                        if(isset($cart_list['combos']) ) {
                            //get product info from product id saved in cookie
                            foreach ($cart_list['combos'] as $key => $qty) {
                                $mdlCombo = new ComboProduct();
                                $combo_info = $mdlCombo->fetchComboProductById($key);
                                if( empty($combo_info) == true ){
                                    continue;
                                }
                                if( is_array($qty['qty']) == true ){
                                    $combo_info["qty"] = 0;
                                    foreach ($qty['qty'] as $qkey => $qitem) {
                                        $combo_info["qty"] += $qitem;
                                    }
                                } else {
                                    $p_full_info["qty"] = $qty['qty'];
                                }
                                $combo_info["total_money"] = $qty['qty'] * $combo_info["total_discount"];
                                $totalMoney += $combo_info["total_money"];
                            }
                        }

                        
                    }
                    $total = $totalMoney;
                    $discount = $scoreDiscount * $this->_exchange_rate_score_to_money;
                    // check tong tien > 1tr
                    if( $totalMoney >= $this->_min_price_discount){
                        // tru het
                        $totalMoney = $totalMoney - $discount;
                        if( $totalMoney < 0 ){
                            $totalMoney =  0;
                        }
                        if( $discount >= $total ){
                            $discount = $total;
                        }
                    } else {
                        if( $discount >= $totalMoney ){
                            $discount = ( $totalMoney * $this->_min_percent_discount_over_price )/100;
                            $totalMoney = $totalMoney - $discount;
                        } else {
                            $totalMoney = $totalMoney - $discount;
                        }
                    }
                    $this->ajaxResponse(CODE_SUCCESS, 
                        'Sử Dụng Chiết Khấu: '.number_format($scoreDiscount).' Điểm', 
                        array('t'=> $t, 'cTotal' => $total, 'discount' => $discount, 'discountText' => number_format($discount), 
                        'aTotal'=> $totalMoney, 'aTotalText' => number_format($totalMoney) ));
                } else {
                    $this->ajaxResponse(CODE_HAS_ERROR, 'Tài Khoản Không Tồn Tại');
                }
            } else {
                $this->ajaxResponse(CODE_HAS_ERROR, 'Giỏ Hàng Rỗng!');
            }
        } 
        $this->ajaxResponse(CODE_HAS_ERROR, 'Đã Xảy Ra Lỗi Vui Lòng Thử Lại!');
    }
    public function ketQuaAction(){
//        if( empty($this->customer_info) == true ){
//            $this->_redirect("/");
//        }
        // check user promotion code or use score
        $data = $this->post_data;
        if( empty($data['cfa_email']) == true || empty($data['cfa_name']) == true
                || empty($data['cfa_phone']) == true || empty($data['cfa_address']) == true ){
            $this->_redirect("/don-hang/dat-hang");
        }
        $userIdMine = -1;
        if ( $this->customer_info == true ) {
            $userIdMine = $this->customer_info['user_id'];
        }
        $scoreDiscount = 0;
        $dataOrder = array(
            'address' => $data['cfa_address'],
            'place' => $data['cfa_place'],
            'name' => $data['cfa_name'],
            'user_id' => $userIdMine,
            'phone' => $data['cfa_phone'],
            'email' => $data['cfa_email'],
            'created_date' => date("Y-m-d H:i:s"),
            'updated_date' => date("Y-m-d H:i:s"),
            'payment_method' => $data['pm'],
            'status' => 1,
            'is_pay' => 0,
            'note' => $data['note'],
        );
        $errors = array();
        $orderDetail = array();
        $t = $this->token;
        $cart_list = UtilSession::get($t . "_CART_LIST");
        $totalMoney = 0;
        $total = 0;
        $cacl = 0;
        $listCategory = array();
        $listProductItem = array();
        $score = 0;
        if( $this->customer_info == true ){
            $user = new Users();
            $userInfo = $user->getUserById($this->customer_info['user_id']);
            $score = $userInfo['score'];
        }
        $mdlVariant = new ProductVariant();
        $productMdl = new Product();
        $mdlCombo = new ComboProduct();
        if (empty($cart_list) == false && is_array($cart_list)) {
                if(empty($cart_list['products'])==false){
                    foreach ($cart_list['products'] as $p_id => $value) {
                        $p_full_info = $productMdl->getProductInfoById($p_id);
                        $variant_list = $mdlVariant->getProductVariants($p_id);
                        
                        if (empty($p_full_info)) {
                            continue;
                        }
                        if (!empty($value['variant']) && is_array($value['variant'])) {
                            foreach ($value['variant'] as $i=>$var) {
                                foreach ($variant_list as $variant) {
                                    if ($variant['id'] == $i) {
                                        $detailItem = array('id_order' =>'','id_product' => $p_id, 'price' =>$variant['variant_price_sales'], 'number' => $var['qty'],'product_variant' => $variant['id']);
                                        $listItem = array('name' => $p_full_info['title'],'price' => $variant['variant_price_sales'], 'number'=> $var['qty'], 'img' => $p_full_info['image'], 'variant' => $variant['variant_name'],'variant_id'  => $variant['id'] );
                                        $listProductItem[] = $listItem;
                                        $orderDetail[] = $detailItem;
                                        $p_full_info["total_money"] = $var['qty'] * $variant["variant_price_sales"];
                                        $totalMoney += $p_full_info["total_money"];
                                        $cart_list_full_info[] = $p_full_info;
                                        $listCategory[] = $p_full_info['id_category'];
                                    }
                                }
                            }
                            
                        }else{
                            // order detail item
                            $detailItem = array('id_order' =>'','id_product' => $p_id, 'price' => $p_full_info['price_sales'], 'number' => $value['qty'], 'product_color' => 1);
                            $listItem = array('name' => $p_full_info['title'],'price' => $p_full_info['price_sales'], 'number'=> $value['qty'], 'img' => $p_full_info['image']);
                            $listProductItem[] = $listItem;
                            $orderDetail[] = $detailItem;
                            $listCategory[] = $p_full_info['id_category'];
                            $p_full_info["qty"] = $value['qty'];
                            $p_full_info["total_money"] = $value['qty'] * $p_full_info["price_sales"];
                            $totalMoney = $totalMoney + $p_full_info["total_money"];
                        }
                    }  
                }
                if(!empty($cart_list['combos'])) {
                    foreach($cart_list['combos'] as $c_id => $value) {
                        $combo_info = $mdlCombo->fetchComboProductById($c_id);
                        
                        $combo_detail_list = $value['products'];
                        if(empty($combo_info)) {
                            continue;
                        }
                        
                        $detailItem = array('id_order' =>'','id_product' => '', 'price' => $combo_info["total_discount"], 'number' => $value['qty'],'product_variant' => '', 'combo_id'=>$combo_info['id']);
                        $listItem = array('name' => $combo_info['title'],'price' => $combo_info["total_discount"], 'number'=> $value['qty'], 'img' => $combo_info['image_cb'],'comb_id'  => $combo_info['id'] );
                        $listProductItem[] = $listItem;
                        $orderDetail[] = $detailItem;

                        $combo_product = array();
                        $combo_product['id'] = $combo_info['id']; 
                        $combo_product['title'] = $combo_info['title'];
                        $combo_product['qty'] = $value['qty'];
                        $combo_product['image_cb'] = $combo_info['image_cb'];
                        $combo_product["price_sales"]= $combo_info["total_discount"];
                        $combo_product["total_money"] = $value['qty'] * $combo_product["price_sales"];
                        $totalMoney += $combo_product["total_money"];
                        
                        $combo_product['products'] = array();
                        foreach($combo_detail_list as $p_id => $product) {
                            $combo_detail = $productMdl->getProductInfoById($p_id);
                            $combo_detail["qty"] = $value['qty'];
                            $combo_detail["price_sales"] = $combo_detail["price_sales"];
                            $combo_detail["title"] = $combo_detail['title'];
                            $combo_detail["combo_id"] = $combo_detail["combo_id"];
                            $combo_detail["total_money"] = $value['qty'] * $combo_detail["price_sales"];
                            $combo_product['products'][] = $combo_detail;
                        }
                    }
                }
            } else {
            $this->_redirect("/don-hang/gio-hang");
        }
        $total = $totalMoney;
        if( @$data['checkPromotion'] == 'true'){
            $promotionCode = new PromotionCode();
            $promoInfo = $promotionCode->fetchpromotionByCode($data['promotionCode']);
            if( empty($promoInfo) == false ){
                $check = true;
                if ($promoInfo['category'] != 0) {
                    if ($p_full_info['id_category'] != $promoInfo['category']) {
                        $check = true;
                    }
                    
                    foreach ( $listCategory as $key => $value){
                        if( $value != $promoInfo['category'] ){
                            $errors[] = 'Thanh Toán không thành công. Mã giảm giá không hợp lệ!';
                            $check = false;
                            break;
                        }
                    }
                }
                if( $check === true ){
                    $cacl = ( $totalMoney * $promoInfo['percent']) / 100;
                    if ($cacl > $promoInfo['max_price']) {
                        $cacl = $promoInfo['max_price'];
                    }
                    $totalMoney = $totalMoney - $cacl;
                    $dataOrder['promotion_code'] = $data['promotionCode'];
                }
            }
        } else if( @$data['checkdisCount'] == 'true' && $this->customer_info == true ){
            $scoreDiscount = $data['discount'];
            $scoreDiscount = intval(str_replace(',','',$scoreDiscount));
            if( $scoreDiscount <= 0 ){
                $errors[] = 'Thanh Toán không thành công. Chiết khấu phải lớn hơn 0';
            }
            if( $score < $scoreDiscount){
                $errors[] = 'Thanh Toán không thành công. Chiết khấu phải nhỏ hơn hoặc bằng điểm bạn hiện có';
            }
            if( empty($errors) == true ){
                $discount = $scoreDiscount * $this->_exchange_rate_score_to_money;
                // check tong tien > 1tr
                if( $totalMoney >= $this->_min_price_discount){
                    // tru het
                    $totalMoney = $totalMoney - $discount;
                    if( $totalMoney < 0 ){
                        $totalMoney =  0;
                    }
                    if( $discount >= $totalMoney ){
                        $discount = $totalMoney;
                    }
                } else {
                    if( $discount >= $totalMoney ){
                        $discount = ( $totalMoney * $this->_min_percent_discount_over_price )/100;
                        $totalMoney = $totalMoney - $discount;
                    } else {
                        $totalMoney = $totalMoney - $discount;
                    }
                }
                
                $dataOrder['discount'] = $discount;
            }
            
        }
        $dataOrder['total'] = $totalMoney;
        if( $this->customer_info == true ){
            $dataOrder['score'] =  floor($totalMoney/$this->_exchange_rate_money_to_score); 
        } else {
            $dataOrder['score'] = '';
        }
        $dataOrder['order_code'] = uniqid();
        
        $order = new Order();
        $detailOfOrder = new OrderDetail();
        if( empty( $errors) == true ){
            $rs = $order->saveOrder($dataOrder);
            if( $rs > 0 ){
                UtilSession::set($t . "_CART_LIST", array());

                if( $this->customer_info == true ){
                    $currentScore = ( $score - $scoreDiscount );
                    $user->updateUser(array('score' => $currentScore), $this->customer_info['user_id']);
                    //get user again after update score
                    //update customer info session
                    $uInfo = $user->getUserById($this->customer_info["user_id"]);
                    UtilAuth::setCustommerLoginInfo($uInfo);
                    $this->customer_info = UtilAuth::getCustommerLoginInfo(true);
                }
                
                // save order detail
                foreach ($orderDetail as $key => $value) {
                    $value['id_order'] = $rs;
                    $detailOfOrder->saveOrderDetail($value);
                }
                $this->view->orderCode = $dataOrder['order_code'];
                $this->view->status = 1;
                $this->view->email = $data['cfa_email'];
                $devi = $cacl;
                if( $devi == 0 ){
                    $devi = $dataOrder['discount'];
                }
                $this->sendMailTemplate( $listProductItem, $dataOrder, $total, $devi );
            } else {
                $this->view->status = 0;
                $this->view->error = $errors;
            }
        } else {
            $this->view->status = 0;
            $this->view->error = $errors;
        }
    }
    public function thongTinDonHangAction(){
        $this->view->headTitle()->append('Thông tin đơn hàng');
        if( empty($this->post_data['order-code']) == false && empty($this->customer_info['user_id']) == false ){
            $order = new Order();
            $orderInfo = $order->getOrderByCodeAndUser($this->post_data['order-code'], $this->customer_info['user_id']);
            if( empty( $orderInfo) == false ){
                $this->view->order = $orderInfo;
                $orderDetail = new OrderDetail();
                $modelComboDetail = new ComboDetail();
                $list = $orderDetail->getListOrderDetail($orderInfo['id']);
                $this->view->listproduct = $list;

                $listCombo = array();
                if (!empty($list)) {
                    foreach ($list as $key => $value) {
                        if (!empty($value['combo_id']) && $value['combo_id'] != 0) {
                            $listProducts = $modelComboDetail->getProductByComboId($value['combo_id']);
                            if (!empty($listProducts)) {
                                foreach ($listProducts as $product) {
                                    $listCombo[$value['combo_id']][] = $product;
                                }
                            }
                        }
                    }
                }
                $this->view->listCombo = $listCombo;
            } else {
                $this->_redirect("/");
            }
        } else {
            $this->_redirect("/");
        }
    }
    public function sendMailTemplate( $listProduct, $orderInfo, $total, $devi ){
        $this->view->listProduct = $listProduct;
        $this->view->orderInfo = $orderInfo;
        $this->view->totalBeforedev = $total;
        $this->view->devi = $devi;
        $tpl = $this->view->render('/don-hang/_tpl-mail.phtml');
        if( empty($orderInfo['email']) == false ){
            UtilEmail::sendMail(DEFAULT_EMAIL, $orderInfo['email'], 'Herogame xác nhận đơn hàng', $tpl );
        }
    }
    public function rejectOrderAction(){
        $this->isAjax();
        if( empty($this->customer_info['user_id']) == false  && empty($this->post_data['code']) == false ){
            $order = new Order();
            $info = $order->getOrderByCodeAndUser($this->post_data['code'],$this->customer_info['user_id']);
            if( empty($info) == false ){
                $id = $info['id'];
                $rs = $order->saveOrder(array('status' => 5 ),$id );
                if( $rs >= 0 ){
                    $user = new Users();
                    $userInfo = $user->getUserById($this->customer_info['user_id']);
                    if( empty($userInfo) == false ){
                        $score = $userInfo['score'];
                        $discount = $info['discount']/$this->_exchange_rate_score_to_money;
                        $scoreUpdate = $score + $discount;
                        $user->updateUser(array('score' => $scoreUpdate), $this->customer_info['user_id']);
                    }
                    $this->ajaxResponse(CODE_SUCCESS, 'Bạn đã hủy hóa đơn thành công');
                } else {
                    $this->ajaxResponse(CODE_HAS_ERROR, 'Bạn đã hủy hóa đơn thất bại');
                }
            } else {
                $this->ajaxResponse(CODE_HAS_ERROR, 'Mã hóa đơn không hợp lệ!');
            }
        } else {
            $this->ajaxResponse(CODE_HAS_ERROR, 'Vui lòng thử lại!');
        }
    }
    public function lichSuMuaHangAction(){
        $this->view->headTitle()->append('Lịch sử mua hàng');
        if( empty($this->customer_info['user_id']) == false ){
            $order = new Order();
            $orderInfo = $order->fetchOrderByUser( $this->customer_info['user_id']);
            $this->view->order = $orderInfo;
        } else {
            $this->_redirect("/");
        }
    }
    /**
     * 
     *  total 1.000.000
     * MGG 10%
     * DISCOUNT 100k
     */

     public function themVaoGioHangComboAction() {
        $this->isAjax();
    
        if (empty($this->post_data["t"]) || empty($this->post_data["cid"]) || empty($this->post_data["qty"])) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thêm combo sản phẩm vào Giỏ Hàng không thành công!");
        }
    
        $t = $this->post_data["t"];
        $cid = intval($this->post_data["cid"]);
        $qty = intval($this->post_data["qty"]);
        $cart_list = UtilSession::get($t . "_CART_LIST");

        
        // add combo to cart
        $mdlCombo = new ComboProduct();
        $mdlComboDetail = new ComboDetail();
        $comboInfo = $mdlCombo->fetchComboProductById($cid);
        $combo_products = $mdlComboDetail->getProductByComboId($cid);
        $combo = array();
    
        if (!empty($cart_list["combos"]) && !empty($cart_list["combos"][$cid])) {
            $combo = $cart_list["combos"][$cid];
            $combo["qty"] += $qty;
            foreach ($combo_products as $combo_product) {
                $pid = $combo_product['product_id'];
                $variant_id = '';
        
                if (isset($this->post_data["variant"][$pid])) {
                    $variant_id = $this->post_data["variant"][$pid];
                }
        
                if (isset($combo["products"][$pid][$variant_id])) {
                    $combo["products"][$pid][$variant_id]["qty"] += $qty;
                } else {
                    $combo["products"][$pid][$variant_id] = array(
                        "id"=>$variant_id,
                        "qty" => $qty
                    );
                }
            }
        }else {
            $combo["combo_id"] = $cid;
            $combo["qty"] = $qty;
            $combo["products"] = array(
               
            );
        
            foreach ($combo_products as $combo_product) {
                $pid = $combo_product['product_id'];
                $variant_id = '';
        
                if (isset($this->post_data["variant"][$pid])) {
                    $variant_id = $this->post_data["variant"][$pid];
                }
        
                // get product detail
                $mdlProduct = new Product();
                $productInfo = $mdlProduct->getProductInfoById($pid);
        
                if (empty($productInfo)) {
                    $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công! Không tìm thấy thông tin sản phẩm");
                } else if ($productInfo["status"] == 2) {
                    $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công! Sản phẩm đã hết hàng");
                }
        
                $mdlVariant = new ProductVariant();
                if (empty($variant_id)) {
                        $list_variant = $mdlVariant->getProductVariants($productInfo['id']);
                        if (!empty($list_variant[0]['id'])) {
                            $variant_id = $list_variant[0]['id'];
                        }
                }
            
                if(empty($variant_id)) {
                    $product = array(
                        "id" => $pid,
                        "combo_id" => $cid,
                        'qty' => $qty,
                    );
                }else{
                    $product = array(
                        "id" => $pid,
                        "combo_id" => $cid,
                        'qty' => $qty,
                        "variant" =>[
                            "id"=>$variant_id,
                            'qty' => $qty
                        ] 
                    );
                }
                $combo["products"][$pid] =  $product;
                // if (empty($variant_id)) {
                //     $combo["products"][$pid] = array(
                //         "id" => $pid,
                //         "qty" => $qty
                //     );
                // } else {
                //     // add product with variant to combo
                //     if (isset($combo["products"][$pid][$variant_id])) {
                //         $combo["products"][$pid][$variant_id]["qty"] += $qty;
                //     } else {
                //         $combo["products"][$pid][$variant_id] = array(
                //             "id" => $variant_id,
                //             "qty" => $qty
                //         );
                //     }
                // }
            }
        }
    
        $cart_list["combos"][$cid] = $combo;
        
    
        // update cart list
        UtilSession::set($t . "_CART_LIST", $cart_list);
        $countItem = Commons::countItemInCart($cart_list);
    
        // return success response
        $this->ajaxResponse(CODE_SUCCESS, '', array(
                            "t" => $t,
                            "cart" => $cart_list,
                            "item_count" => $countItem,
                            "combo_title" => $comboInfo['title'],
        ));
    }
       
    public function themVaoGioHangAction()
    {
        $this->isAjax();
        if (empty($this->post_data["t"]) || empty($this->post_data["pid"]) || empty($this->post_data["qty"])) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công!");
        }

        $t = $this->post_data["t"];
        $variant_id = isset($this->post_data["variant"]) ? intval($this->post_data["variant"]) : null;
        $qty = intval($this->post_data["qty"]);
        $pid = intval($this->post_data["pid"]);
        $cart_list = UtilSession::get($t . "_CART_LIST");
        $product = array();
        $mdlProduct = new Product();
        $mdlVariant = new ProductVariant();
        if (!empty($cart_list["products"]) && !empty($cart_list["products"][$pid])) {
            $product = $cart_list["products"][$pid];
            $variant_name = "";
            if(empty($variant_id)){
                $list_variant = $mdlVariant->getProductVariants($pid);
                if (!empty($list_variant[0]['id'])) {
                    $variant_id = $list_variant[0]['id'];
                    $variant_name=  $list_variant[0]['variant_name'];

                    $product["variant"][$variant_id] = array(
                        "id" => $variant_id,
                        "qty" => $product["variant"][$variant_id]["qty"],
                        "variant_name" => $variant_name
                    );
                }else{
                    $product = array(
                        "id" => $pid,
                        "qty" =>  $product["qty"],
                    );
                }
            }
            if ($variant_id && isset($product["variant"][$variant_id])) {
                $product["variant"][$variant_id]["qty"] += $qty;
                $variant_info = $mdlVariant->fetchVariantById($variant_id);
                if (!empty($variant_info)) {
                    $variant_name = $variant_info['variant_name'];
                }
            
            }else if ($variant_id && !isset($product["variant"][$variant_id])){
                if (isset($variant_id)) {
                    $mdlVariant = new ProductVariant();
                    $variant_info = $mdlVariant->fetchVariantById($variant_id);
                    if (!empty($variant_info)) {
                        $variant_name = $variant_info['variant_name'];
                    }
                }
                $product["variant"][$variant_id] = array(
                    "id" => $variant_id,
                    "qty" => $qty,
                    "variant_name" => $variant_name
                );
                
            }

            $product["qty"] += $qty;
            $productInfo = $mdlProduct->getProductInfoById($pid);
        } else {
            // get product detail
            $productInfo = $mdlProduct->getProductInfoById($pid);

            if (empty($productInfo)) {
                $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công! Không tìm thấy thông tin sản phẩm");
            } else if ($productInfo["status"] == 2) {
                $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công! Sản phẩm đã hết hàng");
            }
            if(empty($variant_id)) {
                $list_variant = $mdlVariant->getProductVariants($pid);
                if (!empty($list_variant[0]['id'])) {
                    $variant_id = $list_variant[0]['id'];
                    $variant_name=  $list_variant[0]['variant_name'];
                    $product["variant"][$variant_id] = array(
                        "id" => $variant_id,
                        "qty" => $qty,
                        "variant_name" => $variant_name
                    );
                }
                else{
                    $product = array(
                        "id" => $pid,
                        "qty" => $qty,
                    );
                }
            }
            
            $variant_name = "";
            if ($variant_id) {
                $variant_info = $mdlVariant->fetchVariantById($variant_id);
                if (!empty($variant_info)) {
                    $variant_name = $variant_info['variant_name'];
                }
            }
            $product = array(
                "id" => $pid,
                "qty" => $qty,
                "variant" => array()
            );
            if ($variant_id) {
                $product["variant"][$variant_id] = array(
                    "id" => $variant_id,
                    "qty" => $qty,
                    "variant_name" => $variant_name
                );
            }
        }

        $cart_list["products"][$pid] = $product;

        // update cart list
        UtilSession::set($t . "_CART_LIST", $cart_list);
        $countItem = Commons::countItemInCart($cart_list);
    
        $this->ajaxResponse(CODE_SUCCESS, '',
                        array("t" => $t, 
                            "cart" => $cart_list, 
                            "item_count" => $countItem, 
                            "product_title" => $productInfo["title"],
                            "variant_name" => isset($variant_name)?$variant_name:'Mặc định',
            )
        );
    
    }

    
            
    
    
    
    public function danhSachSanPhamAction() {
        $this->isAjax();
        if (empty($this->post_data["t"]) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Lấy danh sách sản phẩm không thành công!");
        }
        $t = $this->post_data["t"];
        $cart_list = UtilSession::get($t . "_CART_LIST");
        
        $cart_list_full_info = array();
        $totalMoney = 0;
        if (empty($cart_list) == false && is_array($cart_list)) {
            //get product info from product id saved in cookie
            $cart_list_full_info = self::getProductsFullInfo($cart_list, $totalMoney);
        }
        $this->view->cart_list_full_info = $cart_list_full_info;
        $html = $this->view->render("/don-hang/_danh-sach-san-pham.phtml");
        $this->ajaxResponse(CODE_SUCCESS, "", $html);
    }

    /**
     * 
     */
    public function xoaSanPhamAction() {
        $this->isAjax();
        if (empty($this->post_data["t"]) == true || empty($this->post_data["pid"]) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Xóa sản phẩm không thành công!");
        }
        $t = $this->post_data["t"];
        $cart_list = UtilSession::get($t . "_CART_LIST");
        if(empty($this->post_data["variantid"])){
            unset($cart_list["products"][$this->post_data["pid"]]);
            unset($cart_list["combos"][$this->post_data["pid"]]);
        }else{
            unset($cart_list["products"][$this->post_data["pid"]]['variant'][$this->post_data["variantid"]]);
            $total_qty = 0;
            if (isset($cart_list['products'][$this->post_data["pid"]]['variant'])) {
                foreach ($cart_list['products'][$this->post_data["pid"]]['variant'] as $variant) {
                    if (isset($variant['qty'])) {
                    $total_qty += intval($variant['qty']);
                    }
                }
            }
            $cart_list['products'][$this->post_data["pid"]]['qty'] =$total_qty;
            if (count($cart_list["products"][$this->post_data["pid"]]['variant']) == 0) {
                unset($cart_list["products"][$this->post_data["pid"]]);
            }
            if( count($cart_list[$this->post_data["pid"]]['qty']) == 0 ){
                unset($cart_list[$this->post_data["pid"]]);
            }
           
        }
        UtilSession::set($t . "_CART_LIST", $cart_list);

        $this->ajaxResponse(CODE_SUCCESS);
    }
    /**
     * 
     */
   
    public function capNhatDonHangAction() {
        $this->isAjax();
    
        if (empty($this->post_data["t"]) == true 
        || empty($this->post_data["data"]) == true 
        || (empty($this->post_data["data"]) == false && is_array($this->post_data["data"]) == false )) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Cập nhật số lượng sản phẩm không thành công!");
        }
    
        $t = $this->post_data["t"];
        $cart_list = UtilSession::get($t . "_CART_LIST"); 
        if (empty($cart_list) || !is_array($cart_list)) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Danh sách sản phẩm trong giỏ hàng không hợp lệ!");
        }
        if(!empty($this->post_data["data"]) && is_array($this->post_data["data"])){
            if(empty($cart_list['products'])==false){
                foreach ($this->post_data["data"] as $key => $qty) {
                    $exploded_data = explode('|', $key);
                    $product_id = $exploded_data[0];
                    $variant_id = $exploded_data[1];
                    if (!empty($variant_id)) {
                        $cart_list['products'][$product_id]['variant'][$variant_id]['qty'] = $qty;
                        $total_qty = 0;
                        if (isset($cart_list['products'][$product_id]['variant'])) {
                            foreach ($cart_list['products'][$product_id]['variant'] as $variant) {
                                if (isset($variant['qty'])) {
                                $total_qty += intval($variant['qty']);
                                }
                            }
                        }
                        $cart_list['products'][$product_id]['qty'] =$total_qty;
                    } else {
                        $cart_list['products'][$product_id]['qty'] = $qty;
                    }
                }
            }
        }          
        UtilSession::set($t . "_CART_LIST", $cart_list);
    
        $this->ajaxResponse(CODE_SUCCESS);
    }
    public function capNhatDonHangComboAction() {
        $this->isAjax();
    
        if (empty($this->post_data["t"]) == true 
        || empty($this->post_data["data"]) == true 
        || (empty($this->post_data["data"]) == false && is_array($this->post_data["data"]) == false )) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Cập nhật số lượng sản phẩm không thành công!");
        }
    
        $t = $this->post_data["t"];
        $cart_list = UtilSession::get($t . "_CART_LIST"); 
        if (empty($cart_list) || !is_array($cart_list)) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Danh sách sản phẩm trong giỏ hàng không hợp lệ!");
        }
        if(!empty($this->post_data["data"]) && is_array($this->post_data["data"])){
            if (!empty($cart_list['combos'])) {
                    foreach ($this->post_data["data"] as $key => $qty) {
                        $exploded_data = explode('|', $key);
                        $combo_id = $exploded_data[0];
                        if(!empty($cart_list['combos'][$combo_id])){
                            $cart_list['combos'][$combo_id]['qty'] =$qty;
                            foreach($cart_list['combos'][$combo_id]['products'] as $product_id =>$product){
                                $cart_list['combos'][$combo_id]['products'][$product_id]['qty'] =$qty;
                                $cart_list['combos'][$combo_id]['products'][$product_id]['variant']['qty'] =$qty;
                            }
                        }
                    }
            }
        }       
        
        UtilSession::set($t . "_CART_LIST", $cart_list);
    
        $this->ajaxResponse(CODE_SUCCESS);
    }
    
    
    
    
    
    
}
