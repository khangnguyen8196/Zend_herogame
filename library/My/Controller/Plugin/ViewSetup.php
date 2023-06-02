<?php
/**
 * 
 */
class My_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract {
    /**
     * @var Zend_View
     */
    protected $_view;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'viewRenderer' );
        $viewRenderer->init ();
        
        $view = $viewRenderer->view;
        $this->_view = $view;

        // set up common variables for the view
        $view->module = strtolower ( $request->getModuleName () );
        $view->controller = strtolower ( $request->getControllerName () );
        $view->action = strtolower ( $request->getActionName () );
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        // set up doctype for any view helpers that use it
        $view->doctype ( 'HTML5' );

        // setup initial head place holders
        $view->headMeta()->setCharset('utf-8');
        $view->headMeta()->appendHttpEquiv( 'X-UA-Compatible', 'IE=edge, chrome=1' );
        $view->headMeta()->appendName('robots', 'index, follow');
//         $view->headMeta()->appendName('description', '');
//         $view->headMeta()->appendName('keywords', '');
        $view->headMeta()->appendName('format-detection', 'telephone=no');
        $view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1');
        $auto = new My_View_Helper_AutoRefreshRewriter();
        $view->headLink()->headLink ( array ('rel' => 'icon', 'href' => $auto->autoRefreshRewriter('/favicon.ico'), 'type' => 'image/x-icon' ), 'PREPEND' );
        $view->headLink()->headLink ( array ('rel' => 'apple-touch-icon', 'href' => $auto->autoRefreshRewriter('/favicon.ico') ), 'PREPEND' );
        $view->headLink()->headLink ( array ('rel' => 'canonical', 'href' => $actual_link ), 'PREPEND' );
        // Add helper path to View/Helper directory within this library
        $prefix = 'My_View_Helper';
        $dir = BASE_PATH . '/library/My/View/Helper';
        $view->addHelperPath ( $dir, $prefix );
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'viewRenderer' );
        $viewRenderer->init ();

        $view = $viewRenderer->view;
        $this->_view = $view;

        // set up common variables for the view
        $view->module = strtolower ( $request->getModuleName () );
        $view->controller = strtolower ( $request->getControllerName () );
        $view->action = strtolower ( $request->getActionName () );

        // Load Multi Language
        $session = new My_Controller_Action_Helper_Session ();
        $langCode = $session->getSession ( "LANG_CODE" );
        if (empty ( $langCode ) == FALSE) {
            switch ($langCode) {
                case 'vi' :
                    if (defined ( 'LANG_CODE' ) == FALSE) {
                        define ( 'LANG_CODE', $langCode );
                    }
                    break;
                default :
                    if (defined ( 'LANG_CODE' ) == FALSE) {
                        define ( 'LANG_CODE', 'vi' );
                    }
            }
        } else {
            if (defined ( 'LANG_CODE' ) == FALSE) {
                define ( 'LANG_CODE', 'vi' );
            }
        }

        $translate = UtilTranslator::loadTranslator( 'language' );
        Zend_Registry::set ( 'language', $translate );
        //Auto refresh
        $auto = new My_View_Helper_AutoRefreshRewriter();
        $view->headTitle()->setSeparator( ' - ' );
        if(  $view->module == 'admin') {
	        // Global stylesheets
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/icons/icomoon/styles.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/icons/fontawesome/styles.min.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/bootstrap.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/js/core/libraries/color-picker/css/bootstrap-colorpicker.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/core.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/components.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/colors.css"));
	        $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/css/site/_auth.css"));
	        // Core JS files
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/loaders/pace.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/core/libraries/jquery.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/core/libraries/bootstrap.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/notifications/bootbox.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/validation/validate.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/selects/bootstrap_multiselect.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/inputs/touchspin.min.js', 'text/javascript'));
                
	        if( $view->controller != 'order'){
	        	$view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/selects/select2.min.js', 'text/javascript'));
	        } else {
	        	$view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/ad-min/assets/js/libs/select2/select2.min.css"));
	        	$view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/libs/select2/select2.min.js', 'text/javascript'));
	        }
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/styling/switch.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/styling/switchery.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/styling/uniform.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/loaders/blockui.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/forms/styling/switchery.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/tables/datatables/datatables.min.js', 'text/javascript'));
	        $view->headScript()->offsetSetFile(100, $auto->autoRefreshRewriter('/ad-min/assets/js/core/app.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/md5/jquery.md5.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/pickers/datepicker.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/helper/core.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/helper/datetime.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/constant.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/translate.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/common.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/validation.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/change-pass.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/media/fancybox.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/auth.js', 'text/javascript'));
	        
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/uploaders/plupload/plupload.full.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/uploaders/plupload/plupload.queue.min.js', 'text/javascript'));
	        
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/masonry.pkgd.min.js', 'text/javascript'));
	        $view->headScript()->appendFile($auto->autoRefreshRewriter('/ad-min/assets/js/plugins/imagesloaded.pkgd.min.js', 'text/javascript'));
            } elseif(  $view->module == 'site') {
                $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/site/css/style.css"));
                $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/site/css/css-loader.css"));
                $view->headLink()->appendStylesheet($auto->autoRefreshRewriter("/site/css/_dev.css"));
                

                // load font
                //load library js
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/site.min.js', 'text/javascript'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/library/head.min.js', 'text/javascript'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/library/jquery.cookie.js', 'text/javascript'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/library/jquery.masknumber.js', 'text/javascript'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/library/jquery.form.min.js', 'text/javascript'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('//platform-api.sharethis.com/js/sharethis.js#property=5981940f52281100123ebd6a&product=inline-share-buttons'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/pages/common.js', 'text/javascript'));
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/slide-post-mobile.js', 'text/javascript'));
                
                
                $custInfo = UtilAuth::getCustommerLoginInfo();
            if (empty($custInfo) == true) {
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/pages/auth.js', 'text/javascript'));
            }else if(empty($custInfo) == false){
                $view->headScript()->appendFile($auto->autoRefreshRewriter('/site/js/pages/taikhoan.js', 'text/javascript'));
            }
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {

    }

}