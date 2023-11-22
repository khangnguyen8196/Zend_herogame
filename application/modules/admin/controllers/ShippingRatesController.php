<?php
/**
 * Setting management 
 * created by @Phuong Nguyen
 */
class Admin_ShippingRatesController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
        public function init() {
            parent::init();
            $this->isLoggedIn();
            $this->hasViewPermission();
            $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/libs/ckeditor/ckeditor.js', 'text/javascript'));
            $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/core/libraries/color-picker/js/bootstrap-colorpicker.js', 'text/javascript'));
            $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/bootstrap-datetimepicker.min.css"));
            $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/pickers/bootstrap-datetimepicker.min.js', 'text/javascript'));
            $this->loadJs('shipping-rates');
            // $this->loadJs(array('combo-product'));
        }

    /**
     * Search page
     */
        public function indexAction() {
            $mdlShippingRates = new ShippingRates();
            $this->view->list = $mdlShippingRates->fetchAllShippingRates();
        }

    /**
     * detail page
     */
        function checkDataAction(){
            
            $this->isAjax();
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(); 
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $province = $data['province'];
                $district = $data['district'];
                $mdlShippingRates = new ShippingRates;
                if ($data['province']) {
                    $check = $mdlShippingRates->checkDataExists($province, $district);
                    if($check &&  $data['id'] == 0){
                        $result = 'Địa chỉ này đã tồn tại phí vận chuyển.';
                    }
                }
                $this->getResponse()
                    ->setHeader('Content-Type', 'text/html')
                    ->setBody($result);
            }
        }
        public function detailAction(){
            $mdlShippingRates = new ShippingRates();
            $mdlProvince = new Province();
            $mdlDistrict = new District();
            $mdlWards = new Wards();
            $info = array();
            $error = array();
            $data = $this->post_data;
            $fee_id = 0;
            // get post card information if there is postcard'id available
            if( empty( $this->post_data ['fee_id'] ) == false ) {
                $fee_id = intval($this->post_data ['fee_id']);
                $info = $mdlShippingRates->getShippingRatesById($fee_id);
                if( empty( $info ) == true ) {
                    $this->_redirect( '/'.$this->module.'/'.$this->controller );
                }
            }
            // check request is POST or GET
            if( $this->request->isPost()) {
                if( empty($error) == true ){
                    $dataInsert['fee_matp'] = $data['province'];
                    $dataInsert['fee_maqh'] = $data['district'];
                    $dataInsert['fee_ship'] = $data['fee_ship'];
                    $dataUpdated = $this->getUpdated();
                    $dataInsert = array_merge($data, $dataUpdated);
                    $dataCreated = $this->getCreated();
                    $dataInsert = array_merge($data, $dataCreated);
                    $dataInsert['status'] = STATUS_ACTIVE;
                    $rs = $mdlShippingRates->saveShippingRates($dataInsert, $fee_id);
                    if ($fee_id > 0) {
                        if ($rs >= 0) {
                            $this->_redirect('/admin/' . $this->controller);
                        } else {
                            $error[] = 'Thêm Phí Vận Chuyển Thành Công';
                        }
                    } else {
                        if ($rs > 0) {
                            $this->_redirect('/admin/' . $this->controller);    
                        } else {
                            $error[] = 'Thêm Phí Vận Chuyển Thất Bại';
                        }
                    }  
                } else {
                        $info = $this->post_data;
                }
            }
            
            if( empty( $info ) == false ){
                $listShippingRates = $mdlShippingRates->getShippingRatesById($info['fee_id']);
                $this->view->listShippingRates = $listShippingRates;

                $listDistrict =  $mdlDistrict->getListDistrictByMatp($info['fee_matp']);
                // $listWards =  $mdlWards->getListWardsByMatp($info['fee_maqh']);
                $this->view->listDistrict = $listDistrict;
                // $this->view->listWards = $listWards;
            }
            $listProvince =  $mdlProvince->getAllProvince();
            $this->view->listProvince = $listProvince;
            $this->view->fee_id = $fee_id; 
            $this->view->info = $info;
            $this->view->error = $error;
            
        }
    /**
     * delete
     */
        public function selectAction(){
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(); 

            $request = $this->getRequest();

            if ($request->isPost()) {
                $data = $request->getPost();
                $action = $data['action'];
                $ma_id = $data['ma_id'];
                $output = '';

                if ($action == 'province') {
                    $mdlDistrict = new District;
                    $selectDistrict = $mdlDistrict->getAllDistrictByMatp($ma_id);
                    $output .= '<option>Chọn quận huyện</option>';
                    foreach ($selectDistrict as $key => $value) {
                        $output .= '<option value="' . $value['maqh'] . '">' . $value['name_district'] . '</option>';
                    }
                }
                // else {
                //     $mdlWards = new Wards;
                //     $selectWards = $mdlWards->getAllWardsByMatp($ma_id);
                //     $output .= '<option>--Chọn phường xã--</option>';
                //     foreach ($selectWards as $key => $value) {
                //         $output .= '<option value="' . $value['xaid'] . '">' . $value['name_wards'] . '</option>';
                //     }
                // }
                $this->getResponse()
                    ->setHeader('Content-Type', 'text/html')
                    ->setBody($output);
            }
        }
        public function deleteAction() {
            $this->isAjax();
            if (empty($this->post_data['fee_id']) == false) {
                $mdlShippingRates = new ShippingRates();
                $data = $mdlShippingRates->getShippingRatesById($this->post_data['fee_id']);
                $reponse = $mdlShippingRates->deleteShippingRates($this->post_data['fee_id'],$data['fee_matp'], $data['fee_maqh']);
                if ($reponse >= 0) {
                    $this->ajaxResponse(CODE_SUCCESS);
                }
            }
            $this->ajaxResponse(CODE_HAS_ERROR);
        }

        public function listShippingRatesAction() {
            $this->isAjax();
            $draw = $this->post_data['draw']; // 
            $mdlShippingRates = new ShippingRates();
            //define columns
            $columns = array(// 
                0 => "fee_id",
                1 => "name_province",
                2 => "name_district",
                3 => "name_wards",
                4 => "fee_ship",
                5 => "status",
            );

            //order function
            if (empty($this->post_data["order"]) == false) {
                $this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
                $this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
            } else {
                $this->post_data["order"]["column"] = "updated_at";
                $this->post_data["order"]["dir"] = "desc";
            }
            // search function
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
            $count = count($mdlShippingRates->fetchAllShipping());
            //get filtered data
            unset($this->post_data['count_only']);
            $list = $mdlShippingRates->fetchAllShipping($this->post_data);
            $response["PostData"] = $this->post_data;
            $response["Response"]["Count"] = $count;
            $response["Response"]["List"] = $list;
            $response['draw'] = $draw;
            $this->_helper->json($this->returnResponseDatatable($response));
            exit;
        }
}