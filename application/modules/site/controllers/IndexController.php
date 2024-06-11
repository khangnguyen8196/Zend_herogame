<?php

/**
 * Main page
 */
class Site_IndexController extends FrontEndAction {
    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
    }

    public function indexAction() {
        // list banner
        // list category
        $this->getInfoPage(array('banner' => true, 'category' => true, 'new_post' => true, 'product_best_sell' => true,'new_products' => true));
        // the midle content
        $this->view->contentProductGroup = self::_getContentProductGroup();
        // 
        $this->view->productGroupByCategory = self::_getProductGroupByCategory();
		// get meta index page (og:description, og:image, og:title)
		$settings = $this->setting;
		if( empty( $settings ) == false ){
			foreach ( $settings as $setting ){
				if( $setting['key'] == 'meta_index_page' && empty( $setting['value'] ) == false ){
					$this->_setMeta( json_decode( $setting['value'], true) );
					break;
				}
			}
		}
    }
    /**
     * 
     * @return array
     */
    private function _getProductGroupByCategory() {
        $res = UtilProduct::getProductGroupByCategory(CONTENT_PRODUCT_LIMIT);
        $productGroupByCategory = array();
        if (empty($res) == false) {
            foreach ($res as $key => $value) {
                $productGroupByCategory[$value ['id_category']][$value['category_name']][] = Commons::_buildProductData($value);
            }
        }
        return $productGroupByCategory;
    }

    /**
     * 
     * @return type
     */
    private function _getContentProductGroup() {
        $contentProductGroup["new_arrival"] = array();
        $contentProductGroup["best_sell"] = array();
        $contentProductGroup["promotion"] = array();
        //get new arrival products list 
        $newArrivalP = $this->getNewUpdatedProducts(CONTENT_PRODUCT_LIMIT);
        if (empty($newArrivalP) == false) {
            $contentProductGroup["new_arrival"] = $newArrivalP;
        }
        //get best-sell product  list
        $bestSellP = $this->getProductsBestSale(CONTENT_PRODUCT_LIMIT);
        if (empty($bestSellP) == false) {
            $contentProductGroup["best_sell"] = $bestSellP;
        }
        //get promotion product 
        $promotionP = self::_getPromotionProducts(CONTENT_PRODUCT_LIMIT);
        if (empty($promotionP) == false) {
            $contentProductGroup["promotion"] = $promotionP;
        }
        return $contentProductGroup;
    }


    /**
     * 
     * @return string
     */
    private function _getPromotionProducts($limit) {
        $res = UtilProduct::getPromotionProducts($limit);
        $promotionProducts = array();
        if (empty($res) == false) {
            $promotionProducts = Commons::_buildProductResponse($res);
        }
        return $promotionProducts;
    }

    // public function getNumberOfDayInMonthAction(){
    //     if(empty($this->post_data["m"]) == true || empty($this->post_data["y"]) == true){
    //         $this->ajaxResponse(CODE_HAS_ERROR);
    //     }
    //     $numberOfDay = cal_days_in_month(CAL_GREGORIAN, $this->post_data["m"], $this->post_data["y"]);
    //     $this->view->numberOfDay = $numberOfDay;
    //     $this->view->slt_day = @$this->post_data["slt_day"];
    //     $html = $this->view->render("/tai-khoan/_day-option.phtml");
    //     $this->ajaxResponse(CODE_SUCCESS,"",$html);
    // }
    public function getNumberOfDayInMonthAction(){
        if(empty($this->post_data["m"]) || empty($this->post_data["y"])){
            $this->ajaxResponse(CODE_HAS_ERROR);
        }
    
        $month = (int)$this->post_data["m"];
        $year = (int)$this->post_data["y"];
    
        $numberOfDay = $this->getNumberOfDaysInMonth($month, $year);
    
        $this->view->numberOfDay = $numberOfDay;
        $this->view->slt_day = @$this->post_data["slt_day"];
        $html = $this->view->render("/tai-khoan/_day-option.phtml");
        $this->ajaxResponse(CODE_SUCCESS,"",$html);
    }

    public function getNumberOfDaysInMonth($month, $year) {
        if ($month < 1 || $month > 12) {
            return 0; // Tháng không hợp lệ
        }
    
        // Tính số ngày trong tháng
        switch ($month) {
            case 2: // Tháng 2
                if ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0)) {
                    return 29; // Năm nhuận
                } else {
                    return 28; // Năm không nhuận
                }
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                return 30; // Tháng có 30 ngày
                break;
            default:
                return 31; // Tháng có 31 ngày
        }
    }
    
}
