<?php

/**
 * Category
 */
class Admin_PromotionCodeController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
//         $this->hasViewPermission();
        $this->loadJs('promotion-code');
    }

    /**
     * Search page
     */
    public function indexAction() {
        
    }
    /**
     * 
     */
    public function detailAction() {
        $models = new PromotionCode();
        $modelCategory = new Category();
        $listCategory = $modelCategory->listAllCategory();
        //get parent menu
        //
        $info = array();
        $error = array();
        $id = 0;
        // get post card information if there is postcard'id available
        if (empty($this->post_data ['id']) == false) {
            $id = intval($this->post_data ['id']);
            $info = $models->fetchPromoById($id);
            if (empty($info) == true) {
                $this->_redirect('/admin/' . $this->controller);
            }
        }
        // check request is POST or GET
        if ($this->request->isPost()) {
            $xml = APPLICATION_PATH . '/xml/promo_code.xml';
            $error = $this->checkInputData($xml, $this->post_data);
            $check = $models->fetchPromoByCode( $this->post_data ['code'] );
//             echo "<pre>"; print_r($check); echo "</pre>";exit;
            if ( (empty($check) == false && $id == 0) 
            		|| ( $id > 0 && empty($check) == false && $check['code'] != $this->post_data['code'] )) {
                $error[] = 'Mã khuyến mãi đã tồn tại';
            }
            if (empty($error) == true) {
                $data_in['name'] = $this->post_data['name'];
                $data_in['code'] = $this->post_data['code'];
                $data_in['percent'] = $this->post_data['percent'];
                $data_in['max_price'] = $this->post_data['max_price'];
                $data_in['startdate'] = UtilFormat::formatTimeForCreatedDate($this->post_data['startdate']);
                $data_in['enddate'] = UtilFormat::formatTimeForCreatedDate($this->post_data['enddate']).' 23:59:59';
                $data_in['category'] = $this->post_data['id_category'];
                $data_in['status'] = $this->post_data['status'];
                $rs = $models->savePromo($data_in, $id);
                if ($id > 0) {
                    if ($rs >= 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Cập nhật mã khuyến mãi không thành công!';
                    }
                } else {
                    if ($rs > 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Thêm mới mã khuyến mãi không thành công!';
                    }
                }
            } else {
                $info = $this->post_data;
            }
        }
        $this->view->listCategory = $listCategory;
        $this->view->info = $info;
        $this->view->id = $id;
        $this->view->error = $error;
    }

    /**
     * Search page
     */
    public function listAction() {
        $this->isAjax();
        $draw = $this->post_data['draw']; // 
        $model = new PromotionCode();
        //define columns
        $columns = array(// 
            0 => "id",
            1 => "name",
            2 => "code",
            3 => "startdate",
        	4 => "enddate"     		
        );

        //order function
        if (empty($this->post_data["order"]) == false) {
            $this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
            $this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
        } else {
            $this->post_data["order"]["column"] = "updated_at";
            $this->post_data["order"]["dir"] = "desc";
        }
        //search function
        if (empty($this->post_data["columns"]) == false && is_array($this->post_data["columns"])) {
            foreach ($this->post_data["columns"] as $column) {
                if ($column["searchable"] == true && empty($column["search"]) == false && $column["search"]["value"] != "") {
                    $this->post_data[$column["data"]] = $column["search"]["value"];
                }
            }
        }
        if (empty($this->post_data['search']) == false && empty($this->post_data['search']['value']) == false) {
            $this->post_data['search-key'] = $this->post_data['search']['value'];
        }
        //get total data
        $this->post_data['count_only'] = 1;
        $count = $model->fetchAllPromoCode($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllPromoCode($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }
	public function checkAndGetPromotionAction(){
		$this->isAjax();
		$code = $this->post_data['code'];
		$model = new PromotionCode();
		$rs = $model->fetchpromotionByCode($code);
		if( empty($rs) == false){
			$this->ajaxResponse(CODE_SUCCESS,'',$rs);
		}
		$this->ajaxResponse(CODE_HAS_ERROR);
	}
    /**
     * 
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['id']) == false) {
            $modal = new PromotionCode();
            $reponse = $modal->deletePromo($this->post_data['id']);
            if ($reponse >= 0) {
                $this->ajaxResponse(CODE_SUCCESS);
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }

}
