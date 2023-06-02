<?php

/**
 *
 */
class UtilPost {
    /**
     * 
     * @param type $limit
     * @return type
     */
    public static function getNewestPost($limit = 4) {
        $postMdl = new Post();
        //declare parameters
        $params["updated_at"] = date("Y-m-d H:i:s");
        $params["condition"] = "<=";
        $params["limit"] = $limit;
        $params["order"] = "updated_at";
        $params["order_type"] = "DESC";
        $list = $postMdl->getPosts($params);
        return $list;
    }

}
