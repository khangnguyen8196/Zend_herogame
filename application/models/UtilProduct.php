<?php

/**
 *
 */
class UtilProduct {
    /**
     * 
     * @param type $limit
     * @return type
     */
    public static function getProductsBestSale($limit = 5) {
        $productMdl = new Product();
        $customer_info = UtilAuth::getCustommerLoginInfo(true);
        $rank_id = null;
        if ($customer_info && isset($customer_info['level_member']) && $customer_info['level_member'] >= 1) {
            $rank_id = $customer_info['level_member'];
        }
        //declare parameters
        $params["best_sell"] = 1;
        $params["limit"] = $limit;
        $params["order"] = "priority";
        $params["order_type"] = "ASC";
        return $productMdl->getProducts($params, $rank_id);
    }
    /**
     * 
     * @param type $limit
     * @return type
     */
    public static function getNewUpdatedProducts($limit = 5) {
        $productMdl = new Product();
        $customer_info = UtilAuth::getCustommerLoginInfo(true);
        $rank_id = null;
        if ($customer_info && isset($customer_info['level_member']) && $customer_info['level_member'] >= 1) {
            $rank_id = $customer_info['level_member'];
        }
        $params["new_product"] = 1;
        $params["limit"] = $limit;
        $params["order"] = "priority";
        $params["order_type"] = "ASC";
        return $productMdl->getProducts($params, $rank_id);
    }
    
    /**
     * 
     * @param type $limit
     * @return type
     */
    public static function getPromotionProducts($limit = 5) {
        $productMdl = new Product();
        $customer_info = UtilAuth::getCustommerLoginInfo(true);
        $rank_id = null;
        if ($customer_info && isset($customer_info['level_member']) && $customer_info['level_member'] >= 1) {
            $rank_id = $customer_info['level_member'];
        }
        $params["is_promotion"] = 1;
        $params["limit"] = $limit;
        $params["order"] = "priority";
        $params["order_type"] = "ASC";
        return $productMdl->getProducts($params, $rank_id);
    }
    /**
     * 
     * @param type $limit
     * @return type
     */
    public static function getProductGroupByCategory($limit = 5) {
        $productMdl = new Product();
        $customer_info = UtilAuth::getCustommerLoginInfo(true);
        $rank_id = null;
        if ($customer_info && isset($customer_info['level_member']) && $customer_info['level_member'] >= 1) {
            $rank_id = $customer_info['level_member'];
        }
        $params["type_of_category"] = CATEGORY_TYPE_PRODUCT;
        $params["show_list_product_home"] = 1;
        $params["limit"] = $limit;
        $params["order"] = "priority";
        $params["order_type"] = "ASC";
        $params["order_category"] = "true";
        $params["show_in_category_home_page"] = 1;
        return $productMdl->getProducts($params, $rank_id);
    }

}
