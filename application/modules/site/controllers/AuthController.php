<?php

/**
 * Main page
 */
class Site_AuthController extends FrontEndAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
    }

    /**
     * Search page
     */
    public function indexAction() {
        
    }

    private function authenCustommer($username, $password) {
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
        $auth->setStorage(new Zend_Auth_Storage_Session($namespace, 'CUSTOMER_LOGIN'));

        $retData['result'] = $auth->authenticate($authAdapter);
        $retData['data_auth'] = $authAdapter->getResultRowObject(null, 'password');

        return $retData;
    }

    public function dangNhapAction() {
        $this->isAjax();
        if (empty($this->customer_info) == false) {
           $this->ajaxResponse(CODE_SUCCESS, "", "/");
        }
        $error = array();
        $data = $this->post_data;
        if (empty($data['sif_usr']) == true) {
            $error[] = "Vui lòng nhập Tên Đăng nhập";
        }
        if (empty($data['sif_pwd']) == true) {
            $error[] = "Vui lòng nhập Mật khẩu";
        }
        
        if (empty($error) == true) {
            $mdlUser = new Users();
            $username = $data["sif_usr"];
            $password = $data["sif_pwd"];
            $encryptPassword = md5($password);
            $uData = $mdlUser->fetchUserByParam(array('user_name' => $data["sif_usr"]));
            if (empty($uData["salt"]) == false) {
                $salt = $uData["salt"];
                $options = [
                    'salt' => $salt,
                ];
                $encryptPassword = password_hash($password, PASSWORD_BCRYPT, $options);
            }
            // Authenticating user login information result
            // $password must be sha1 encrypt
            $authResult = $this->authenCustommer($username, $encryptPassword);
            
            // Check authenticate user info result
            if (empty($authResult['data_auth']) == false) {
                $loginInfo = (array) $authResult['data_auth'];
                UtilAuth::setCustommerLoginInfo($loginInfo);
                setcookie('LOGIN', '', time() - 3600, '/');
                $arr['failed_login_attempt'] = 0;
                $mdlUser->updateUser($arr, $loginInfo['user_id']);
                
                if (empty($this->post_data['act']) == false && $this->post_data['act'] == 'giohang') {
                    $this->ajaxResponse(CODE_SUCCESS, "", "/don-hang/gio-hang");
                } else {
                    $this->ajaxResponse(CODE_SUCCESS, "", "/");
                }
            } else {
                $data = $mdlUser->fetchUserByParam(array('user_name' => $username));
                if (empty($data) == false) {
                    if ($data ["password"] == $encryptPassword) {
                        if ($data ["status"] == STATUS_IN_ACTIVE) {
                            $error[] = "Tài khoản hiện không kích hoạt";
                        } else if ($data ["status"] == STATUS_LOCKED) {
                            $error[] = "Tài khoản đã bị khóa";
                        }
                    } else {
                        $time = $data['failed_login_attempt'];
                        if ($time >= MAX_LIMIT_LOGIN_TIME) {
                            $arr['status'] = STATUS_LOCKED;
                            $mdlUser->updateUser($arr, $data['user_id']);
                            $error[] = "Tài khoản đã bị khóa";
                        } else {
                            $arr['failed_login_attempt'] = $time + 1;
                            $mdlUser->updateUser($arr, $data['user_id']);

                            $error[] = "Tên Đăng nhập hoặc Mật khẩu không chính xác";
                        }
                    }
                } else {
                    $error[] = "Tên Đăng nhập hoặc Mật khẩu không chính xác";
                }
            }
        }
        //$this->view->message = $error;
        if (empty($error) == true) {
            $this->ajaxResponse(CODE_SUCCESS);
        } else {
            $error = implode("<br>", $error);
            $this->ajaxResponse(CODE_HAS_ERROR, $error);
        }
    }

    /**
     *
     * @param type $firstName
     * @return type
     */
    public function _generateTempPassword($name) {
    	$name= str_replace(" ", "", $name);
    	$name= strtolower($name);
    	return base64_encode($name);
    }

    public function quenMatKhauAction() {
		$this->isAjax ();
		$error = array ();
		if (empty ( $this->post_data ['fpf_usr'] ) == true) {
			$error [] = 'Vui lòng nhập tên đăng nhập';
		}
		if (empty ( $this->post_data ['fpf_email'] ) == true) {
			$error [] = 'Vui lòng nhập email';
		}
		$username = $this->post_data ['fpf_usr'];
		$email = $this->post_data ['fpf_email'];
		
		$mdlUser = new Users ();
		$param ['user_name'] = $username;
		$param ['email'] = $email;
		
		$data = $mdlUser->fetchUserByParam ( $param );
		
		if (empty ( $data ) == true) {
			$error [] = 'Tên Đăng Nhập hoặc Email không tồn tại';
		}
		if (empty ( $error ) == true) {
			$token = self::_generateTempPassword ( $data ['fullname'] );
			
			$uParam ['updated_by'] = $data ['fullname'];
			$uParam ['updated_at'] = date ( "Y-m-d H:i:s" );
			$uParam ['reset_token'] = md5 ( $token );
			
			$mdlUser->updateUser ( $uParam, $data ['user_id'] );
			
			$data ['reset_token'] = $uParam ['reset_token'];
			
			$this->view->data = $data;
			$body = $this->view->render ( '/auth/_forgot-password-email-template.phtml' );
			UtilEmail::sendMail ( DEFAULT_EMAIL, $data ['email'], "Yêu cầu đặt lại Mật Khẩu", $body );
			$this->ajaxResponse(CODE_SUCCESS);
		}
		$error = implode("<br>", $error);
		$this->ajaxResponse(CODE_HAS_ERROR, $error);
    }

    public function datLaiMatKhauAction() {
		$this->isAjax ();
		if (empty ( $this->post_data ['t'] ) == true && empty ( $this->post_data ['e'] ) == true) {
			$this->ajaxResponse ( CODE_HAS_ERROR );
		}
		
		$error = array ();
		$email = $this->post_data ['e'];
		$token = $this->post_data ['t'];
		$password = $this->post_data ['cpf_npwd'];
		
		$mdlUser = new Users ();
		$param ['reset_token'] = $token;
		$param ['email'] = $email;
		
		$data = $mdlUser->fetchUserByParam ( $param );
		if (empty ( $data ) == true) {
			$error [] = "Tài Khoản không tồn tại";
		}
		if (empty ( $error ) == true) {
			$saltNew = base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
			$optionsNew = [
					'salt' => $saltNew,
			];
			$pass = password_hash($password, PASSWORD_BCRYPT, $optionsNew);
			
			$uParam ['password'] = $pass;
			$uParam ['reset_token'] = '';
			$uParam ['salt'] = $saltNew;
			
			$res = $mdlUser->updateUser ( $uParam, $data ['user_id'] );
			if ($res > 0) {
				$this->ajaxResponse ( CODE_SUCCESS );
			} else {
				$this->ajaxResponse ( CODE_HAS_ERROR );
			}
		}
		$this->ajaxResponse ( CODE_HAS_ERROR );
	}
    /**
     * Index page
     */
    public function dangXuatAction() {
    	// Clear all session of browser
    	Zend_Session::destroy();
        setcookie('token', null, -1, '/');
    	$this->_redirect( '/' );
    }
    
    /**
     * 
     */
    public function kiemTraDangNhapAction() {
        $this->isAjax();
        $result = $this->isLoggedIn();
        if ($result == true) {
            $this->ajaxResponse(CODE_SUCCESS);
        }
    }
    /*
     * Search page
     */
    public function dangKyAction() {
        $this->isAjax();
        $error = array();
        $success = 0;
        $userName = $this->post_data['suf_usr']; // check user name
        $email = $this->post_data['suf_email']; // check email
        $firstName = "";
        $lastName = "";
        $fullname = $this->post_data['suf_name'];
        $password = $this->post_data['suf_pwd'];
        $rePassword = $this->post_data['suf_rpwd'];
        // check user name exists
        $user = new Users();
        $checkName['user_name'] = $userName;
        $rs = $user->fetchUserByParam($checkName);
        if (empty($rs) == false) {
            $error[] = 'Tài khoản đã tồn tại trong hệ thống.';
        }
        // check email
        $checkMail['email'] = $email;
        $rs = $user->fetchUserByParam($checkMail);
        if (empty($rs) == false) {
            $error[] = 'Email đã được sử dụng';
        }
        if (strlen($password) < 6) {
            $error[] = 'Mật Khẩu có ít nhất 6 ký tự';
        }
        // check password
        if ($password != $rePassword) {
            $error[] = 'Xác nhận Mật Khẩu không trùng khớp.';
        }

        $param['first_name'] = $firstName;
        $param['last_name'] = $lastName;
        $param['fullname'] = $fullname;
        $param['email'] = $email;
        $param['status'] = STATUS_ACTIVE;
        $param['created_at'] = date("Y-m-d H:i:s");
        $param['updated_at'] = date("Y-m-d H:i:s");
        $param['created_by'] = $userName;
        $param['updated_by'] = $userName;
        $param['role_id'] = CUSTOMER;
        $param['user_name'] = $userName;

        if (empty($error) == true) {
            $salt = base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
            $options = [
                'salt' => $salt,
            ];
            $pass = password_hash($password, PASSWORD_BCRYPT, $options);
            $param['password'] = $pass;
            $param['salt'] = $salt;
            // add new account
            $reponses = $user->addUser($param);
            if ($reponses > 0) {
                $userInfo["user_id"] = $reponses;
                $rs = $user->fetchUserByParam($userInfo);
                if (empty($rs) == false) {
                    $success = 1;
                    UtilAuth::setCustommerLoginInfo($rs);
                } else {
                    $error[] = 'Tạo Tài Khoản không thành công.';
                }
            } else {
                $error[] = 'Tạo Tài Khoản không thành công.';
            }
        }

        if (empty($error) == true) {
            $this->ajaxResponse(CODE_SUCCESS);
        } else {
            $error = implode("<br>", $error);
            $this->ajaxResponse(CODE_HAS_ERROR, $error);
        }
    }

}
