<?php

/**
 * User page
 */
class Admin_ProfileController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/helper/core.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/selects/select2.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/notifications/jgrowl.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/visualization/sparkline.min.js', 'text/javascript'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_dev.css"));
        $this->loadJs(array('common', 'user'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        $loggedAccountInfo = '';
        if (empty($this->post_data['uid']) == false) {
            if ($this->post_data['uid'] != $this->login_info['user_id']) {
                $this->_redirect('/admin/profile?uid=' . $this->login_info['user_id']);
            }
        } else {
            $this->_redirect('/admin/profile?uid=' . $this->login_info['user_id']);
        }
        $id = $this->login_info['user_id'];
        $mdlUser = new Users();
        // get logged account information
        if (empty($id) == false) {
            $params['user_id'] = $id;
            $response = $mdlUser->fetchUserByParam($params);
            if (empty($response) == false) {
                $loggedAccountInfo = $response;
            }
        }
        $this->view->loggedAccountInfo = $loggedAccountInfo;
        $this->view->id = $id;
        $error = array();
        if ($this->getRequest()->isPost()) {
            if (empty($this->post_data['first_name']) == true) {
                $error[] = UtilTranslator::translate('please-enter-username');
            }
            if (empty($this->post_data['last_name']) == true) {
                $error[] = UtilTranslator::translate('please-enter-username');
            }
            if (empty($error) == true) {
                $updated = $this->getUpdated();

                $param['first_name'] = $this->post_data['first_name'];
                $param['last_name'] = $this->post_data['last_name'];
                $param['email'] = $this->post_data['email'];
                $param['updated_at'] = $updated['updated_at'];

                $reponses = $mdlUser->updateUser($param, $id);

                if ( $reponses > 0) {
                    UtilSession::set('UPDATE_PROFILE_MSG', array(1 => UtilTranslator::translate('information-has-been-updated')));
                    $par['user_id'] = $id;
                    $data = $mdlUser->fetchUserByParam($par);

                    if (empty($data) == false) {
                        unset($data ["password"]);
                        UtilAuth::setLoginInfo($data);
                    }
                } else {
                    UtilSession::set('UPDATE_PROFILE_MSG', array(-1 => UtilTranslator::translate('update-information-failed')));
                }
                $this->_redirect('/admin/profile?uid=' . $id);
            }
        }
        // Get update message 
        $msg = UtilSession::get('UPDATE_PROFILE_MSG');
        if (empty($msg) == false && is_array($msg) == true) {
            UtilSession::set('UPDATE_PROFILE_MSG', '');
            $key = key($msg);
            if ($key == 1) {
                $this->view->success = 1;
            }
            $error[] = $msg[$key];
        }
        $this->view->message = $error;
    }

}
