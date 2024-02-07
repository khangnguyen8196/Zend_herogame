<?php

/**
 * Main page
 */
class Site_PagesController extends FrontEndAction {

	protected $_postMdl = "";
	protected $_productMdl ="";
	protected $_categoryMdl = "";
    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->getInfoPage(array('banner' => true, 'category' => false, 'new_post' => true, 'product_best_sell' => false,'new_products' => false));
        $this->_categoryMdl = new Category();
        $this->_postMdl = new Post();
		$this->_productMdl = new Product();
        $this->loadJs('pages/tintuc');
        $params = array();
        $params["type_of_category"] = CATEGORY_TYPE_POST;
        $this->view->listCategoryPost = $this->_categoryMdl->getCategoryInfoByParams( $params );
    }

    /**
     * Search page
     */
    public function indexAction() {
    	$modelPost = new Post();
    	$params = array();
    	if( empty( $this->post_data['danh-muc'] ) == false){
    		$cateInfo = $this->_categoryMdl->getCategoryInfoByParams( array( 'url_slug' => $this->post_data['danh-muc'] ) );
    		if ( empty( $cateInfo ) == true) {
    			$this->_redirect("/");
    		} else if( empty( $cateInfo ) == false && empty( $cateInfo[0]['id'] ) == false ){
		    	$params = array(
		    			'id_category' => $cateInfo[0]['id']
		    	);
    		}
    		$this->view->cateInfo = $cateInfo[0];
    		$this->view->danhMuc = $this->post_data['danh-muc'];
    	}
    	$params['limit'] = MAX_POST_OF_PAGE + 1;
    	$listPost = $modelPost->getPosts( $params );
    	$this->_setMeta( @$listPost[0] );
    	$this->view->listPost = $listPost;
        $this->view->headingData = array(
            'h1' => 'Herogame',
            'h2' => 'Bài Viết, Tin Tức, Thông Tin'
        );
    }
    /**
     *
     */
    public function chiTietAction() {
    	if (empty($this->post_data["bai-viet"]) == true) {
    		$this->_redirect("/");
    	}
    	$info = $this->_postMdl->fetchPostByUrl($this->post_data["bai-viet"]);
    	if (empty($info) == true) {
    		$this->_redirect("/");
    	}
    	$this->view->info = $info;
        $this->view->headingData = array(
            'h1' => 'Herogame',
            'h2' => $info['title']
        );
    	$this->view->relative_post = $this->_getRelativePost( $info["relative_post"] );
		$this->view->relative_product = $this->_getRelativeProduct($info["relative_product"]);
		$this->view->order_with_product = $this->_getOrderWithProduct($info["order_with_product"]);
    	$this->_setMeta( $info );
    }
    
    /**
     *
     * @param type $productIdList
     * @return array
     */
    private function _getRelativePost( $postIdList ) {
    	$relativePost = array();
    	if ( empty($postIdList) == false ) {
    		if( is_array( $postIdList ) == false ){
    			$postIdList = explode( ',', $postIdList);
    		}
    		foreach ($postIdList as $key => $value) {
    			$postInfo = $this->_postMdl->fetchPostById($value);
    			$relativePost[$key] = $postInfo;
    		}
    	}
    	return $relativePost;
    }
    public function loadViewMoreAction(){
    	$this->isAjax();
    	$data = $this->post_data;
    	if( empty( $data['start'] ) == false ){
    		$modelPost = new Post();
    		$params = array();
    		if( empty( $this->post_data['danhMuc'] ) == false){
    			$cateInfo = $this->_categoryMdl->getCategoryInfoByParams( array( 'url_slug' => $this->post_data['danhMuc'] ) );
    			if ( empty( $cateInfo ) == true) {
    				$this->_redirect("/");
    			} else if( empty( $cateInfo ) == false && empty( $cateInfo[0]['id'] ) == false ){
    				$params = array(
    						'id_category' => $cateInfo[0]['id']
    				);
    			}
    		}
    		$params['start'] = $this->post_data['start'];
    		$params['limit'] = MAX_POST_OF_PAGE + 1;
    		$listPost = $modelPost->getPosts( $params );
    		$response['next'] = true;
    		if( count( $listPost ) <= MAX_POST_OF_PAGE ){
    			$response['next'] = false;
    		}
    		$this->view->listPost = $listPost;
    		$response['html'] = $this->view->render('/pages/_item-post.phtml');
    		$this->ajaxResponse( CODE_SUCCESS, '', $response );
    	}
    	$this->ajaxResponse( CODE_HAS_ERROR );
    }

	private function _getRelativeProduct($productIdList) {
        $relativeProduct = array();
        if (empty($productIdList) == false) {
            $productIdList = explode(",", $productIdList);
            if (empty($productIdList) == false) {
                foreach ($productIdList as $key => $value) {
                    $productInfo = $this->_productMdl->fetchProductById($value);
                    $relativeProduct[$value] = $productInfo;
                }
            }
        }
        
        if( empty($relativeProduct) == false ){
            $relativeProduct = Commons::_buildProductResponse($relativeProduct);
        }
        return $relativeProduct;
    }
	private function _getOrderWithProduct($productIdList) {
        $orderWithProduct = array();
        if (empty($productIdList) == false) {
            $productIdList = explode(",", $productIdList);
            if (empty($productIdList) == false) {
                foreach ($productIdList as $key => $value) {
                    $productInfo = $this->_productMdl->fetchProductById($value);
                    $orderWithProduct[$value] = $productInfo;
                }
            }
        }
        
        if( empty($orderWithProduct) == false ){
            $orderWithProduct = Commons::_buildProductResponse($orderWithProduct);
        }
        return $orderWithProduct;
    }
}
