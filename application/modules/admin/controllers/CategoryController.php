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
                $data_in['og_image'] = @$this->post_data['og_image'];
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
                // banner youtube
                $this->handleBannerImage($data_in, $info, 'banner_ytb', 'url_image_banner_ytb_delete');
                $this->handleBannerImage($data_in, $info, 'banner_ytb_L', 'url_image_banner_ytb_delete_left');
                $this->handleBannerImage($data_in, $info, 'banner_ytb_M', 'url_image_banner_ytb_delete_mid');
                $this->handleBannerImage($data_in, $info, 'banner_ytb_R', 'url_image_banner_ytb_delete_right');

                $this->handleBannerImage($data_in, $info, 'banner_ytb_2', 'url_image_banner_ytb_delete_2');
                $this->handleBannerImage($data_in, $info, 'banner_ytb_L_2', 'url_image_banner_ytb_delete_left_2');
                $this->handleBannerImage($data_in, $info, 'banner_ytb_M_2', 'url_image_banner_ytb_delete_mid_2');
                $this->handleBannerImage($data_in, $info, 'banner_ytb_R_2', 'url_image_banner_ytb_delete_right_2');
                
                $data_in['text_banner_ytb'] = $this->post_data['text_banner_ytb'];
                $data_in['text_banner_ytb_2'] = $this->post_data['text_banner_ytb_2'];
                $data_in['iframe_ytb'] = $this->post_data['iframe_ytb'];
                $data_in['iframe_ytb_2'] = $this->post_data['iframe_ytb_2'];

                //  banner slide category
                if( empty($_POST['url_image_slide_category_delete']) == false && empty($info['image_slide_category']) == false ){
                    $public_path = UPLOAD_PATH;
                    $image_slide_category = explode(",",$info['image_slide_category']);
                    foreach ($_POST['url_image_slide_category_delete'] as $keyd => $valued ){
                        foreach ($image_slide_category as $k => $v ){
                            if( $valued == $v){
                                unset($image_slide_category[$k]);
                                $full = $public_path  . $valued;
                                if (file_exists($full)) {
                                    unlink($full);
                                }
                                break;
                            }
                        }
                    }
                    if( empty($image_slide_category) == false ){
                        $info['image_slide_category'] = implode(",",$image_slide_category);
                    } else {
                        $info['image_slide_category'] = '';
                    }
                    $data_in['image_slide_category'] = $info['image_slide_category'];
                }

                if(empty($_POST['url_image_slide_category_botton_delete'])== false && empty($info['url_image_slide_category']) == false ){
                    $url_image_slide_category = explode(",",$info['url_image_slide_category']);
                    foreach ($_POST['url_image_slide_category_botton_delete'] as $keyd => $valued ){
                        foreach ($url_image_slide_category as $k => $v ){
                            if( $valued == $v){
                                unset($url_image_slide_category[$k]);
                                break;
                            }
                        }
                    }
                    if( empty($url_image_slide_category) == false ){
                        $info['url_image_slide_category'] = implode(",",$url_image_slide_category);
                    } else {
                        $info['url_image_slide_category'] = '';
                    }
                    $data_in['url_image_slide_category'] = $info['url_image_slide_category'];
                }

                if (!empty($_FILES['image_slide_category'])) {
                    $public_path = UPLOAD_PATH;
                    $nowdir = 'img_' . date('d_m_Y');
                    $listFile = array();
                    foreach ($_FILES['image_slide_category']['name'] as $key => $value) {
                        if (!empty($value)) {
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            $fileName = pathinfo($value, PATHINFO_FILENAME);
                            $fileName = str_replace(' ', '-', $fileName);
                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                            Commons::makedirs($public_path . '/images/' . $nowdir);
                            if (move_uploaded_file($_FILES["image_slide_category"]["tmp_name"][$key], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                $listFile[$key] = '/images/' . $nowdir . '/' . $newname;
                            }
                        }
                    }
                    
                    if (!empty($listFile)) {
                        if (!empty($info['image_slide_category'])) {
                            $current_images = explode(",", $info['image_slide_category']);
                            if (count($current_images) <= 100) {
                                foreach ($listFile as $key => $file) {
                                    if (isset($current_images[$key])) {
                                        $full = $public_path  . $current_images[$key];
                                        if (file_exists($full)) {
                                            unlink($full);
                                        }
                                        $current_images[$key] = $file;
                                    }elseif(count($current_images)<$key && $key<=100){
                                        $current_images[] = $file;
                                      
                                    }
                                }
                                $data_in['image_slide_category'] = implode(",", $current_images);
                            } else {
                                $data_in['image_slide_category'] = $info['image_slide_category'] . ',' . implode(",", $listFile);
                            }
                        } else {
                            $data_in['image_slide_category'] = implode(",", $listFile);
                        }
                    }
                }
                if (!empty($_POST['url_image_slide_category'])) {
                    $allUrls = array();
                    foreach ($_POST['url_image_slide_category'] as $url) {
                        if (!empty($url)) {
                            $allUrls[] = $url;
                        }
                    }
                    $data_in['url_image_slide_category'] = implode(",", $allUrls);
                }
                // end banner slide category
                //  banner 2
                if( empty($_POST['url_image_2_delete']) == false && empty($info['image_2_botton']) == false ){
                    $public_path = UPLOAD_PATH;
                    $image_2_botton = explode(",",$info['image_2_botton']);
                    foreach ($_POST['url_image_2_delete'] as $keyd => $valued ){
                        foreach ($image_2_botton as $k => $v ){
                            if( $valued == $v){
                                unset($image_2_botton[$k]);
                                $full = $public_path  . $valued;
                                if (file_exists($full)) {
                                    unlink($full);
                                }
                                break;
                            }
                        }
                    }
                    if( empty($image_2_botton) == false ){
                        $info['image_2_botton'] = implode(",",$image_2_botton);
                    } else {
                        $info['image_2_botton'] = '';
                    }
                    $data_in['image_2_botton'] = $info['image_2_botton'];
                }

                if(empty($_POST['url_image_2_botton_delete'])== false && empty($info['url_image_2_botton']) == false ){
                    $url_image_2_botton = explode(",",$info['url_image_2_botton']);
                    foreach ($_POST['url_image_2_botton_delete'] as $keyd => $valued ){
                        foreach ($url_image_2_botton as $k => $v ){
                            if( $valued == $v){
                                unset($url_image_2_botton[$k]);
                                break;
                            }
                        }
                    }
                    if( empty($url_image_2_botton) == false ){
                        $info['url_image_2_botton'] = implode(",",$url_image_2_botton);
                    } else {
                        $info['url_image_2_botton'] = '';
                    }
                    $data_in['url_image_2_botton'] = $info['url_image_2_botton'];
                }

                if (!empty($_FILES['image_2_botton'])) {
                    $public_path = UPLOAD_PATH;
                    $nowdir = 'img_' . date('d_m_Y');
                    $listFile = array();
                    foreach ($_FILES['image_2_botton']['name'] as $key => $value) {
                        if (!empty($value)) {
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            $fileName = pathinfo($value, PATHINFO_FILENAME);
                            $fileName = str_replace(' ', '-', $fileName);
                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                            Commons::makedirs($public_path . '/images/' . $nowdir);
                            if (move_uploaded_file($_FILES["image_2_botton"]["tmp_name"][$key], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                $listFile[$key] = '/images/' . $nowdir . '/' . $newname;
                            }
                        }
                    }
                    
                    if (!empty($listFile)) {
                        if (!empty($info['image_2_botton'])) {
                            $current_images = explode(",", $info['image_2_botton']);
                            if (count($current_images) <= 100) {
                                foreach ($listFile as $key => $file) {
                                    if (isset($current_images[$key])) {
                                        $full = $public_path  . $current_images[$key];
                                        if (file_exists($full)) {
                                            unlink($full);
                                        }
                                        $current_images[$key] = $file;
                                    }elseif(count($current_images)<$key && $key<=100){
                                        $current_images[] = $file;
                                      
                                    }
                                }
                                $data_in['image_2_botton'] = implode(",", $current_images);
                            } else {
                                $data_in['image_2_botton'] = $info['image_2_botton'] . ',' . implode(",", $listFile);
                            }
                        } else {
                            $data_in['image_2_botton'] = implode(",", $listFile);
                        }
                    }
                }
                if (!empty($_POST['url_image_2_botton'])) {
                    $allUrls = array();
                    foreach ($_POST['url_image_2_botton'] as $url) {
                        if (!empty($url)) {
                            $allUrls[] = $url;
                        }
                    }
                    $data_in['url_image_2_botton'] = implode(",", $allUrls);
                }
                // end banner 2

                // banner 3 
                if(empty($_POST['url_image_3_delete']) == false && empty($info['image_3_botton']) == false ){
                    $public_path = UPLOAD_PATH;
                    $image_3_botton = explode(",",$info['image_3_botton']);
                    foreach ($_POST['url_image_3_delete'] as $keyd => $valued ){
                        foreach ($image_3_botton as $k => $v ){
                            if( $valued == $v){
                                unset($image_3_botton[$k]);
                                $full = $public_path  . $valued;
                                if (file_exists($full)) {
                                    unlink($full);
                                }
                                break;
                            }
                        }
                    }
                    if( empty($image_3_botton) == false ){
                        $info['image_3_botton'] = implode(",",$image_3_botton);
                    } else {
                        $info['image_3_botton'] = '';
                    }
                    $data_in['image_3_botton'] = $info['image_3_botton'];
                }
                if(empty($_POST['url_image_3_botton_delete'])== false && empty($info['url_image_3_botton']) == false ){
                    $url_image_3_botton = explode(",",$info['url_image_3_botton']);
                    foreach ($_POST['url_image_3_botton_delete'] as $keyd => $valued ){
                        foreach ($url_image_3_botton as $k => $v ){
                            if( $valued == $v){
                                unset($url_image_3_botton[$k]);
                                break;
                            }
                        }
                    }
                    if( empty($url_image_3_botton) == false ){
                        $info['url_image_3_botton'] = implode(",",$url_image_3_botton);
                    } else {
                        $info['url_image_3_botton'] = '';
                    }
                    $data_in['url_image_3_botton'] = $info['url_image_3_botton'];
                }
                if (!empty($_FILES['image_3_botton'])) {
                    $public_path = UPLOAD_PATH;
                    $nowdir = 'img_' . date('d_m_Y');
                    $listFile = array();
                    foreach ($_FILES['image_3_botton']['name'] as $key => $value) {
                        if (!empty($value)) {
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            $fileName = pathinfo($value, PATHINFO_FILENAME);
                            $fileName = str_replace(' ', '-', $fileName);
                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                            Commons::makedirs($public_path . '/images/' . $nowdir);
                            if (move_uploaded_file($_FILES["image_3_botton"]["tmp_name"][$key], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                $listFile[$key] = '/images/' . $nowdir . '/' . $newname;
                            }
                        }
                    }
                    
                    if (!empty($listFile)) {
                        if (!empty($info['image_3_botton'])) {
                            $current_images = explode(",", $info['image_3_botton']);
                            if (count($current_images) <= 100) {
                                foreach ($listFile as $key => $file) {
                                    if (isset($current_images[$key])) {
                                        $full = $public_path  . $current_images[$key];
                                        if (file_exists($full)) {
                                            unlink($full);
                                        }
                                        $current_images[$key] = $file;
                                    }elseif(count($current_images)<$key && $key<=100){
                                        $current_images[] = $file;
                                    }
                                }
                                $data_in['image_3_botton'] = implode(",", $current_images);
                            } else {
                                $data_in['image_3_botton'] = $info['image_3_botton'] . ',' . implode(",", $listFile);
                            }
                        } else {
                            $data_in['image_3_botton'] = implode(",", $listFile);
                        }
                    }
                }
                if (!empty($_POST['url_image_3_botton'])) {
                    $allUrls = array();
                    foreach ($_POST['url_image_3_botton'] as $url) {
                        if (!empty($url)) {
                            $allUrls[] = $url;
                        }
                    }
                    $data_in['url_image_3_botton'] = implode(",", $allUrls);
                }
                // end banner 3 

                // banner 12
                if(empty($_POST['url_image_12_delete']) == false && empty($info['image_12_botton']) == false ){
                    $public_path = UPLOAD_PATH;
                    $image_12_botton = explode(",",$info['image_12_botton']);
                    foreach ($_POST['url_image_12_delete'] as $keyd => $valued ){
                        foreach ($image_12_botton as $k => $v ){
                            if( $valued == $v){
                                unset($image_12_botton[$k]);
                                $full = $public_path  . $valued;
                                if (file_exists($full)) {
                                    unlink($full);
                                }
                                break;
                            }
                        }
                    }
                    if( empty($image_12_botton) == false ){
                        $info['image_12_botton'] = implode(",",$image_12_botton);
                    } else {
                        $info['image_12_botton'] = '';
                    }
                    $data_in['image_12_botton'] = $info['image_12_botton'];
                }
                if(empty($_POST['url_image_12_botton_delete'])== false && empty($info['url_image_12_botton']) == false ){
                    $url_image_12_botton = explode(",",$info['url_image_12_botton']);
                    foreach ($_POST['url_image_12_botton_delete'] as $keyd => $valued ){
                        foreach ($url_image_12_botton as $k => $v ){
                            if( $valued == $v){
                                unset($url_image_12_botton[$k]);
                                break;
                            }
                        }
                    }
                    if( empty($url_image_12_botton) == false ){
                        $info['url_image_12_botton'] = implode(",",$url_image_12_botton);
                    } else {
                        $info['url_image_12_botton'] = '';
                    }
                    $data_in['url_image_12_botton'] = $info['url_image_12_botton'];
                }

                if (!empty($_FILES['image_12_botton'])) {
                    $public_path = UPLOAD_PATH;
                    $nowdir = 'img_' . date('d_m_Y');
                    $listFile = array();
                    foreach ($_FILES['image_12_botton']['name'] as $key => $value) {
                        if (!empty($value)) {
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            $fileName = pathinfo($value, PATHINFO_FILENAME);
                            $fileName = str_replace(' ', '-', $fileName);
                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                            Commons::makedirs($public_path . '/images/' . $nowdir);
                            if (move_uploaded_file($_FILES["image_12_botton"]["tmp_name"][$key], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                $listFile[$key] = '/images/' . $nowdir . '/' . $newname;
                            }
                        }
                    }
                    
                    if (!empty($listFile)) {
                        if (!empty($info['image_12_botton'])) {
                            $current_images = explode(",", $info['image_12_botton']);
                            if (count($current_images) <= 100) {
                                foreach ($listFile as $key => $file) {
                                    if (isset($current_images[$key])) {
                                        $full = $public_path  . $current_images[$key];
                                        if (file_exists($full)) {
                                            unlink($full);
                                        }
                                        $current_images[$key] = $file;
                                    }elseif(count($current_images)<$key && $key<=100){
                                        $current_images[] = $file;
                                    }
                                }
                                $data_in['image_12_botton'] = implode(",", $current_images);
                            } else {
                                $data_in['image_12_botton'] = $info['image_12_botton'] . ',' . implode(",", $listFile);
                            }
                        } else {
                            $data_in['image_12_botton'] = implode(",", $listFile);
                        }
                    }
                }
                if (!empty($_POST['url_image_12_botton'])) {
                    $allUrls = array();
                    foreach ($_POST['url_image_12_botton'] as $url) {
                        if (!empty($url)) {
                            $allUrls[] = $url;
                        }
                    }
                    $data_in['url_image_12_botton'] = implode(",", $allUrls);
                }
                //end banner 12

                //  banner 2 top
                if( empty($_POST['url_image_2_delete_top']) == false && empty($info['image_2_top']) == false ){
                    $public_path = UPLOAD_PATH;
                    $image_2_top = explode(",",$info['image_2_top']);
                    foreach ($_POST['url_image_2_delete_top'] as $keyd => $valued ){
                        foreach ($image_2_top as $k => $v ){
                            if( $valued == $v){
                                unset($image_2_top[$k]);
                                $full = $public_path  . $valued;
                                if (file_exists($full)) {
                                    unlink($full);
                                }
                                break;
                            }
                        }
                    }
                    if( empty($image_2_top) == false ){
                        $info['image_2_top'] = implode(",",$image_2_top);
                    } else {
                        $info['image_2_top'] = '';
                    }
                    $data_in['image_2_top'] = $info['image_2_top'];
                }

                if(empty($_POST['url_image_2_top_delete'])== false && empty($info['url_image_2_top']) == false ){
                    $url_image_2_top = explode(",",$info['url_image_2_top']);
                    foreach ($_POST['url_image_2_top_delete'] as $keyd => $val ){
                        foreach ($url_image_2_top as $k => $v ){
                            if( $val == $v){
                                unset($url_image_2_top[$k]);
                                break;
                            }
                        }
                    }
                    if( empty($url_image_2_top) == false ){
                        $info['url_image_2_top'] = implode(",",$url_image_2_top);
                    } else {
                        $info['url_image_2_top'] = '';
                    }
                    $data_in['url_image_2_top'] = $info['url_image_2_top'];
                }

                if (!empty($_FILES['image_2_top'])) {
                    $public_path = UPLOAD_PATH;
                    $nowdir = 'img_' . date('d_m_Y');
                    $listFile = array();
                    foreach ($_FILES['image_2_top']['name'] as $key => $value) {
                        if (!empty($value)) {
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            $fileName = pathinfo($value, PATHINFO_FILENAME);
                            $fileName = str_replace(' ', '-', $fileName);
                            $newname = $fileName . '_' . rand(0, 1000000) . '_' . uniqid('', true) . '.' . $ext;
                            Commons::makedirs($public_path . '/images/' . $nowdir);
                            if (move_uploaded_file($_FILES["image_2_top"]["tmp_name"][$key], $public_path . '/images/' . $nowdir . '/' . $newname)) {
                                $listFile[$key] = '/images/' . $nowdir . '/' . $newname;
                            }
                        }
                    }
                    
                    if (!empty($listFile)) {
                        if (!empty($info['image_2_top'])) {
                            $current_images = explode(",", $info['image_2_top']);
                            if (count($current_images) <= 100) {
                                foreach ($listFile as $key => $file) {
                                    if (isset($current_images[$key])) {
                                        $full = $public_path  . $current_images[$key];
                                        if (file_exists($full)) {
                                            unlink($full);
                                        }
                                        $current_images[$key] = $file;
                                    }elseif(count($current_images)<$key && $key<=100){
                                        $current_images[] = $file;
                                      
                                    }
                                }
                                $data_in['image_2_top'] = implode(",", $current_images);
                            } else {
                                $data_in['image_2_top'] = $info['image_2_top'] . ',' . implode(",", $listFile);
                            }
                        } else {
                            $data_in['image_2_top'] = implode(",", $listFile);
                        }
                    }
                }
                if (!empty($_POST['url_image_2_top'])) {
                    $allUrlsTop = array();
                    foreach ($_POST['url_image_2_top'] as $url) {
                        if (!empty($url)) {
                            $allUrlsTop[] = $url;
                        }
                    }
                    $data_in['url_image_2_top'] = implode(",", $allUrlsTop);
                }
                // end banner 2 top
                
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
            if(UtilAuth::hasPrivilege('category', ACTION_DELETE) == true){
                $reponse = $modal->deleteCategory($this->post_data['id']);
                if ($reponse >= 0) {
                    $this->ajaxResponse(CODE_SUCCESS);
                }
            } 
        }
        $this->ajaxResponse(CODE_HAS_ERROR);
    }

    public function mediaAction(){
		$this->_helper->layout->disableLayout( true );
		$this->loadJs('category');
		$mdlMedia = new Media();
		$data = array();
		$data['length'] = MAX_ITEM_IMAGE;
		$list = $mdlMedia->fetchAllMedia( $data );
		$this->view->listMedia = $list;
		$this->view->functionNum = $this->post_data["CKEditorFuncNum"];
	}
	
	public function handleBannerImage(&$data_in, $info, $field, $field_post_delete) {
        $public_path = UPLOAD_PATH;
    
        if (!empty($_POST[$field_post_delete])) {
            $current = isset($info[$field]) ? $info[$field] : '';
            $full = $public_path . '/images' . $current;
    
            if ($_POST[$field_post_delete] == $current && file_exists($full)) {
                unlink($full);
            }
    
            $data_in[$field] = '';
        }
    
        if (!empty($_FILES[$field]) && $_FILES[$field]['tmp_name']) {
            $upload_img = Commons::cwUpload(
                $field,
                $public_path . '/images/full/',
                '',
                false,
                $public_path . '/images/thumnail/',
                '1920',
                '600'
            );
    
            $data_in[$field] = '/full/' . $upload_img;
    
            // Xóa ảnh cũ nếu tồn tại
            if (!empty($info[$field])) {
                $old_img_path = $public_path . '/images' . $info[$field];
                if (file_exists($old_img_path)) {
                    unlink($old_img_path);
                }
            }
        }
    }

}
