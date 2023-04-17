<?php

/**
 * Category
 */
class Admin_CategoryController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/libs/ckeditor/ckeditor.js', 'text/javascript'));
        $this->loadJs('category');
    }

    /**
     * Search page
     */
    public function indexAction() {
        //index action
    }
    /**
     * 
     * @param type $editId
     * @return type
     */
    private function _getParentCategory($editId = '') {
        $categoryMdl = new Category();
        $data = $categoryMdl->fetchAllCategory();
        $parentCategory = array();
        if (empty($data) == false && is_array($data)) {
            foreach ($data as $key => $value) {
                if ($value["id"] != $editId && $value["level_category"] < 2) {
                    $parentCategory[] = $value;
                }
            }
        }

        return $parentCategory;
    }

    public function detailAction() {
        $typeOfCategory = array( "1" => "Product", "2" => "Post");
        $this->view->typeOfCategory = $typeOfCategory;
        $models = new Category();
        // list category level < 2 
        $parentCategoryList = self::_getParentCategory( @$this->post_data ['id']);
        $this->view->parentCategoryList = $parentCategoryList; 
        // end get list parent category
        
        $info = array();
        $error = array();
        $id = 0;
        // get post card information if there is postcard'id available
        if (empty($this->post_data ['id']) == false) {
            $id = intval($this->post_data ['id']);
            $info = $models->fetchCategoryById($id);
            if (empty($info) == true) {
                $this->_redirect('/admin/' . $this->controller);
            }
        }
        // check request is POST or GET
        if ($this->request->isPost()) {
            $xml = APPLICATION_PATH . '/xml/category.xml';
            $error = $this->checkInputData($xml, $this->post_data);
            if (empty($error) == true) {
                $this->post_data['url_slug'] = Commons::url_slug($this->post_data['url_slug']);
                $check = $models->checkExistCategoryUrl($this->post_data['url_slug'], $id);
                if (empty($check) == false) {
                    $error[] = 'Đường dẫn đã tồn tại';
                }
            }
            if (empty($error) == true) {
                if (empty($this->post_data['priority']) == false && is_numeric($this->post_data['priority']) == true) {
                    $data_in['priority'] = $this->post_data['priority'];
                } else {
                    $data_in['priority'] = 0;
                }
                $data_in['name'] = $this->post_data['name'];
                $data_in['meta_description'] = $this->post_data['meta_description'];
                $data_in['description'] = $this->post_data['description'];
                $data_in['url_slug'] = @$this->post_data['url_slug'];
                $data_in['title_page'] = @$this->post_data['title_page'];
                $data_in['keyword'] = @$this->post_data['keyword'];
                $data_in['summary'] = @$this->post_data['summary'];
                if (empty($this->post_data['url_menu']) == false) {
                    $data_in['url_menu'] = $this->post_data['url_menu'];
                } else {
                    $data_in['url_menu'] = '';
                }
                
                $data_in['status'] = $this->post_data['status'];
                $data_in['type_of_category'] = $this->post_data['type_of_category'];
                $data_in['parent_category'] = $this->post_data['parent_category'];
                $data_in['level_category'] = $this->post_data['level_category'];
                if( empty($this->post_data['show_in_menu']) == false ){
                	$data_in['show_in_menu'] = $this->post_data['show_in_menu'];
                } else {
                	$data_in['show_in_menu'] = 0;
                }
                if( empty($this->post_data['show_in_home_cate_page']) == false ){
                	$data_in['show_in_home_cate_page'] = $this->post_data['show_in_home_cate_page'];
                } else {
                	$data_in['show_in_home_cate_page'] = 0;
                }
                if( empty($this->post_data['show_list_product_home_page']) == false ){
                	$data_in['show_list_product_home_page'] = $this->post_data['show_list_product_home_page'];
                } else {
                	$data_in['show_list_product_home_page'] = 0;
                }
                //image
                if (empty($_FILES['icon_on_menu']) == false && $_FILES['icon_on_menu']['tmp_name']) {
                	$public_path = UPLOAD_PATH;
                	$upload_img = Commons::cwUpload('icon_on_menu', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', '400', '300');
                	$data_in['icon_on_menu'] = '/full/' . $upload_img;
                	if (empty($info['icon_on_menu']) == false) {
                		// img info
                		$full = $public_path . '/images' . $info['icon_on_menu'];
                		if (file_exists($full)) {
                			unlink($full);
                		}
                	}
                }
                //image
                if (empty($_FILES['image']) == false && $_FILES['image']['tmp_name']) {
                    $public_path = UPLOAD_PATH;
                    $upload_img = Commons::cwUpload('image', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', '400', '300');
                    $data_in['image'] = '/full/' . $upload_img;
                    if (empty($info['image']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['image'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }
                //icon
                if (empty($_FILES['icon']) == false && $_FILES['icon']['tmp_name']) {
                    $public_path = UPLOAD_PATH;
                    $upload_img = Commons::cwUpload('icon', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', '400', '300');
                    $data_in['icon'] = '/full/' . $upload_img;
                    if (empty($info['icon']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['icon'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }
            	//icon
                if (empty($_FILES['icon_hover']) == false && $_FILES['icon_hover']['tmp_name']) {
                    $public_path = UPLOAD_PATH;
                    $upload_img = Commons::cwUpload('icon_hover', $public_path . '/images/full/', '', FALSE, $public_path . '/images/thumnail/', '400', '300');
                    $data_in['icon_hover'] = '/full/' . $upload_img;
                    if (empty($info['icon_hover']) == false) {
                        // img info
                        $full = $public_path . '/images' . $info['icon_hover'];
                        if (file_exists($full)) {
                            unlink($full);
                        }
                    }
                }
                //save data
                $rs = $models->saveCategory($data_in, $id);
                if ($id > 0) {
                    if ($rs >= 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = UtilTranslator::translate('update-category-fail');
                    }
                } else {
                    if ($rs > 0) {
                        $this->_redirect('/admin/' . $this->controller);
                    } else {
                        $error[] = UtilTranslator::translate('add-category-fail');
                    }
                }
            } else {
                $info = $this->post_data;
            }
        }
        $model = new Menu();
        $listMenu = $model->fetchMenu();
        $this->view->listMenu = $listMenu;
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
        $model = new Category();
        //define columns
        $columns = array(// 
            0 => "id",
            1 => "name",
            2 => "url_slug",
            3 => "image",
            4 => "url_menu",
            5 => 'status',
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
        $count = $model->fetchAllCategory($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $model->fetchAllCategory($this->post_data);
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $response['draw'] = $draw;
        $this->_helper->json($this->returnResponseDatatable($response));
    }

    /**
     * 
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['id']) == false) {
            $modal = new Category();
            $reponse = $modal->deleteCategory($this->post_data['id']);
            if ($reponse >= 0) {
                $this->ajaxResponse(CODE_SUCCESS);
            }
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }

}
