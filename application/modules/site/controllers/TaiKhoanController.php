<?php

/**
 * Main page
 */
class Site_TaiKhoanController extends FrontEndAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
    }

    /**
     * Search page
     */
    public function indexAction() {
        $this->_redirect("/");
    }
    
    public function thayDoiMatKhauAction() {
        $this->isAjax();
        if ( empty($this->post_data['cpf_opwd']) == true || empty($this->post_data['cpf_npwd']) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thiếu các thông tin cần thiết. Vui lòng kiểm tra lại!");
        }
        $uId = $this->customer_info["user_id"];
        
        $curtPass = $this->post_data['cpf_opwd'];
        $newPass = $this->post_data['cpf_npwd'];

        $mdlUser = new Users();
        $param['user_id'] = $uId;
        $data = $mdlUser->fetchUserByParam($param);
        if (empty($data) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Tài Khoản không tồn tại");
        }
        $salt = $data["salt"];
        $options = [
            'salt' => $salt,
        ];
        $curtPassEcrypted = password_hash($curtPass, PASSWORD_BCRYPT, $options);
        //check current password
        if ($curtPassEcrypted != $data["password"]) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Mật Khẩu hiện tại không trùng khớp.");
        }
        $cparams['updated_by'] = $this->customer_info["fullname"];
        $cparams['updated_at'] = date("Y-m-d H:i:s");
        
        $saltNew = base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
        $optionsNew = [
            'salt' => $saltNew,
        ];
        $pass = password_hash($newPass, PASSWORD_BCRYPT, $optionsNew);

        $cparams['password'] = $pass;
        $cparams['salt'] = $saltNew;
        
        try {
            $response = $mdlUser->updateUser($cparams, $uId);
            if ($response == 1) {
                $this->ajaxResponse(CODE_SUCCESS, "Thay đổi Mật Khẩu thành công.");
            } else {
                $this->ajaxResponse(CODE_HAS_ERROR, "Thay đổi Mật Khẩu thất bại");
            }
        } catch (Exception $ex) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thay đổi Mật Khẩu thất bại");
        }
    }
    
    public function capNhatThongTinAction(){
        $this->isAjax();
        if (empty($this->post_data['uif_email']) == true ||
                empty($this->post_data['uif_name']) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thiếu các thông tin cần thiết. Vui lòng kiểm tra lại!");
        }
        $uId = $this->customer_info["user_id"];
        $param = array();
        $param["email"] = $this->post_data['uif_email'];
        $param["fullname"] = $this->post_data['uif_name'];
        $param["phone_number"] = $this->post_data['uif_phone'];
        
        if(empty($this->post_data['uif_byear']) == false 
                && empty($this->post_data['uif_bmonth']) == false 
                && empty($this->post_data['uif_bday']) == false){
            $param["day_of_birth"] = $this->post_data['uif_byear'].'-'.$this->post_data['uif_bmonth']."-".$this->post_data['uif_bday'];
        }
        $param["gender"] = $this->post_data['uif_gender'];
        $mdlUser = new Users();
        $response = $mdlUser->updateUser($param, $uId);
        if ($response == 1) {
            $uInfo = $mdlUser->getUserById($uId);
            if( empty($uInfo) == false ){
                UtilAuth::setCustommerLoginInfo($uInfo);
                $this->ajaxResponse(CODE_SUCCESS, "Cập nhật thông tin Tài Khoản thành công.");
            }else{
                $this->ajaxResponse(CODE_HAS_ERROR, "Cập nhật thông tin Tài Khoản không thành công.");
            }
        } else {
            $this->ajaxResponse(CODE_HAS_ERROR, "Cập nhật thông tin Tài Khoản không thành công.");
        }
    }
    
    public function thongTinTaiKhoanAction() {
        $this->isAjax();
        $mdlUser = new Users();
        $uId = $this->customer_info["user_id"];
        $response = $mdlUser->getUserById($uId);
        $this->view->customer_info = $response;
        $html = $this->view->render("/tai-khoan/_thong-tin-tai-khoan.phtml");
        $this->ajaxResponse(CODE_SUCCESS, "", $html);
    }

}
