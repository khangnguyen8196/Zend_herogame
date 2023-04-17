<?php

/**
 * Base controller for crm module
 *
 */
class FrontEndAction extends Zend_Controller_Action {

    protected $post_data;
    protected $request;
    protected $autorefresh = null;
    protected $customer_info = null;
    protected $controller = '';
    protected $action = '';
    protected $module = '';
    protected $require_login = false;
	protected $setting = '';
        protected $token;
    /**
     * Init
     * @see Zend_Controller_Action::init()
     */
    public function init() {
        $post = $this->_helper->Common->myTrim($this->_getAllParams());
        $this->post_data = $post;
        $this->request = $this->getRequest();
        //Action and Controller
        $this->action = $this->request->getActionName();
        $this->controller = $this->request->getControllerName();
        $this->module = $this->request->getModuleName();
        
        $this->customer_info = UtilAuth::getCustommerLoginInfo(true);
        if (empty($this->customer_info) == false) {
            $this->view->customer_info = $this->customer_info;
            //update cart
            $cookieToken = $_COOKIE['token'];
            $t = md5($this->customer_info["user_id"]);
            if(empty($cookieToken) == false && $cookieToken != $t){
                $cart_list = UtilSession::get($cookieToken . "_CART_LIST");
                //remove cart item in old cookie
                UtilSession::set($cookieToken . "_CART_LIST","");
                //add new cart item to new cookie
                UtilSession::set($t . "_CART_LIST", $cart_list);
            }
        } else {
            $cookieToken = $_COOKIE['token'];
            if (empty($cookieToken) == false) {
                $t = $cookieToken;
            } else {
                $t = md5(uniqid(mt_rand(), true));
            }
        }
        //calc product quantiy
        $totalItem = 0;
        if (empty($t) == false) {
            $cart_list = UtilSession::get($t . "_CART_LIST");
            if (empty($cart_list) == false && is_array($cart_list)) {
                $totalItem = Commons::countItemInCart($cart_list);
            }
        }
        $this->view->item_count = $totalItem;
        setcookie("token", $t, strtotime( '+1 days' ), '/');
        $this->token = $t;
        //Auto refresh js
        $this->autorefresh = new My_View_Helper_AutoRefreshRewriter();
        $this->view->autorefresh = $this->autorefresh;
        //
        //get all setting
        $setting = $this->_getAllSetting();
        $this->setting = $setting;
        $this->view->setting = $setting;
        $menu = $this->_getMenu();
        $this->view->menu = $menu;
        //check permissions
        $this->view->hasViewPermission = UtilAuth::hasPrivilege($this->controller, ACTION_VIEW);
        $this->view->hasAddPermission = UtilAuth::hasPrivilege($this->controller, ACTION_ADD);
        $this->view->hasEditPermission = UtilAuth::hasPrivilege($this->controller, ACTION_EDIT);
        $this->view->hasDeletePermission = UtilAuth::hasPrivilege($this->controller, ACTION_DELETE);
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        
        if(empty($this->post_data["is_reset"]) == false){
        	$this->view->is_reset = $this->post_data["is_reset"];
        	$this->view->e = $this->post_data["e"];
        	$this->view->t = $this->post_data["t"];
        }
        $h2 = 'Máy chơi game nintendo, nintendo switch, nitendo 3DS';
        $keyword = Commons::getSettingByKey($setting, 'meta_index_page');
        if( empty($keyword) == false ){
            $ar = json_decode( $keyword['value'],true);
            if( empty($ar['keyword_meta']) == false ){
                $h2 = $ar['keyword_meta'];
            }
        }
        $this->view->headingData = array(
            'h1' => 'Herogame',
            'h2' => $h2
        );
    }
    //
    public function getInfoPage($data){
    	if( empty($data['category']) == false && $data['category'] == true ){
	    	$category = new Category();
	    	$listAllProductCategory = $category->listAllCategoryHomePage(array('type' => CATEGORY_TYPE_PRODUCT));
	    	$contentCategory = self::_getContentCateoryList($listAllProductCategory);
	    	$menuProductCategory = self::_getMenuCategory($listAllProductCategory);
	    	$this->view->menuCategory = $menuProductCategory;
	    	$this->view->listCategory = $contentCategory;
    	}
    	if( empty($data['new_post']) == false && $data['new_post'] == true ){
    		$this->view->newestPost = self::_getNewestPost(LEFT_SITE_PRODUCT_LIMIT);
    	}
    	if( empty($data['product_best_sell']) == false && $data['product_best_sell'] == true ){
    		$this->view->productBestSell = self::getProductsBestSale(LEFT_SITE_PRODUCT_LIMIT);
    	}
    	if( empty($data['new_products']) == false && $data['new_products'] == true ){
    		$this->view->newProducts = self::getNewUpdatedProducts(LEFT_SITE_PRODUCT_LIMIT);
    	}
    	if( empty($data['banner']) == false && $data['banner'] == true ){
    		$this->view->banner = self::_getBanner();
    	}
    }
    /**
     *
     * @return int
     */
    private function _getBanner() {
    	$banner = self::getBannerByType();
    	$list = array();
    	foreach ($banner as $key => $value) {
    		if ( $value['type'] == BANNER_MAIN ) {
    			$item = array();
    			if( $value['is_video'] == 0 ){
    				$item =  array(
    						'width' => 1920,
    						'height' => 600,
    						'outside' => 1, // Set if this banner link to another website or not. Value: True/False
    						'url' => @$value['link'],
    						'photo' => '/upload/images/full/' . $value['image'],
    						'caption' => @$value['description']
    				);
    			} else {
    				$item =  array(
    						'width' => 1920,
    						'height' => 600,
    						'video' => $value['video_url'],
    						'caption' => @$value['description']
    				);
    			}
    			$list['main'][] = $item;
    		} else {
    			$width = 510;
    			$height = 222;
    			if($value['type'] == BANNER_CHILD_HEADER ){
    				$width = 1040;
    				$height = 200;
    			}
    			$item = array(
    					'width' => $width,
    					'height' => $height,
    					'outside' => 1, // Set if this banner link to another website or not. Value: True/False
    					'url' => @$value['link'],
    					'photo' => '/upload/images/full/' . $value['image']
    			);
    			if( $value['is_video'] == 1  ){
    				$item =  array(
    						'width' => $width,
    						'height' => $height,
    						'video' => $value['video_url']
    				);
    			}
    			$list['child_' . $value['type']][] = $item;
    		}
    	}
    	return $list;
    }
    //
    /**
     *
     * @return string
     */
    public function getNewUpdatedProducts($limit) {
    	$res = UtilProduct::getNewUpdatedProducts($limit);
    	$newProducts = array();
    	if (empty($res) == false) {
    		$newProducts = Commons::_buildProductResponse($res);
    	}
    	return $newProducts;
    }
    /**
     *
     * @return string
     */
    public function getProductsBestSale($limit) {
    	$res = UtilProduct::getProductsBestSale($limit);
    	$productBestSell = array();
    	if (empty($res) == false) {
    		$productBestSell = Commons::_buildProductResponse($res);
    	}
    	return $productBestSell;
    }
    /**
     *
     * @return type
     */
    private function _getNewestPost($limit) {
    	$newestPost = UtilPost::getNewestPost($limit);
    	if (empty($newestPost) == false) {
    		foreach ($newestPost as $key => $value) {
    			$value["url"] = "/bai-viet/" . $value["url_name"];
    			$value["photo"] = '';
    			if(empty($value['image_id']) == false){
    				$value["photo"] = "/upload/images/" . $value['image_id'];
    			}
    			$value["date"] = date("d-m-Y", strtotime($value["updated_at"]));
    			$value["ccount"] = 0;
    			$newestPost[$key] = $value;
    		}
    	}
    	return $newestPost;
    }
    //
    private function _getMenuCategory($list) {
    	$rs = array();
    	if (empty($list) == false) {
    		foreach ($list as $key => $value) {
    			$rs[$value['level_category']][] = $value;
    		}
    	}
    	return $rs;
    }
    //
    private function _getContentCateoryList($list) {
    	$listCategory = array();
    	if (empty($list) == false) {
    		foreach ($list as $key => $value) {
    			if ($value['show_in_home_cate_page'] == 1) {
    				$listCategory[] = array(
    						'id' => $value['id'],
    						'name' => $value['name'],
    						//'url' => '/san-pham/index/category-name/' . $value['url_slug'],
                                                'url' => '/danh-muc/' . $value['url_slug'],
    						'pcount' => rand(1, 100),
    						'photo' => '/upload/images/' . $value['image'],
    						'show_in_home_cate_page'
    				);
    			}
    		}
    	}
    	return $listCategory;
    }
    // get all setting of page
    private function _getAllSetting(){
    	$model = new Setting();
    	$rs = $model->fetchAllSetting();
    	return $rs;
    }
    private function _getCategoryMenu(){
    	$model = new Category();
    	$list = $model->listAllCategoryOnMenu();
    	return $list;
    }
    private function _getMenu(){
    	$model = new Menu();
    	$list = array();
    	$qr = array(
    			'order' => array(
    					'column' => 'priority',
    					'dir' => 'asc'
    			)
    	);
    	$listMenu = $model->fetchAllMenu($qr);
    	$listCategory = $this->_getCategoryMenu();
    	if( empty($listMenu) == false ){
    		$listMainMenu = array();
    		$listChildMenu1 = array();
    		$listChildMenu2 = array();
    		foreach ( $listMenu as $key => $value ){
    			$value['is_menu'] = 1;
    			if( $value['level'] == 0 ){
    				$listMainMenu[] = $value;
    			} else if( $value['level'] == 1 ){
    				$listChildMenu1[] = $value;
    			} else {
    				$listChildMenu2[] = $value;
    			}
    			foreach ($listCategory as $keyc => $valuec ){
    				if( $value['url'] == $valuec['url_menu']){
    					$item = array(
    							'name' => $valuec['name'],
    							'image_icon' => $valuec['icon_on_menu'],
    							'image' => $valuec['image'],
    							'priority' => $valuec['priority'],
    							'parent_menu' => $value['id'],
    							'is_menu' => 0,
    							'description' => $value['description']
    					);
                                        if( $valuec['type_of_category'] == CATEGORY_TYPE_PRODUCT ){
                                            $item['url'] = '/danh-muc/'.$valuec['url_slug'];
                                        } else {
                                            $item['url'] = '/bai-viet/'.$valuec['url_slug'];
                                        }
    					if( $value['level'] == 0 ){
    						$listChildMenu1[] = $item;
    					} else if( $value['level'] == 1){
    						$listChildMenu2[] = $item;
    					}
    				}
    			}
    		}
    		foreach ( $listMainMenu as $key => $value ){
    			foreach ( $listChildMenu1 as $key1 => $value1){
    				if( $value1['parent_menu'] == $value['id']){
    					foreach ( $listChildMenu2 as $key2 => $value2){
    						if( $value2['parent_menu'] == $value1['id']){
    							$value1['child'][] = $value2;
    						}
    					}
    					$listMainMenu[$key]['child'][] = $value1;
    				}
    			}
    		}
    		$list = $listMainMenu;
    	}
    	return $list;
    }
    // get list banner
	public function getBannerByType( $type = 0 ){
		$model = new Banner();
		$listBanner = $model->loadListBannerBytype($type);
		return $listBanner;
	}
	// get product detail
	public function getProductDetail($url){
		$model = new Product();
		$info = $model->fetchProductByUrl($url);
		if( empty($info) == false ){
			$listImg = explode(',',$info['gallery']);
			$mediaModel = new Media();
			$listFullImg = $mediaModel->getListImage($listImg);
			$info['galleryDetail'] = $listFullImg; 
		}
		return $info;
	}
	public function getProductList($input){
		$model = new Product();
		$input['status'] = STATUS_ACTIVE;
		$list = $model->fetchAllProduct($input);
		return $list;
	}
    /**
     * Check ajax request
     * @param boolean $isRedirect
     * @return boolean
     */
    public function isAjax($isRedirect = true) {
        $result = false;
        if ($this->request->isXmlHttpRequest()) {
            $result = true;
        } else {
            if ($isRedirect == true) {
                $this->_redirect('/');
            } else {
                $result = false;
            }
        }
        return $result;
    }


    /**
     * Return ajax response
     * @param int $code
     * @param string $message
     * @param array $data
     * @param string $url
     */
    public function ajaxResponse($code, $message = '', $data = array(), $url = '') {
        $this->retData['Code'] = $code;
        $this->retData['Message'] = $message;
        $this->retData['Data'] = $data;
        $this->retData['Url'] = $url;
        $this->_helper->json($this->retData);
    }


    /**
     * Load template content
     * @param string $template
     */
    public function loadTemplate($template) {
        $content = $this->view->render($template);
        $this->ajaxResponse(CODE_SUCCESS, '', $content);
    }

    /**
     * Check has view permission
     * @param unknown_type $url
     */
    public function hasViewPermission($url = "/") {
        if ($this->view->hasViewPermission == false) {
            if ($this->isAjax(false) == true) {
                $this->ajaxResponse(CODE_PERMISSION_DENIED);
            } else {
                $this->_redirect($url);
            }
        }
    }

    /**
     * Load translation
     * @param mix $fileName
     */
    public function loadJs($fileName) {
        if (is_array($fileName) == true) {
            foreach ($fileName as $value) {
                $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/site/js/' . $value . '.js', 'text/javascript'));
            }
        } else {
            $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/site/js/' . $fileName . '.js', 'text/javascript'));
        }
    }

    public function isLoggedIn() {
        $result = true;
        if( $this->module == 'site'){
	        if (empty($this->view->customer_info) == true) {
	            if ($this->isAjax(false) == true) {
	                $this->ajaxResponse(CODE_SESSION_EXPIRED);
	            } else {
	                $this->_redirect('/index');
	            }
	        }
        }
        return $result;
    }
    /**
     * 
     * @param type $select
     */
    public function paginator($select, $maxLink = PAGINNATOR_MAX_LINK_PER_PAGE, $limit = PAGINNATOR_LIMIT_ROW ) {
        // Get a Paginator object using Zend_Paginator's built-in factory.
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/shared/_data_pager.phtml');
        $paginator = Zend_Paginator::factory($select);
        $pageParam = $this->getRequest()->getParam("page", 1);
        $paginator->setItemCountPerPage($limit);
        $paginator->setPageRange($maxLink);
        $paginator->setCurrentPageNumber($pageParam);
        $this->view->paginator = $paginator;
        $currentPage = $this->_getParam('page', 1);
        $this->view->current_page = $currentPage;
    }
    
    public function _setMeta($data) {
    	if (empty($data['description_meta']) == false) {
            $this->view->headMeta()->appendName('description', $data['description_meta']);
        } else if( empty($data['meta_description']) == false ){
            $this->view->headMeta()->appendName('description', $data['meta_description']);
        } else if( empty($data['description']) == false ){
            $this->view->headMeta()->appendName('description', $data['description']);
        }else {
            $this->view->headMeta()->appendName('description', 'Herogame - Gọi điện để đặt hàng ngay 0906.221.218  (Call-Sms-Viber-Zalo)');
    	}
    	if (empty($data['keyword_meta']) == false ) {
    		$this->view->headMeta()->appendName('keywords', $data['keyword_meta']);
        } if (empty($data['keyword']) == false ) {
            $this->view->headMeta()->appendName('keywords', $data['keyword']);
        } else {
    		$this->view->headMeta()->appendName('keywords', 'Máy chơi game nintendo, nintendo switch, nitendo 3DS');
    	}
    	if (empty($data['og_title']) == false) {
    		$this->view->headMeta()->appendName('og:title', $data['og_title']);
    	}
    	if (empty($data['og_site_name']) == false) {
    		$this->view->headMeta()->appendName('og:site_name', $data['og_site_name']);
    	}
    	if (empty($data['og_url']) == false) {
    		$this->view->headMeta()->appendName('og:url', $data['og_url']);
    	}
    	if (empty($data['og_image']) == false) {
    		$this->view->headMeta()->appendName('og:image', $data['og_image']);
    	}
    	if (empty($data['og_description']) == false) {
    		$this->view->headMeta()->appendName('og:description', $data['og_description']);
    	}
    	if( empty( $data['title'] ) == false ){
    	    if (empty($data['title_page']) == true) {
    	        $this->view->headTitle()->append($data['title']);
    	    }
    	}
    	if( empty( $data['title_page'] ) == false ){
    		$this->view->headTitle()->append($data['title_page']);
    		if (empty($data['og_title']) == true) {
    	        $this->view->headMeta()->appendName('og:title', $data['title_page']);
    		}
    	}
    }
    /**
     * 
     * @param type $cart_list
     * @return type
     */
    public function getProductsFullInfo($cart_list, &$totalMoney) {
        $productMdl = new Product();
        $mdlProductColor = new ProductColor();
        $cart_list_full_info = array();
        
        if (empty($cart_list) == false && is_array($cart_list)) {
            foreach ($cart_list as $p_id => $value) {
                $p_full_info = $productMdl->getProductInfoById($p_id);
                if (empty($p_full_info) == true) {
                    continue;
                }
                if (empty($value) == false && is_array($value)) {
                    foreach($value as $color_id => $qty){
                        $color = $mdlProductColor->fetchColorById($color_id);
                        $p_full_info["qty"] = $qty;
                        $p_full_info["total_money"] = $qty * $p_full_info["price_sales"];
                        $p_full_info["color_name"] = $color['color_name'];
                        $p_full_info["color_id"] = $color['id'];
                        $totalMoney = $totalMoney + $p_full_info["total_money"];

                        $cart_list_full_info[] = $p_full_info;
                    }
                }else{
                    $p_full_info["qty"] = $value;
                    $p_full_info["total_money"] = $value * $p_full_info["price_sales"];
                    $p_full_info["color_name"] = '';
                    $p_full_info["color_id"] = '';
                    $totalMoney = $totalMoney + $p_full_info["total_money"];

                    $cart_list_full_info[] = $p_full_info;
                }
            }
        }
        return $cart_list_full_info;
    }

}
