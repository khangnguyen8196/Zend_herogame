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
    }

    // search function
    public function indexAction() {
        $this->isAjax();
        $productMdl = new Product();
        $postMdl = new Post();
        if (empty($this->post_data["k"]) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, 'Vui lòng nhập từ khóa tại ổ tìm kiếm.');
        }
        $key = trim($this->post_data["k"]);
        // product list
        $productList = $productMdl->search($key);
        $this->view->productList = $productList;
        // get post list
        $postList = $postMdl->search($key);
        $this->view->postList = $postList;
        
        $this->view->key = $key;
        
        $html = $this->view->render("/tim-kiem/_item.phtml");
        $this->ajaxResponse(CODE_SUCCESS, '', $html);
    }

}
