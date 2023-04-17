<?php

/**
 * Permission page
 */
class Admin_PermissionController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->isLoggedIn();
        $this->hasViewPermission();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/styling/uniform.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/selects/select2.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/notifications/jgrowl.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/visualization/sparkline.min.js', 'text/javascript'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_dev.css"));

        $this->loadJs(array('common', 'permission'));
    }

    /**
     * Search page
     */
    public function indexAction() {
        
    }

    /**
     * Search page
     */
    public function listAction() {
        $this->isAjax();
        $mdlRole = new Role();

        $columns = array(
            0 => "role_id",
            1 => "role_name",
            2 => 'status',
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
        $count = $mdlRole->fetchRoles($this->post_data);
        //get filtered data
        unset($this->post_data['count_only']);
        $list = $mdlRole->fetchRoles($this->post_data);
        //return data
        $response["PostData"] = $this->post_data;
        $response["Response"]["Count"] = $count;
        $response["Response"]["List"] = $list;
        $this->_helper->json($this->returnResponseDatatable($response));
        exit;
    }

    /**
     * 
     */
    public function addAction() {
        $role = new Role();
        // prepare resource
        $resourcesList = $this->prepareResource();
        $this->view->resourcesList = $resourcesList;

        $roleInfo = '';
        $id = '';
        if (empty($this->post_data['id']) == false) {
            $id = $this->post_data['id'];
            $result = $role->fetchRoleByParam( array ( 'role_id' => $this->post_data['id'] ) );
            if (empty($result) == false) {
                $result["permission"] = explode(',', $result["permission"]);
                $roleInfo = $result;
            }
        }
        $this->view->roleInfo = $roleInfo;
        $this->view->id = $id;

        $error = array();
        if ($this->getRequest()->isPost() == true) {
            if (empty($this->post_data['id']) == true && empty($this->post_data['p_name']) == true) {
                    $error[] = UtilTranslator::translate('missing-permission-name');
            }
            if (empty($this->post_data['p_name']) == false) {
                $response = $role->fetchRoleByParam(array( 'role_name' => $this->post_data['p_name'] ) );
                if (empty($response) == false  ) {
                    $error[] = UtilTranslator::translate('permission-existed');
                }
            }
            if (empty($this->post_data['permissions']) == true ) {
                    $error[] = UtilTranslator::translate('please-select-at-least-one-permission');
            }
            if (empty($error) == true) {
                $data['permission'] = implode(",", $this->post_data["permissions"]);
                $data['status'] = STATUS_ACTIVE;
                if (empty($this->post_data['id']) == false) {
                    $updated = $this->getUpdated();
                    $data['updated_at'] = $updated['updated_at'];
                    $data['updated_by'] = $updated['updated_by'];
                    $data['role_id'] = $this->post_data['id'];

                    $roleId = $role->updateRole($data);
                    if ($roleId > 0) {
                        UtilSession::set('UPDATE_PERMISSION_MSG', array( 1 => UtilTranslator::translate('information-has-been-updated')));
                    } else {
                        UtilSession::set('UPDATE_PERMISSION_MSG', array( -1 => UtilTranslator::translate('update-information-failed')));
                    }
                    $this->_redirect('/admin/permission/add?id=' . $this->post_data['id']);
                } else {

                    $created = $this->getCreated();
                    $data['created_at'] = $created['created_at'];
                    $data['created_by'] = $created['created_by'];
                    $data['role_name'] = $this->post_data['p_name'];

                    $roleId = $role->insertRole($data);
                    if ($roleId > 0) {
                        $this->view->success = 1;
                        $error[] = UtilTranslator::translate('create-permission-information-successful');
                    } else {
                        $error[] = UtilTranslator::translate('create-permission-information-failed');
                    }
                }
            }
        }
        // Get update message 
        $msg = UtilSession::get('UPDATE_PERMISSION_MSG');
        if (empty($msg) == false && is_array($msg) == true) {
            UtilSession::set('UPDATE_PERMISSION_MSG', '');
            $key = key($msg);
            if( $key == 1 ){
                $this->view->success = 1;
            }
            $error[] = $msg[$key];
        }
        $this->view->message = $error;
    }

    /**
     * Add resource from Constant to database
     */
    private function prepareResource() {
//        $front = $this->getFrontController();
//        $acl = array();
//        foreach ($front->getControllerDirectory() as $module => $path) {
//            foreach (scandir($path) as $file) {
//                if (strstr($file, "Controller.php") !== false) {
//                    include_once $path . DIRECTORY_SEPARATOR . $file;
//                    foreach (get_declared_classes() as $class) {
//                        if (is_subclass_of($class, 'Zend_Controller_Action')) {
//                            $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
//                            
//                            $actions = array();
//                            foreach (get_class_methods($class) as $action) {
//                                if (strstr($action, "Action") !== false) {
//                                    $action = substr($action,0,strpos($action,"Action"));
//                                    $actions[] = $action;
//                                }
//                            }
//                        }
//                    }
//                    $c = explode('_', $controller);
//                    $controller = $c[1];
//                    $acl[$module][ $controller ] = $actions;
//                }
//            }
//        }
        $path = APPLICATION_PATH . '/xml/resource.xml';
        $xmlObj = new Zend_Config_Xml($path);
        $result = $xmlObj->toArray();

        $resourceModel = new Model_Acl_Resources();
        foreach ($result as $controller => $controllerInfo) {
            foreach ($controllerInfo['permissions'] as $action => $permissionInfo) {
                $where = "'" . $controller . ':' . $permissionInfo['action_name'] . "'";
                $resource = $resourceModel->fetchRow("CONCAT( controller_name, ':', action_name ) = $where");
                if (!$resource) {
                    $data = array(
                        'permission_name' => $permissionInfo['permission_name'],
                        'module_name' => $permissionInfo['module_name'],
                        'controller_name' => $controller,
                        'action_name' => $permissionInfo['action_name']
                    );
                    $id = $resourceModel->saveData($data);
                } else {
                    $id = $resource->permission_id;
                }
                $result[$controller]['permissions'][$action]["id"] = $id;
            }
        }
        return $result;
    }

    /**
     * 
     */
    public function deleteAction() {
        $this->isAjax();
        if (empty($this->post_data['id']) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('parameters-error'));
        }
        $mdlRole = new Role();
        $roleInfo = $mdlRole->fetchRoleByParam( array ( 'role_id' => $this->post_data['id'] ) );

        if (empty($roleInfo) == true) {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('permission-not-found'));
        }
        $reponse = $mdlRole->updateRole(array('status' => STATUS_DELETE, 'role_id' => $this->post_data['id']));
        if ($reponse > 0) {
            $this->ajaxResponse(CODE_SUCCESS, UtilTranslator::translate('delete-permission-successful'));
        } else {
            $this->ajaxResponse(CODE_HAS_ERROR, UtilTranslator::translate('delete-permission-failed'));
        }
    }

}
