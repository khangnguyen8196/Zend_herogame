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
        $this->loadJs('pages/sangpham');
    }

    public function searchAction() {
        $productMdl = new Product();
        $postMdl = new Post();
        $this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        $key = trim($this->getRequest()->getParam('keyword'));
        $selectedOption = isset($this->post_data["option"]) ? $this->post_data["option"] : '';
        $searchParams = array();
        $sortV = "priority desc";
        $sorted = "priority_desc";
        if (empty($this->post_data["sorted"]) == false) {
            $sortV = Commons::getSortRealValue($this->post_data["sorted"]);
            $sorted = $this->post_data["sorted"];
        }
        $this->view->sorted = $sorted;
        $this->view->selectedOption = $selectedOption;
        $searchParams["sort"] = $sortV;
        $productBySearch = $productMdl->search($key,$searchParams);
        $postBySearch =  $postMdl->search($key,$searchParams);
        //search by prices range
        if (empty($this->post_data["minRange"]) == false && is_numeric($this->post_data["minRange"])) {
            $searchParams["minRange"] = $this->post_data["minRange"];
        }
        $this->view->minRange = @$this->post_data["minRange"];
        if (empty($this->post_data["maxRange"]) == false && is_numeric($this->post_data["maxRange"])) {
            $searchParams["maxRange"] = $this->post_data["maxRange"];
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
            $this->paginator($productBySearch, PAGINNATOR_MAX_LINK_PER_PAGE, $limit);
            $this->view->productBySearch = $this->_getProductBySearch($productBySearch);
        }else{
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

    // private function _getNewestPost($limit) {
    // 	$newestPost = UtilPost::getNewestPost($limit);
    // 	if (empty($newestPost) == false) {
    // 		foreach ($newestPost as $key => $value) {
    // 			$value["url"] = "/bai-viet/" . $value["url_name"];
    // 			$value["photo"] = '';
    // 			if(empty($value['image_id']) == false){
    // 				$value["photo"] = "/upload/images/" . $value['image_id'];
    // 			}
    // 			$value["date"] = date("d-m-Y", strtotime($value["updated_at"]));
    // 			$value["ccount"] = 0;
    // 			$newestPost[$key] = $value;
    // 		}
    // 	}
    // 	return $newestPost;
    // }
         
    
}
