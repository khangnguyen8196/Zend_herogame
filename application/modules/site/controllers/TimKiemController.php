<?php

/**
 * Main page
 */
class Site_TimKiemController extends FrontEndAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->view->pageSize = Commons::pageSizeList();
        $this->view->sortList = Commons::sortList();
        $this->view->sortStatus = Commons::sortStatus();
        $this->loadJs('pages/sangpham');
    }

    public function searchAction() {
        $productMdl = new Product();
        $postMdl = new Post();
        $this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        $key = trim($this->getRequest()->getParam('keyword'));
        $selectedOption = isset($this->post_data["option"]) ? $this->post_data["option"] : '';
        $sortStatus = isset($_GET['status']) ? $_GET['status'] : '';
        $searchParams = array();
        $sortV = "priority desc";
        $sorted = "priority_desc";
        if (empty($this->post_data["sorted"]) == false) {
            $sortV = Commons::getSortRealValue($this->post_data["sorted"]);
            $sorted = $this->post_data["sorted"];
        }
        $this->view->sorted = $sorted;
        $this->view->selectedOption = $selectedOption;
        $this->view->sort_status = $sortStatus;
        $searchParams["sort"] = $sortV;
        if (!empty($sortStatus)) {
            $searchParams["sort_status"] = $sortStatus;
        }
        $this->view->maxRange = @$this->post_data["maxRange"];
        //limit item
        $limit = PAGINNATOR_LIMIT_ROW;
        if (empty($this->post_data["page_size"]) == false) {
            $limit = $this->post_data["page_size"];
        }
        $this->view->selected_page_size = $limit;
        $this->view->key = $key;
        if ($selectedOption == 'sanpham') {
            $productBySearch = $productMdl->search($key, $searchParams);
            $this->paginator($productBySearch, PAGINNATOR_MAX_LINK_PER_PAGE, $limit);
            $this->view->productBySearch = $this->_getProductBySearch($productBySearch);
        }else{
            $postBySearch =  $postMdl->search($key,$searchParams);
            $this->paginator($postBySearch, PAGINNATOR_MAX_LINK_PER_PAGE, $limit);
            $this->view->postBySearch = $this->_getPostBySearch($postBySearch);
        }
        $this->render('search');
    }
    
    private function _getProductBySearch($productBySearch) {
        $result = array();
        if (!empty($productBySearch)) {
            foreach ($productBySearch as $key => $value) {
             $result[$value["title"]][] = Commons::_buildProductData($value);
            }
        }
        return $result;
    }

    private function _getPostBySearch($postBySearch) {
        $result = array();
        if (!empty($postBySearch)) {
            foreach ($postBySearch as $key => $value) {
             $result[$value["title"]][] = Commons::_buildPostData($value);
            }
        }
        return $result;
    }

    public function flashAction() {
        $productMdl = new Product();
    	$this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        $sortStatus = isset($_GET['status']) ? $_GET['status'] : '';
        $params = array();
        //sort value in Db
        $sortV = "priority desc";

        $sorted = "priority_desc";
        if (empty($this->post_data["sorted"]) == false) {
            $sortV = Commons::getSortRealValue($this->post_data["sorted"]);
            $sorted = $this->post_data["sorted"];
        }
        $this->view->sort_status = $sortStatus;
        $this->view->sorted = $sorted;
        $params["sort"] = $sortV;
        if (!empty($sortStatus)) {
            $params["sort_status"] = $sortStatus;
        }
        $limit = PAGINNATOR_LIMIT_ROW;
        if (empty($this->post_data["page_size"]) == false) {
            $limit = $this->post_data["page_size"];
        }
        $this->view->selected_page_size = $limit;
        $productFlashSales =  $productMdl->getProductFlashSales($params);
		$this->paginator($productFlashSales, PAGINNATOR_MAX_LINK_PER_PAGE, $limit);
		$this->view->productFlashSales = $this->_getproductFlashSales($productFlashSales);
    }
	private function _getproductFlashSales($productFlashSales) {
        $result = array();
        if (!empty($productFlashSales)) {
            foreach ($productFlashSales as $key => $value) {
             $result[$value["title"]][] = Commons::_buildProductData($value);
            }
        }
        return $result;
    }
}
