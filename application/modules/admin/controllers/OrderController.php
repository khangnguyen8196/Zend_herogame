<?php

/**
 * Category
 */
class Admin_OrderController extends FrontBaseAction {
	protected $_exchange_rate_score_to_money = 1000;
    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
//         $this->hasViewPermission();
        $this->loadJs('order');
        $modelUser = new Users();
        
        $listUser = $modelUser->fetchAllUsers();
        $this->view->listUser = $listUser;
        $exr = Commons::getSettingByKey($this->setting, 'EXCHANGE_RATE');
        if( empty($exr) == false ){
        	$this->_exchange_rate_score_to_money = $exr['value'];
        }
    }

    /**
     * Search page
     */
    public function indexAction() {
        
    }
    public function listProductAction(){
    	$this->isAjax();
    	$page = 1;
    	if( empty($this->post_data['page']) == false ){
    		$page = $this->post_data['page'];
    	}
    	$model = new Product();
    	$max_product = 30;
    	$data['length'] = $max_product;
    	$data['q'] = $this->post_data['q'];
    	if($page > 1 ){
    		$page = $page - 1;
    		$data['start'] = $page*$max_product;
    	}
    	$data['count_only'] = 1;
    	$count = $model->searchAllProduct($data);
    	unset($data['count_only']);
    	$list = $model->searchAllProduct($data);
    	$res = array(
    			'total_count' => $count,
    			'items' => $list,
    			'incomplete_results' => false
    	);
    	if( empty($list) == true ){
    		$res['incomplete_results'] = true;
    	}
    	echo json_encode($res);exit;
    }
    public function listUserAction(){
    	$this->isAjax();
    	$page = 1;
    	if( empty($this->post_data['pages']) == false ){
    		$page = $this->post_data['pages'];
    	}
    	$model = new Users();
    	$max = 30;
    	$data['length'] = $max;
    	$data['q'] = $this->post_data['q'];
    	if($page > 1 ){
    		$page = $page - 1;
    		$data['start'] = $page*$max;
    	}
    	$data['count_only'] = 1;
    	$count = $model->searchAllUserCustomer($data);
    	unset($data['count_only']);
    	$list = $model->searchAllUserCustomer($data);
    	$res = array(
    			'total_count' => $count,
    			'list' => $list,
    			'incomplete_results' => false
    	);
    	if( empty($list) == true ){
    		$res['incomplete_results'] = true;
    	}
    	echo json_encode($res);exit;
    }
    /**
     * 
     */
    public function detailAction() {
    	$this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_order.css"));
        $models = new Order();
        //get parent menu
        //
        $info = array();
        $error = array();
        $id = 0;
        
        $modelProduct = new Product();
        $listProduct = $modelProduct->fetchAllProduct();
        $this->view->listProduct = $listProduct;
        $order = new Order();
        $orderDetailModel = new OrderDetail();
        $modelUser = new Users();
        // get post card information if there is postcard'id available
        if (empty($this->post_data ['id']) == false) {
            $id = intval($this->post_data ['id']);
            $info = $models->fetchOrderById($id);
            if (empty($info) == true) {
                $this->_redirect('/admin/' . $this->controller);
            }
            $listOrderDetail = $orderDetailModel->getListOrderDetail($id);
            if( empty($listOrderDetail) == false ){
            	$info['order_detail'] = $listOrderDetail;
            	$total = 0;
            	foreach ( $listOrderDetail as $key => $value ){
            		$total+= ($value['price']* $value['number']);
            	}
            	$info['total_before_sale'] = $total;
            	if( $info['user_id'] != -1 ){
	            	$userInfo = $modelUser->getUserById($info['user_id']);
	            	$info['user_name'] = $userInfo['user_name'];
            	} else {
            		$info['user_name'] = 'Khách Mới';
            	}
            }
        }
        // check request is POST or GET
        if ($this->request->isPost()) {
            $xml = APPLICATION_PATH . '/xml/order.xml';
            $error = $this->checkInputData($xml, $this->post_data);
            $data = $this->post_data;
            if (empty($error) == true) {
                if ($id > 0) {
                	// only update status or is pay
                	$dataIn = array(
                			'is_pay' => $data['is_pay'],
                			'status' => $data['status'],
                			'name' => $data['name'],
                			'address' => $data['address'],
                			'place' => $data['place'],
                			'phone' => $data['phone'],
                			'email' => $data['email'],
                            'note' => $data['note'],
                            'payment_method' => $data['payment_method'],
                            'reject_reason' => $data['reject_reason'],
                            'admin_discount'=> $data['admin_discount'],
                	);
                    if( $data['status'] != 4 ){
                        $dataIn['status_score'] = 0;
                    } else {
                        $dataIn['status_score'] = 1;
                    }
                    
                	$rs = $order->saveOrder($dataIn, $id);
                	
                	if( $data['status'] == 5 ) {
                        $newInfo = $models->fetchOrderById($id);
                        $this->sendCancelMailTemplate($newInfo);
                    } 
                    
                	if( $rs >= 0 ){
                		if( $info['user_id'] != -1 ){
                            // update score
                            $userInfo = $modelUser->getUserById( $info['user_id'] );
                            $currentScore = $userInfo['score'];
                            if( $data['status'] != 5 ){
                                if( $data['status'] != 4 && $info['status_score'] == 1 ){
                                    $currentScore = $currentScore - $info['score'];
                                    if( $currentScore < 0 ) { 
                                        $currentScore = 0;
                                    }
                                } else if( $data['status'] == 4 && $info['status_score'] == 0 ){
                                    $currentScore = $currentScore + $info['score'];
                                }
                            } else if( $data['status'] == 5 && $info['status'] != 4 ){
                                if( empty($info['discount']) == false && $info['discount'] > 0 ){
                                    $discount = $info['discount']/$this->_exchange_rate_score_to_money;
                                    $currentScore += $discount;
                                }
                            }
                            $modelUser->updateUser(array('score' => $currentScore), $info['user_id'] );
                        }

                		$this->_redirect('/admin/' . $this->controller);
                	} else{
                        $error[] = 'Cập nhật đơn hàng thất bại';
                	}
                } else {
                	// check ma giam gia
                	if( empty($data['price_pro']) == false ){
                		$promo = new PromotionCode();
                		$total = 0;
                		$cacl = 0;
                		foreach ( $data['price_pro'] as $key => $value ){
                			$total+= $value*$data['sl_pro'][$key];
                		}
                		if( empty($data['code']) == false && $data['apdungma'] == 'true'){
                			$promoinfo = $promo->fetchpromotionByCode($data['code']);
                			if( empty($promoinfo) == false ){
                				 $cacl = ($total*$promoinfo['percent'])/100;
                				 if( $cacl > $promoinfo['max_price']){
                				 	$cacl = $promoinfo['max_price'];
                				 }
                			} else {
                				$error[] = 'Mã giảm giá không hợp lệ';
                			}
                		}
                		$total = $total - $cacl;
                		//
                		$score = floor($total/SCORE_EXCHANGE_RATE);
                		if( empty($data['hiddenId']) == true ){
                			$data['hiddenId'] = -1;
                		}
                		if( empty($error) == true ){
                			$dataIn = array(
                					'order_code' =>  uniqid(),
                					'user_id' => $data['hiddenId'],
                					'created_date' => date("Y-m-d H:i:s"),
                					'updated_date' => date("Y-m-d H:i:s"),
                					'total' => $total,
                					'address'=> $data['address'],
                					'phone' => $data['phone'],
                					'email' => $data['email'],
                					'status' => $data['status'],
                					'is_pay' => $data['is_pay'],
                					'name'=> $data['name'],
                					'promotion_code'=> $data['code'],
                					'score' => $score,
                                                        'note' => $data['note'],
                                                        'payment_method' => $data['payment_method'],
                			);
                                if( $data['status'] == 4 ){
                                    $dataIn['status_score'] = 1;
                       		} else {
                                    $dataIn['status_score'] = 0;
                       		}
                                $rs = $order->saveOrder($dataIn);
                                if( $rs > 0 ){
                                    foreach ($data['price_pro'] as $key => $value ){
                                            $orderDetail = array(
                                                            'id_order' => $rs,
                                                            'id_product' => $data['pro_id'][$key],
                                                            'price' => $value,
                                                            'number' => $data['sl_pro'][$key],
                                                            'product_color'=> $data['color_pro'][$key]
                                            );
                                            $orderDetailModel->saveOrderDetail($orderDetail);
                                    }
                                    if( $data['hiddenId'] != '-1'){
                                        $userInfo = $modelUser->getUserById($data['hiddenId']);
                                        $currentScore = $userInfo['score'];
                                        if( $data['status'] == 4 ){
                                            $currentScore +=$score;
                                            $modelUser->updateUser(array('score' => $currentScore), $data['hiddenId']);
                                        } 
                                    }
                                    $this->_redirect('/admin/' . $this->controller);
                                } else{
                                        $error[] = 'Tạo đơn hàng thất bại';
                                }
                            }
                	} else {
                		$info = $this->post_data;
                		$error[] = 'Vui lòng chọn 1 sản phẩm';
                	}
                }
            } else {
                $info = $this->post_data;
            }
        }
        $listCombo = array();
        if (!empty($listOrderDetail)) {
            foreach ($listOrderDetail as $key => $value) {
                if (!empty($value['combo_id']) && $value['combo_id'] != 0) {
                    $listProducts = $modelProduct->getProductByComboId($value['combo_id']);
                    if (!empty($listProducts)) {
                        foreach ($listProducts as $product) {
                            $listCombo[$value['combo_id']][] = $product;
                        }
                    }
                }
            }
        }
        $this->view->listCombo = $listCombo;

        $productColor = new ProductColor();
        $listColor = $productColor->fetchAllColor();
        $this->view->color = $listColor;
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
        $model = new Order();
        //define columns
        $columns = array(// 
            0 => "id",
            1 => "total",
        	2 => "is_pay",
            3 => "address",
            4 => "phone",
        	5 => "email",
        	6 => "created_date",
        	7 => "updated_date",
        	8 => "updated_by",
        	9 => "first_name",
        	10 => "last_name",
        	11 => "user_id",
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
        $count = $model->fetchAllOrder($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllOrder($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }
	public function updateIspayAction(){
		$this->isAjax();
		if( empty($this->post_data['id']) == false ){
			$modal = new Order();
			$data = array('is_pay' => @$this->post_data['is_pay']);
			$reponse = $modal->saveOrder($data,$this->post_data['id']);
			if ($reponse >= 0) {
				$this->ajaxResponse(CODE_SUCCESS);
			}
		}
		$this->ajaxResponse(CODE_HAS_ERROR);
	}
	public function updateStatusAction(){
        $this->isAjax();
        if( empty($this->post_data['id']) == false && empty($this->post_data['user_id']) == false ){
            $modal = new Order();
            $info = $modal->fetchOrderById($this->post_data['id']);
            if( empty($info) == false ){
                $modelUser = new Users();
                $disc = 0;
                if( empty($this->post_data['admin_discount']) == false ){
                    $disc = $this->post_data['admin_discount'];
                }
                $data = array('status' => @$this->post_data['status'], 'reject_reason' => @$this->post_data['reason'], 'admin_discount'=> $disc );
                if( $data['status'] != 4 ){
                    $data['status_score'] = 0;
                } else {
                    $data['status_score'] = 1;
                }
                   
                $reponse = $modal->saveOrder($data,$this->post_data['id']);
                
                if( $data['status'] == 5 ) {
                    $newInfo = $modal->fetchOrderById($info['id']);
                    $this->sendCancelMailTemplate($newInfo);
                }
                
                if ( $reponse >= 0 ) {
                    if( $info['user_id'] != -1 ){
                        // update score
                        $userInfo = $modelUser->getUserById( $info['user_id'] );
                        $currentScore = $userInfo['score'];
                        if( @$this->post_data['status'] != 5 ){
                            if( @$this->post_data['status'] != 4 && $info['status_score'] == 1 ){
                                $currentScore = $currentScore - $info['score'];
                                if( $currentScore < 0 ) {
                                    $currentScore = 0;
                                }
                            } else if( @$this->post_data['status'] == 4 && $info['status_score'] == 0 ){
                                $currentScore = $currentScore + $info['score'];
                            } 
                        } else if( @$this->post_data['status'] == 5 && $info['status'] != 4 ) {
                            if( empty($info['discount']) == false && $info['discount'] > 0 ){
                                $discount = $info['discount']/$this->_exchange_rate_score_to_money;
                                $currentScore += $discount;
                            }
                        }
                        $modelUser->updateUser(array('score' => $currentScore), $info['user_id'] );
                    }                   

                    $this->ajaxResponse(CODE_SUCCESS);
                }
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
	}
    /**
     * 
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['id']) == false) {
            $modal = new Order();
            $reponse = $modal->deleteOrder($this->post_data['id']);
            if ($reponse >= 0) {
                $this->ajaxResponse(CODE_SUCCESS);
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }
    public function deleteAllAction(){
        $this->isAjax();
        $modal = new Order();
        $reponse = $modal->deleteOrderCancel();
        if ($reponse >= 0) {
            $this->ajaxResponse(CODE_SUCCESS);
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }
    public function approveAllAction() {
    	$this->isAjax();
    	$model = new Order();
    	$listOrder = $model->fetchOrderToApproveAll();
        $modelUser = new Users();
    	if( empty( $listOrder ) == false ){
    		foreach ( $listOrder as $order){
    			$model->saveOrder( array('status' => 4, 'status_score' => 1), $order['id'] );
                        if( $order['user_id'] != -1){
                            // update score
                            $userInfo = $modelUser->getUserById( $order['user_id'] );
                            $currentScore = $userInfo['score'];
                            if( $order['status_score'] == 0 ){
                                $currentScore = $currentScore + $order['score'];
                            }
                            $modelUser->updateUser(array('score' => $currentScore), $order['user_id'] );
                        }
    		}
    		$this->ajaxResponse(CODE_SUCCESS);
    	} else {
    		$this->ajaxResponse(CODE_HAS_ERROR);
    	}
    }
    public function sendCancelMailTemplate($orderInfo){
        $this->view->orderInfo = $orderInfo;
        $tpl = $this->view->render('/order/_tpl-mail.phtml');
        if( empty($orderInfo['email']) == false ){
            UtilEmail::sendMail(DEFAULT_EMAIL, $orderInfo['email'], 'Herogame hủy đơn hàng', $tpl );
        }
    }
}
