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
        $this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        $key = trim($this->getRequest()->getParam('keyword'));
        $searchParams = array();
        $sortV = "priority desc";
        $sorted = "priority_desc";
        if (empty($this->post_data["sorted"]) == false) {
            $sortV = Commons::getSortRealValue($this->post_data["sorted"]);
            $sorted = $this->post_data["sorted"];
        }
        $this->view->sorted = $sorted;
        $searchParams["sort"] = $sortV;
        $productBySearch = $productMdl->search($key,$searchParams);
        $select=$productBySearch;

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
        $this->paginator($select, PAGINNATOR_MAX_LINK_PER_PAGE, $limit);
        $this->view->key = $key;
        $this->view->productBySearch = $this->_getProductBySearch($productBySearch);
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
         
    
}
