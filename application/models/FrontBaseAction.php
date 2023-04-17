<?php

/**
 * Base controller for crm module
 *
 */
class FrontBaseAction extends Zend_Controller_Action {

    protected $post_data;
    protected $request;
    protected $autorefresh = null;
    protected $login_info = null;
    protected $controller = '';
    protected $action = '';
    protected $module = '';
    protected $require_login = true;

    /**
     * Init
     * @see Zend_Controller_Action::init()
     */
    public function init() {
        $post = $this->_helper->Common->myTrim($this->_getAllParams());
        $this->post_data = $post;
        $this->request = $this->getRequest();
        //Action and Controller
        $this->action = $this->request->getActionName();
        $this->controller = $this->request->getControllerName();
        $this->module = $this->request->getModuleName();
        $this->login_info = UtilAuth::getLoginInfo();

        $this->view->login_info = $this->login_info;
        if( $this->require_login == true ){
        	$this->isLoggedIn();
        }
       
        //Auto refresh js
        $this->autorefresh = new My_View_Helper_AutoRefreshRewriter();
        $this->view->autorefresh = $this->autorefresh;
        //check permissions
        $this->view->hasViewPermission = UtilAuth::hasPrivilege($this->controller, ACTION_VIEW);
        $this->view->hasAddPermission = UtilAuth::hasPrivilege($this->controller, ACTION_ADD);
        $this->view->hasEditPermission = UtilAuth::hasPrivilege($this->controller, ACTION_EDIT);
        $this->view->hasDeletePermission = UtilAuth::hasPrivilege($this->controller, ACTION_DELETE);
        date_default_timezone_set("Asia/Ho_Chi_Minh");
    }

    /**
     * Check ajax request
     * @param boolean $isRedirect
     * @return boolean
     */
    public function isAjax($isRedirect = true) {
        $result = false;
        if ($this->request->isXmlHttpRequest()) {
            $result = true;
        } else {
            if ($isRedirect == true) {
                $this->_redirect('/');
            } else {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Set no login required
     */
    public function setNoLoginRequired() {
        $this->require_login = false;
    }

    /**
     * Translate
     * @param string $key
     * @param array $params
     */
    public function translate($key, $params = array()) {
        return UtilTranslator::translate($key, $params);
    }

    /**
     * Return ajax response
     * @param int $code
     * @param string $message
     * @param array $data
     * @param string $url
     */
    public function ajaxResponse($code, $message = '', $data = array(), $url = '') {
        $this->retData['Code'] = $code;
        $this->retData['Message'] = $message;
        $this->retData['Data'] = $data;
        $this->retData['Url'] = $url;
        $this->_helper->json($this->retData);
    }

    /**
     * Load translation
     * @param string $fileName
     */
    public function loadLanguage($fileName) {
        return UtilTranslator::loadTranslator($fileName);
    }

    /**
     * Load template content
     * @param string $template
     */
    public function loadTemplate($template) {
        $content = $this->view->render($template);
        $this->ajaxResponse(CODE_SUCCESS, '', $content);
    }

    /**
     * Check has view permission
     * @param unknown_type $url
     */
    public function hasViewPermission($url = "/") {
        if ($this->view->hasViewPermission == false) {
            if ($this->isAjax(false) == true) {
                $this->ajaxResponse(CODE_PERMISSION_DENIED);
            } else {
                $this->_redirect($url);
            }
        }
    }

    /**
     * Load translation
     * @param mix $fileName
     */
    public function loadJs($fileName) {
        if (is_array($fileName) == true) {
            foreach ($fileName as $value) {
                $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/' . $value . '.js', 'text/javascript'));
            }
        } else {
            $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/' . $fileName . '.js', 'text/javascript'));
        }
    }

    public function isLoggedIn() {
        $result = true;
        if( $this->module == 'admin'){
	        if (empty($this->view->login_info) == true) {
	            if ($this->isAjax(false) == true) {
	                $this->ajaxResponse(CODE_SESSION_EXPIRED);
	            } else {
	                $this->_redirect('/admin/auth/login');
	            }
	        }
        }
        return $result;
    }

    /**
     * Return datatable response
     * @param array $data
     * @return multitype:unknown string number Ambigous <multitype:, boolean>
     */
    public function returnResponseDatatable($data) {
        $draw = "";
        if (empty($data["PostData"]["draw"]) == false) {
            $draw = $data["PostData"]["draw"];
        }
        $count = 0;
        if (empty($data["Response"]) == false && empty($data["Response"]["Count"]) == false) {
            $count = $data["Response"]["Count"];
        }
        $list = array();
        if (empty($data["Response"]) == false && empty($data["Response"]["List"]) == false) {
            $list = $data["Response"]["List"];
        }
        //permission

        if ($this->controller == 'user') {
            if (empty($list) == false && is_array($list)) {
                foreach ($list as $key => $user) {
                    if ($this->view->hasEditPermission) {
                        $list[$key]["edit_permission"] = true;
                    } else {
                        $list[$key]["edit_permission"] = false;
                    }
                    if ($this->view->hasDeletePermission && $user["role_id"] != $this->login_info["role_id"]) {
                        $list[$key]["delete_permission"] = true;
                    } else {
                        $list[$key]["delete_permission"] = false;
                    }
                }
            }
        } else {
            $edit_permission = false;
            if ($this->view->hasEditPermission) {
                $edit_permission = true;
            }

            $delete_permission = false;
            if ($this->view->hasDeletePermission) {
                $delete_permission = true;
            }

            if (empty($list) == false && is_array($list)) {
                foreach ($list as $key => $val) {
                    $list[$key]["edit_permission"] = $edit_permission;
                    $list[$key]["delete_permission"] = $delete_permission;
                }
            }
        }
        //return data
        $return = array();
        $return['draw'] = $draw;
        $return['recordsTotal'] = $count;
        $return['recordsFiltered'] = $count;
        $return['data'] = $list;
        return $return;
    }

    public function checkInputData($xml, &$data, $ignore = false) {
        $errors = UtilValidator::check($xml, $data, $option = array('isTranslate' => true), $ignore);
        return $errors;
    }

    public function getUpdated() {
        $now = date("Y-m-d H:i:s");
        $login = UtilAuth::getLoginInfo();
        return array('updated_at' => $now, 'updated_by' => $login['user_name']);
    }

    public function getCreated() {
        $now = date("Y-m-d H:i:s");
        $login = UtilAuth::getLoginInfo();
        return array('created_at' => $now,'updated_at' => $now, 'created_by' => $login['user_name']);
    }

}
