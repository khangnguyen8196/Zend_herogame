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
                        //get product info from product id saved in cookie
                        foreach ($cart_list as $key => $qty) {
                            $p_full_info = $this->_productMdl->getProductInfoById($key);
                            if( empty($p_full_info) == true ){
                                continue;
                            }
                            if( $promoInfo['category'] != 0 ){
                                if( $p_full_info['id_category'] != $promoInfo['category']){
                                    $check = true;
                                }
                            }
                            if( is_array($qty) == true ){
                                $p_full_info["qty"] = 0;
                                foreach ($qty as $qkey => $qitem) {
                                    $p_full_info["qty"] += $qitem;
                                }

                            } else {
                                 $p_full_info["qty"] = $qty;
                            }
                            $p_full_info["total_money"] = $p_full_info["qty"] * $p_full_info["price_sales"];
                            $totalMoney = $totalMoney + $p_full_info["total_money"];
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
                        //get product info from product id saved in cookie
                        foreach ($cart_list as $key => $qty) {
                            $p_full_info = $this->_productMdl->getProductInfoById($key);
                            if( empty($p_full_info) == true ){
                                continue;
                            }
                            if( is_array($qty) == true ){
                                $p_full_info["qty"] = 0;
                                foreach ($qty as $qkey => $qitem) {
                                    $p_full_info["qty"] += $qitem;
                                }
                            } else {
                                $p_full_info["qty"] = $qty;
                            }
                            $p_full_info["total_money"] = $p_full_info["qty"] * $p_full_info["price_sales"];
                            $totalMoney = $totalMoney + $p_full_info["total_money"];
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
            'note' => $data['note']
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
        $mdlProductColor = new ProductColor();
        if (empty($cart_list) == false && is_array($cart_list)) {
            //get product info from product id saved in cookie
           
            foreach ($cart_list as $p_id => $value) {
                $p_full_info = $this->_productMdl->getProductInfoById($p_id);
                if (empty($p_full_info) == true) {
                    continue;
                }

                if (empty($value) == false && is_array($value)) {
                    foreach($value as $color_id => $qty){
                        $color = $mdlProductColor->fetchColorById($color_id);
                        // order detail item
                        $detailItem = array('id_order' =>'','id_product' => $p_id, 'price' => $p_full_info['price_sales'], 'number' => $qty, 'product_color' => $color_id);
                        $listItem = array('name' => $p_full_info['title'],'price' => $p_full_info['price_sales'], 'number'=> $qty, 'img' => $p_full_info['image'], 'color' => $color['color_name'],'color_id'  => $color_id );
                        $listProductItem[] = $listItem;
                        $orderDetail[] = $detailItem;
                        $listCategory[] = $p_full_info['id_category'];
                        $p_full_info["qty"] = $qty;
                        $p_full_info["total_money"] = $qty * $p_full_info["price_sales"];
                        $totalMoney = $totalMoney + $p_full_info["total_money"];
                    }
                }else{
                    // order detail item
                    $detailItem = array('id_order' =>'','id_product' => $p_id, 'price' => $p_full_info['price_sales'], 'number' => $value, 'product_color' => 1);
                    $listItem = array('name' => $p_full_info['title'],'price' => $p_full_info['price_sales'], 'number'=> $value, 'img' => $p_full_info['image'], 'color' =>'');
                    $listProductItem[] = $listItem;
                    $orderDetail[] = $detailItem;
                    $listCategory[] = $p_full_info['id_category'];
                    $p_full_info["qty"] = $value;
                    $p_full_info["total_money"] = $value * $p_full_info["price_sales"];
                    $totalMoney = $totalMoney + $p_full_info["total_money"];
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
                        if( $value != $promoInfo ){
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
                $list = $orderDetail->getListOrderDetail($orderInfo['id']);
                $this->view->listproduct = $list;
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
    public function themVaoGioHangAction() {
        $this->isAjax();
        if (empty($this->post_data["pid"]) == true || empty($this->post_data["qty"]) == true || empty($this->post_data["t"]) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công!");
        } else if ((empty($this->post_data["pid"]) == false && is_numeric($this->post_data["pid"]) == false) || (empty($this->post_data["qty"]) == false && is_numeric($this->post_data["qty"]) == false)) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công!");
        }
        $t = $this->post_data["t"];
        $cart_list = UtilSession::get($t . "_CART_LIST");

        $qty = intval($this->post_data["qty"]);
        $pid = intval($this->post_data["pid"]);

        $color_id = '';
        if(empty($this->post_data["color"]) == false){
            $color_id = $this->post_data["color"];
        }

        //get product detail
        $mdlProduct = new Product();
        $productInfo = $mdlProduct->getProductInfoById($pid);
        
        if(empty($productInfo) == true ){
            $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công! Không tìm thấy thông tin sản phẩm");
        }else {
            if($productInfo["status"] == 2 ){
                $this->ajaxResponse(CODE_HAS_ERROR, "Thêm sản phẩm vào Giỏ Hàng không thành công! Sản phẩm đã hết hàng");
            }
        }
        
        if( empty($color_id) ){
            $color_list = Commons::getProductColor($productInfo['product_color']);
            if(empty($color_list[0]['id']) == false){
                $color_id = $color_list[0]['id'];
            }
        }
        
        if (empty($cart_list) == false) {
            if(empty($color_id)){
                if (empty($cart_list[$pid]) == false) {
                    $qty = $qty + $cart_list[$pid];
                }
                $cart_list[$pid] = $qty;
            }else{
                if (empty($cart_list[$pid][$color_id]) == false) {
                    $qty = $qty + $cart_list[$pid][$color_id];
                }
                $cart_list[$pid][$color_id] = $qty;
            }
            
        } else {
            if(empty($color_id)){
                $cart_list[$pid] = $qty;
            }else{
                $cart_list[$pid][$color_id] = $qty;
            }
        }

        // update cart list
        UtilSession::set($t . "_CART_LIST", $cart_list);
        $countItem = Commons::countItemInCart($cart_list);
        $color_name = "";
        if( empty($color_id) == false){
            $mdlProductColor = new ProductColor();
            $color_info = $mdlProductColor->fetchColorById($color_id);
            if( empty($color_info) == false ){
                $color_name = $color_info['color_name'];
            }
        }
        $this->ajaxResponse(CODE_SUCCESS, '',
                    array("t" => $t, 
                        "cart" => $cart_list, 
                        "item_count" => $countItem, 
                        "product_title" => $productInfo["title"],
                        "color_name" => $color_name
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
         
        if(empty($this->post_data["colorid"])){
            unset($cart_list[$this->post_data["pid"]]);
        }else{
            unset($cart_list[$this->post_data["pid"]][$this->post_data["colorid"]]);
            if( count($cart_list[$this->post_data["pid"]]) == 0 ){
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
        $data = array();

        if(empty($this->post_data["data"]) == false && is_array($this->post_data["data"])){
            foreach($this->post_data["data"] as $key => $qty){
                $exploded_data = explode('|', $key);
                if(empty($exploded_data[1]) == false){
                    $data[$exploded_data[0]][$exploded_data[1]] = $qty;
                }else{
                    $data[$exploded_data[0]] = $qty;
                }
            }
        }
        $cart_list = $data;
        UtilSession::set($t . "_CART_LIST", $cart_list);
        
        $this->ajaxResponse(CODE_SUCCESS);
    }
}
