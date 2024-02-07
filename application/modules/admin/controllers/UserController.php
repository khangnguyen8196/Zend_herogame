<?php

/**
 * User page
 */
class Admin_UserController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/helper/core.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/selects/select2.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/notifications/jgrowl.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/sparkline.min.js', 'text/javascript'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_dev.css"));
        $this->loadJs(array('common', 'user'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        // load role list
        $mdlRole = new Role();
        $roleList = $mdlRole->fetchRoles(array('status' => STATUS_ACTIVE));
        $this->view->roleList = $roleList;
    }

    /**
     * Search page
     */
    public function listAction() {
        $this->isAjax();
        $mdlUser = new Users();

        $columns = array(
            0 => "user_id",
            1 => "phone_number",
            2 => "fullname",
            3 => "email",
            4 => 'role_id',
            5 => 'status',
            6 => 'created_at'
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
        $count = $mdlUser->fetchAllUsers($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $mdlUser->fetchAllUsers($this->post_data);

        //return data
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }

    /**
     * Delete user
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['userId']) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('parameters-error'));
        }
        $mdlUser = new Users();
        $userInfo = $mdlUser->fetchUserByParam(array('user_id' => $this->post_data['userId']));
        if (empty($userInfo) == false) {
            if ($userInfo['is_user_root'] == 1) {
                $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('not-allow-delete-default-account'));
            }
        } else {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('account-not-found'));
        }

        $reponse = $mdlUser->updateUser(array('status' => STATUS_DELETE), $this->post_data['userId']);
        if ($reponse > 0) {
            $this->ajaxResponse(CODE_SUCCESS, UtilTranslator::translate('delete-user-successful'));
        } else {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('delete-user-failed'));
        }
    }

    /**
     * Search page
     */
    public function addAction() {
        // load role list
        $mdlRole = new Role();
        $roleList = $mdlRole->fetchRoles(array('status' => STATUS_ACTIVE));
        $this->view->roleList = $roleList;
        $mdlUser = new Users();
        $error = array();
        if ($this->getRequest()->isPost()) {
            if (empty($this->post_data['username']) == true ) {
                $error[] = UtilTranslator::translate('please-enter-username');
            }

            if (empty($this->post_data['username']) == false) {
                $params['user_name'] = trim($this->post_data['username']);
                $response = $mdlUser->fetchUserByParam($params);

                if (empty($response) == false) {
                    $error[] = UtilTranslator::translate('username-existed');
                }
            }

            if (empty($this->post_data['first_name']) == true) {
                $error[] = UtilTranslator::translate('please-enter-first-name');
            }
            if (empty($this->post_data['last_name']) == true) {
                $error[] = UtilTranslator::translate('please-enter-last-name');
            }
            if (empty($this->post_data['password']) == true ) {
                $error[] = UtilTranslator::translate('please-enter-passwod');
            }
            if (empty($this->post_data['email']) == true) {
                $error[] = UtilTranslator::translate('please-enter-email');
            }
            if (empty($this->post_data['role']) == true ) {
                $error[] = UtilTranslator::translate('please-selec-role');
            }
            if (empty($error) == true) {
                $created = $this->getCreated();
                $updated = $this->getUpdated();

                $param['first_name'] = $this->post_data['first_name'];
                $param['last_name'] = $this->post_data['last_name'];
                $param['email'] = $this->post_data['email'];
                $param['status'] = STATUS_ACTIVE;
                $param['created_at'] = $created['created_at'];
                $param['updated_at'] = $updated['updated_at'];
                $param['created_by'] = $created['created_by'];
                $param['updated_by'] = $updated['updated_by'];
                $param['role_id'] = $this->post_data['role'];
                
                $salt = base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)) ;
                $options = [
                    'salt' => $salt,
                ];
                $pass = password_hash($this->post_data['password'], PASSWORD_BCRYPT, $options);
                $param['salt'] = $salt;

                $param['password'] = $pass;
                $param['user_name'] = $this->post_data['username'];
                // add new account 
                $reponses = $mdlUser->addUser($param);
                if ($reponses > 0) {
                    $this->view->success = 1;
                    $error[] = UtilTranslator::translate('create-account-information-successful');
                } else {
                    $error[] = UtilTranslator::translate('create-account-information-failed');
                }
            }
        }
        // Get update message 
        $msg = UtilSession::get('UPDATE_ACCOUNT_MSG');
        if (empty($msg) == false && is_array($msg) == true) {
            UtilSession::set('UPDATE_ACCOUNT_MSG', '');
            $key = key($msg);
            if ($key == 1) {
                $this->view->success = 1;
            }
            $error[] = $msg[$key];
        }
        $this->view->message = $error;
    }

    /**
     * 
     */
    public function changeUserInfoAction() {
        $this->isAjax();
        if (empty($this->post_data['userId']) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('parameters-error'));
        }
        $mdlUser = new Users();
        $updated = $this->getUpdated();
        $param['updated_by'] = $updated['updated_by'];
        $param['updated_at'] = $updated['updated_at'];

        if (empty($this->post_data['status']) == false) {
            $param['status'] = $this->post_data['status'];
        }

        if (empty($this->post_data['role']) == false) {
            $param['role_id'] = $this->post_data['role'];
        }

        $reponse = $mdlUser->updateUser($param, $this->post_data['userId']);

        if ($reponse > 0) {
            $this->ajaxResponse(CODE_SUCCESS, UtilTranslator::translate('update-user-information-successful'));
        } else {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('update-user-information-failed'));
        }
    }

}
