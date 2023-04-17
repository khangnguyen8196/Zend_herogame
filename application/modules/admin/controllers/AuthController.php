<?php

/**
 * Main page
 */
class Admin_AuthController extends FrontBaseAction {

    protected $_login = '';

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
    	if($this->action = 'login'){
    		$this->setNoLoginRequired();
    	}
        parent::init();
       $this->loadJs('auth');
       $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_auth.css"));
    }

    public function indexAction() {
        if (empty($this->view->login_info) == false) {
            $this->_redirect('/admin/index/');
        } else {
            $this->_redirect('/admin/auth/login');
        }
    }

    /**
     * Search page
     */
    public function loginAction() {
		if (empty($this->view->login_info) == false) {
            $this->_redirect('/admin/index/');
        }
        $error = array();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if (empty($data['username']) == true) {
                $error[] = "Vui lòng nhập Tên Đăng Nhập";
            }
            if (empty($data['password']) == true) {
                $error[] = "Vui lòng nhập Mật Khẩu";
            }
            if (empty($error) == true) {
                $mdlUser = new Users();
                $username = $data["username"];
                $password = $data["password"];
                $encryptPassword = md5($password);
                $uData = $mdlUser->fetchUserByParam(array('user_name' => $data["username"]));
                if (empty($uData["salt"]) == false) {
                    $salt = $uData["salt"];
                    $options = array(
                        'salt' => $salt,
                    );
                    $encryptPassword = password_hash($password, PASSWORD_BCRYPT, $options);
                }
                // Authenticating user login information result
                // $password must be sha1 encrypt
                $authResult = $this->authUser($username, $encryptPassword);
                // Check authenticate user info result
                if (empty($authResult['data_auth']) == false) {
                    $loginInfo = (array)$authResult['data_auth'];
                    if( $loginInfo['role_id'] == CUSTOMER ){
                    	$this->_redirect("/index/");
                    }
                    UtilAuth::setLoginInfo($loginInfo);
                    setcookie('LOGIN', '', time() - 3600, '/');
                    $arr['failed_login_attempt'] = 0;
                    $mdlUser->updateUser( $arr , $loginInfo['user_id'] );
                    $this->_redirect("/admin/index/");
                } else {
                    $data = $mdlUser->fetchUserByParam(array('user_name' => $data["username"]));
                    if (empty($data) == false) {
                        if ($data ["password"] == $encryptPassword ) {
                            if( $data ["status"] == STATUS_IN_ACTIVE ){
                                $error[] = "Tài Khoản hiện không kích hoạt";
                            }else if( $data ["status"] == STATUS_LOCKED){
                                $error[] = "Tài Khoản đã bị khóa";
                            }
                        } else {
                            $time = $data['failed_login_attempt'];
                            if( $time >= MAX_LIMIT_LOGIN_TIME ){
                                $arr['status'] = STATUS_LOCKED;
                                $mdlUser->updateUser( $arr , $data['user_id'] );
                                $error[] = "Tài Khoản đã bị khóa";
                            }else{
                                $arr['failed_login_attempt'] = $time + 1 ;
                                $mdlUser->updateUser($arr, $data['user_id'] );
                                
                                $error[] = "Tên Đăng Nhập hoặc Mật Khẩu không chính xác";
                            }
                        }
                    } else {
                        $error[] = "Tên Đăng nhập hoặc Mật Khẩu không chính xác";
                    }
                }
            }
        }
        $this->view->message = $error;
    }

    /**
     * 
     * @param type $username
     * @param type $password
     * @return type
     */
    private function authUser($username, $password) {
        $retData = array();
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('user');
        $authAdapter->setIdentityColumn('user_name');
        $authAdapter->setCredentialColumn('password');
        $authAdapter->setCredentialTreatment('?');
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential(( $password));
        $authAdapter->getDbSelect()->where('status = "1"');

        $auth = Zend_Auth::getInstance();
        $namespace = Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
        $auth->setStorage(new Zend_Auth_Storage_Session($namespace, 'LOGIN'));

        $retData['result'] = $auth->authenticate($authAdapter);
        $retData['data_auth'] = $authAdapter->getResultRowObject(null, 'password');

        return $retData;
    }

    /**
     * Simple code 
     */
    public function changePasswordAction() {
        $this->isLoggedIn();
        $this->view->uId = @$this->post_data['uId'];
        if (empty($this->post_data['uId']) == true ||
                empty($this->post_data['curentPassword']) == true ||
                empty($this->post_data['newPassword']) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Thiếu các thông tin cần thiết. Vui lòng kiểm tra lại!");
        }
        $uId = $this->post_data['uId'];
        
        $curtPass = base64_decode($this->post_data['curentPassword']);
        $newPass = base64_decode($this->post_data['newPassword']);

        $mdlUser = new Users();
        $param['user_id'] = $uId;
        $data = $mdlUser->fetchUserByParam($param);
        if (empty($data) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Tài Khoản không tồn tại");
        }
        $salt = $data["salt"];
        $options = array(
            'salt' => $salt,
        
        );
        $curtPassEcrypted = password_hash($curtPass, PASSWORD_BCRYPT, $options);
        //check current password
        if ($curtPassEcrypted != $data["password"]) {
            $this->ajaxResponse(CODE_HAS_ERROR, "Mật Khẩu hiện tại khồng trùng khớp.");
        }
        $updated = $this->getUpdated();
        $cparams['updated_by'] = $updated['updated_by'];
        $cparams['updated_at'] = $updated['updated_at'];
        
        $saltNew = base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
        $optionsNew = array(
            'salt' => $saltNew,
        );
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

    /**
     * 
     */
    public function forgotPasswordAction() {
        $error = array();
        if ($this->request->isPost() == true) {
            if (empty($this->post_data['f_username']) == true) {
                $error[] = "Vui lòng nhập Tên Đăng Nhập";
            }
            if (empty($this->post_data['f_email']) == true) {
                $error[] = "Vui lòng nhập Email";
            }
            $username = $this->post_data['f_username'];
            $email = $this->post_data['f_email'];

            $mdlUser = new Users();
            $param['user_name'] = $username;
            $param['email'] = $email;

            $data = $mdlUser->fetchUserByParam($param);
            if (empty($data) == true) {
                $error[] = "Tài Khoản không tồn tại";
            }
            if (empty($error) == true) {
                $token = self::_generateTempPassword($data['first_name']);
                
                $updated = $this->getUpdated();
                $uParam['updated_by'] = $updated['updated_by'];
                $uParam['updated_at'] = $updated['updated_at'];
                $uParam['reset_token'] = md5($token);
                
                $mdlUser->updateUser($uParam, $data['user_id']);

                $data['reset_token'] = $uParam['reset_token'];

                $this->view->data = $data;
                $body = $this->view->render('/admin/auth/_forgot-password-email-template.phtml');

                UtilEmail::sendMail(DEFAULT_EMAIL, $data['email'], "Yêu cầu đặt lại Mật Khẩu", $body);
                $this->view->success = 1;
                $error[] = "Email xác nhận đặt lại mật khẩu đã được gửi đi. Vui lòng kiểm tra Email!";
            }
        }
        $this->view->message = $error;
    }

    /**
     * 
     * @param type $firstName
     * @return type
     */
    public function _generateTempPassword($firstName) {
        $firstName = str_replace(" ", "", $firstName);
        $firstName = strtolower($firstName);
        return $firstName . rand(100, 999);
    }

    /**
     * 
     */
    public function resetPasswordAction() {
        $this->view->token = @$this->post_data['t'];
        $this->view->email = @$this->post_data['e'];
        $error = array();
        if ($this->request->isPost() == true) {
            if (empty($this->post_data['e']) == true) {
                $error[] = "Vui lòng nhập email";
            }
            if (empty($this->post_data['t']) == true) {
                $error[] = "Vui lòng cung cấp Token";
            }
            if (empty($this->post_data['r_new_password']) == true) {
                $error[] = "Vui lòng nhập Mật Khẩu mới";
            }
            $email = $this->post_data['e'];
            $token = $this->post_data['t'];
            $password = $this->post_data['r_new_password'];

            $mdlUser = new Users();
            $param['reset_token'] = $token;
            $param['email'] = $email;

            $data = $mdlUser->fetchUserByParam($param);
            if (empty($data) == true) {
                $error[] = "Tài Khoản không tồn tại";
            }
            if (empty($error) == true) {
                
                $updated = $this->getUpdated();
                $uParam['updated_by'] = $updated['updated_by'];
                $uParam['updated_at'] = $updated['updated_at'];
                $uParam['password'] = md5($password);
                $uParam['reset_token'] = '';
                
                $res = $mdlUser->updateUser($uParam, $data['user_id']);
                if ($res > 0) {
                    $this->view->success = 1;
                    $this->view->token = '';
                    $this->view->email = '';
                    $error[] = "Thay đổi Mật Khẩu thành công";
                } else {
                    $error[] = "Thay đổi Mật Khẩu thất bại";
                }
            }
        }
        $this->view->message = $error;
    }
    /**
     * Index page
     */
    public function logoutAction() {
    	// Clear all session of browser
    	Zend_Session::destroy();
    	$this->_redirect( '/admin/auth/login' );
    }

}
