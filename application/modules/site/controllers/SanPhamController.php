<?php

/**
 * Main page
 */
class Site_SanPhamController extends FrontEndAction {

    protected $_categoryMdl = "";
    protected $_productMdl = "";
    protected $_variantMdl = "";
    protected $_variantImgMdl = ""; 
    protected $_comboMdl = ""; 
    protected $_comboDetailMdl = ""; 

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->_categoryMdl = new Category();
        $this->_productMdl = new Product();
        $this->_variantMdl = new ProductVariant();
        $this->_variantImgMdl = new VariantImage();
        $this->_comboMdl = new ComboProduct();
        $this->_comboDetailMdl = new ComboDetail();
        $this->view->pageSize = Commons::pageSizeList();
        $this->view->sortList = Commons::sortList();
        $this->loadJs('pages/sangpham');
    }

    /**
     * Search page
     */
    public function indexAction() {
    	$this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        if (empty($this->post_data["category-name"]) == true) {
            $this->_redirect("/");
        }
        $params["url_slug"] = $this->post_data["category-name"];
        $params["type_of_category"] = CATEGORY_TYPE_PRODUCT;
        $categoryInfo = $this->_categoryMdl->getCategoryInfoByParams($params);
        
        if (empty($categoryInfo) == true) {
            $this->_redirect("/");
        }
        $categoryInfo = $categoryInfo[0];
        $this->_setMeta( $categoryInfo );
        
        $groupCategoryId = array();
        $currentCategoryId = $categoryInfo["id"];
        //add first category
        $groupCategoryId[] = $currentCategoryId;
        $firstChildCategory = $this->_categoryMdl->getChildCategoryInfoByParentCategoryId($currentCategoryId);
        if(empty($firstChildCategory) == false && is_array($firstChildCategory)){
            foreach ($firstChildCategory as $child ){
                if(empty($child["id"]) == false ){
                    $groupCategoryId[] = $child["id"];
                    $childNextCategory = $this->_categoryMdl->getChildCategoryInfoByParentCategoryId($child["id"]);
                    if(empty($childNextCategory) == false && is_array($childNextCategory)){
                        foreach ($childNextCategory as $childNext ){
                            if(empty($childNext["id"]) == false ){
                                $groupCategoryId[] = $childNext["id"];
                            }
                        }
                    }
                    
                }
            }
        }
        
        //search params
        $searchParams = array();
        //sort value in Db
        $sortV = "priority desc";
        // sort value at html
        $sorted = "priority_desc";
        if (empty($this->post_data["sorted"]) == false) {
            $sortV = Commons::getSortRealValue($this->post_data["sorted"]);
            $sorted = $this->post_data["sorted"];
        }
        $this->view->sorted = $sorted;
        // add sort params
        $searchParams["sort"] = $sortV;
        //search by prices range
        if (empty($this->post_data["minRange"]) == false && is_numeric($this->post_data["minRange"])) {
            $searchParams["minRange"] = $this->post_data["minRange"];
        }
        $this->view->minRange = @$this->post_data["minRange"];
        if (empty($this->post_data["maxRange"]) == false && is_numeric($this->post_data["maxRange"])) {
            $searchParams["maxRange"] = $this->post_data["maxRange"];
        }
        $this->view->maxRange = @$this->post_data["maxRange"];
        
        //excute select
        $select = $this->_productMdl->getProductsByCategoryId($groupCategoryId, $searchParams);
        //limit item
        $limit = PAGINNATOR_LIMIT_ROW;
        if (empty($this->post_data["page_size"]) == false) {
            $limit = $this->post_data["page_size"];
        }
        $this->view->selected_page_size = $limit;
        $this->view->categoryInfo = $categoryInfo;
        $this->view->headingData = array(
            'h1' => $categoryInfo['name'],
            'h2' => $categoryInfo['description']
        );
        $this->paginator($select, PAGINNATOR_MAX_LINK_PER_PAGE, $limit);
    }

    /**
     * 
     */
    public function chiTietAction() {
        $this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        if (empty($this->post_data["name"]) == true) {
            $this->_redirect("/");
        }
        $productInfo = $this->_productMdl->fetchProductByUrl($this->post_data["name"]);
        if (empty($productInfo) == true) {
            $this->_redirect("/");
        }
        
        $color_list = Commons::getProductColor($productInfo['product_color']);
        $list_variant = $this->_variantMdl->getProductVariants($productInfo['id']);
        $list_variant_img = $this->_variantImgMdl->getProductImages($productInfo['id']);
       
        $list_combo_product = $this->_comboDetailMdl->getComboByProductId($productInfo['id']);
        $list_combo_detail = array();
        foreach ($list_combo_product as $combo_product) {
            $combo_detail = $this->_comboDetailMdl->getProductByComboId($combo_product['combo_id']);
            $list_combo_detail[$combo_product['combo_id']] = $combo_detail; 
        }
        $this->view->list_combo_detail = $list_combo_detail;
        $this->view->list_combo_product=$list_combo_product;
        

        $this->view->color_list = $color_list; 
        $this->view->list_variant = $list_variant;   
        $this->view->list_variant_img = $list_variant_img;   
        
        $this->_setMeta( $productInfo );
        $this->view->info = $productInfo;
        $this->view->relative_product = $this->_getRelativeProduct($productInfo["relative_product"]);

    }

    /**
     * 
     * @param type $productIdList
     * @return array
     */
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
}
