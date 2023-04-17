<?php

/**
 * Category
 */
class Admin_MenuController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
//         $this->hasViewPermission();
        $this->loadJs('menu');
    }

    /**
     * Search page
     */
    public function indexAction() {
        
    }
    /**
     * 
     * @param type $editId
     * @return type
     */
    private function _getParentMenu( $editId = '' ){
        $menuMdl = new Menu();
        $data = $menuMdl->fetchAllMenu();
        $parentMenu = array();
        if( empty($data) == false && is_array($data)){
            foreach ($data as $key => $value) {
                if($value["level"] == 0 && $value["id"] != $editId ){
                    $parentMenu [] = $value;
                }
            }
        }
        return $parentMenu;
    }
    /**
     * 
     */
    public function detailAction() {
        $models = new Menu();
        //get parent menu
        $parentMenu = self::_getParentMenu( @$this->post_data ['id'] );
        $this->view->parentMenu = $parentMenu;
        //
        $info = array();
        $error = array();
        $id = 0;
        // get post card information if there is postcard'id available
        if (empty($this->post_data ['id']) == false) {
            $id = intval($this->post_data ['id']);
            $info = $models->fetchMenuById($id);
            if (empty($info) == true) {
                $this->_redirect('/admin/' . $this->controller);
            }
        }
        // check request is POST or GET
        if ($this->request->isPost()) {
            $xml = APPLICATION_PATH . '/xml/menu.xml';
            $error = $this->checkInputData($xml, $this->post_data);
            $check = $models->fetchMenuByUrl($this->post_data['url'], $id);
            if (empty($check) == false) {
                $error[] = 'Đường dẫn đã tồn tại';
            }
            if (empty($error) == true) {
                $data_in['name'] = $this->post_data['name'];
                $data_in['url'] = $this->post_data['url'];
                
                $showInMenu = 0;
                if( empty($this->post_data["show_in_menu"]) == false ){
                    $showInMenu = $this->post_data["show_in_menu"];
                }
                if( empty($this->post_data['priority']) == false ){
                	$data_in['priority'] = $this->post_data['priority'];
                }
                $data_in['show_in_menu'] = $showInMenu;
                $data_in['parent_menu'] = $this->post_data["parent_menu"];
                $data_in['level'] = $this->post_data["level"];
                $data_in['style'] = $this->post_data["style"];
                $data_in['description'] = $this->post_data["description"];
                if (empty($_FILES['image_icon']) == false && $_FILES['image_icon']['tmp_name']) {
                    $public_path = UPLOAD_PATH;
                    $upload_img = Commons::cwUpload('image_icon', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', $detailImg['width'], $detailImg['height']);
                    $data_in['image_icon'] = '/full/' . $upload_img;
                    if (empty($info['image_icon']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['image_icon'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }
              	if (empty($_FILES['image_icon_hover']) == false && $_FILES['image_icon_hover']['tmp_name']) {
                    $public_path = UPLOAD_PATH;
                    $upload_img = Commons::cwUpload('image_icon_hover', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', $detailImg['width'], $detailImg['height']);
                    $data_in['image_icon_hover'] = '/full/' . $upload_img;
                    if (empty($info['image_icon_hover']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['image_icon_hover'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }
                if (empty($_FILES['image']) == false && $_FILES['image']['tmp_name']) {
                    $public_path = UPLOAD_PATH;
                    $upload_img = Commons::cwUpload('image', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', $detailImg['width'], $detailImg['height']);
                    $data_in['image'] = '/full/' . $upload_img;
                    if (empty($info['image']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['image'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }

                $rs = $models->saveMenu($data_in, $id);
                if ($id > 0) {
                    if ($rs >= 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Update Menu Fail';
                    }
                } else {
                    if ($rs > 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = 'Add Menu Fail';
                    }
                }
            } else {
                $info = $this->post_data;
            }
        }
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
        $model = new Menu();
        //define columns
        $columns = array(// 
            0 => "id",
            1 => "name",
            2 => "image_icon",
            3 => "url"
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
        $count = $model->fetchAllMenu($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllMenu($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }

    /**
     * 
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['id']) == false) {
            $modal = new Menu();
            $reponse = $modal->deleteMenu($this->post_data['id']);
            if ($reponse >= 0) {
                $this->ajaxResponse(CODE_SUCCESS);
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }

}
