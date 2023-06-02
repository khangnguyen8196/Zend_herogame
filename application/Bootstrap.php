<?php
/**
 * The application bootstrap used by Zend_Application
 *
 * @category   Bootstrap
 * @package    Bootstrap
 * @copyright  Copyright (c) 2008 Keith Pope (http://www.thepopeisdead.com)
 * @license    http://www.thepopeisdead.com/license.txt     New BSD License
 */
class Bootstrap extends Zend_Application_Bootstrap_BootstrapAbstract {
    /**
     *
     * @var Zend_Log
     */
    protected $_logger;

    /**
     *
     * @var Zend_Application_Module_Autoloader
     */
    protected $_resourceLoader;

    /**
     *
     * @var Zend_Controller_Front
     */
    public $frontController;

    /**
     * Add the config to the registry
     */
    protected function _initConfig() {
        Zend_Registry::set ( 'config', $this->getOptions () );
    }

    /**
     * Setup the logging
     */
    protected function _initLogging() {
        $this->bootstrap ( 'frontController' );

        // Read log configurations
        $config = $this->getOptions ();
        $configLog = $config ['logging'];

        $writer = TRUE == $configLog ['enabled'] ? new Zend_Log_Writer_Stream ( $configLog ['logfile'] ) : new Zend_Log_Writer_Null ();
        $logger = new Zend_Log ( $writer );

        // Set log policy per each enviroment
        switch ($this->getEnvironment ()) {
            case 'test' :
                $filter = new Zend_Log_Filter_Priority ( Zend_Log::WARN );
                break;
            case 'production' :
                $filter = new Zend_Log_Filter_Priority ( Zend_Log::CRIT );
                break;
            case 'development' :
            default :
                $filter = new Zend_Log_Filter_Priority ( Zend_Log::DEBUG );
                break;
        }
        $logger->addFilter ( $filter );

        $this->_logger = $logger;
        Zend_Registry::set ( 'Zend_Log', $logger );
    }

    /**
     * Setup request and response so we can use Firebug for logging
     * also make the dispatcher prefix the default module
     */
    protected function _initFrontControllerSettings() {
        $this->bootstrap ( 'frontController' );
        $this->frontController->setResponse ( new Zend_Controller_Response_Http () );
        $this->frontController->setRequest ( new Zend_Controller_Request_Http () );
    }

    /**
     * Configure the default modules autoloading, here we first create
     * a new module autoloader specifiying the base path and namespace
     * for our default module.
     * This will automatically add the default
     * resource types for us. We also add two custom resources for Services
     * and Model Resources.
     */
    protected function _initDefaultModuleAutoloader() {
        $autoLoader = Zend_Loader_Autoloader::getInstance ();
        $loader = new My_Loader ();
        $autoLoader->pushAutoloader ( $loader );
        $autoLoader->setFallbackAutoloader ( true );

        $this->_resourceLoader = new Zend_Application_Module_Autoloader ( array ('namespace' => 'My', 'basePath' => APPLICATION_PATH ) );
        $this->_resourceLoader->addResourceTypes ( array (
        		'siteControllerHelper' => array ('path' => 'modules/site/controllers/helpers', 'namespace' => 'Site_Controller_Helper' ), 
        		'siteViewHelper' => array ( 'path' => 'modules/site/views/helpers', 'namespace' => 'Site_View_Helper' ),
        		'adminViewHelper' => array( 'path' => 'modules/admin/views/helpers', 'namespace' => 'Admin_View_Helper' ), 
        		'adminControllerHelper' => array( 'path' => 'modules/admin/controllers/helpers', 'namespace' => 'Admin_Controller_Helper' )
         ) );
        $resourceLoader = new Zend_Loader_Autoloader_Resource ( array ('basePath' => APPLICATION_PATH, 'namespace' => '', 'resourceTypes' => 
        		array ('model' => array ('path' => 'models', 'namespace' => 'Model' ) ) ) );

    }
    /**
     * Setup Helpers
     */
    protected function _initHelper() {
        /*
         * Add common controller action helper
        */
        $helperPath = BASE_PATH . '/library/My/Controller/Action/Helper';
        Zend_Controller_Action_HelperBroker::addPath ( $helperPath, "My_Controller_Action_Helper" );

        /*
         * Add common view helper
        */
        $helperPath = BASE_PATH . '/library/My/View/Helper';
        Zend_Controller_Action_HelperBroker::addPath ( $helperPath, "My_View_Helper" );
    }

    /**
     * Setup locale
     */
    protected function _initLocale() {
    }

    /**
     * Setup the view
     */
    protected function _initView() {
        Zend_Layout::startMvc ( array ('layout' => 'site', 'layoutPath' => APPLICATION_PATH . '/layouts/scripts', 'pluginClass' => 'My_Controller_Plugin_LayoutSetup' ) );
    }

    /**
     * Setup multiple database adapters
     */
    protected function _initDb() {
        $configDb = $this->getOption ( 'db' );
        $configProfiler = $this->getOption ( 'profiler' );
        $dbAdapters = array ();

        foreach ( $configDb as $key => $db ) {
            $dbAdapter = Zend_Db::factory ( $db ['adapter'], $db ['params'] );

            if ($configProfiler ['enabled'] == true && $this->getEnvironment () != 'production') {
                $profiler = new Zend_Db_Profiler ( true );
                $dbAdapter->setProfiler ( $profiler );
            }

            $dbAdapters [$key] = $dbAdapter;
            if ($key == 'default') {
                Zend_Db_Table::setDefaultAdapter ( $dbAdapter );
            }
        }

        // Register all adapters into Registry
        Zend_Registry::set ( 'dbAdapters', $dbAdapters );
    }

    /**
     * Setup session
     */
    protected function _initSession() {
        $front = $this->bootstrap ( 'FrontController' )->getResource ( 'FrontController' );
        $configSession = $this->getOption ( 'session' );
        if ($configSession ['enabled'] == true) {
            // Session handler is database
            if ($configSession ['dbHandler'] == true) {
                $adapters = Zend_Registry::get ( 'dbAdapters' );
                $adapter = $adapters [$configSession ['adapter']];

                Zend_Session_SaveHandler_DbTable::setDefaultAdapter ( $adapter );
                $session = new Zend_Session_SaveHandler_DbTable ( $configSession ['options'] );
                $session->setLifetime ( $configSession ['remember_me_seconds'] );
                Zend_Session::setSaveHandler ( $session );

            } else {
                $options = array ('save_path' => $configSession ['save_path'], 'remember_me_seconds' => $configSession ['remember_me_seconds'] );

                Zend_Session::setOptions ( $options );
            }
        }

        Zend_Session::start ();
    }

    /**
     * Setup caches
     */
    protected function _initCache() {
        $this->_logger->info ( __METHOD__ );

        // Read caching configurations
        $config = $this->getOptions ();
        $configCache = $config ['cache'];
        $frontendConfig = $configCache ['frontend'];
        $htmlConfig = $configCache ['html'];
        $backendConfig = $configCache ['backend'];

        // Config for normal caching
        $frontendOptions = $frontendConfig;
        $backendOptions = $backendConfig;
        $backendOptions ['cache_dir'] .= '/normal/';

        $normalCache = Zend_Cache::factory ( 'Core', 'File', $frontendOptions, $backendOptions );
        Zend_Registry::set ( 'Zend_Cache_Normal', $normalCache );

        // Config for html caching
        $frontendOptions = $frontendConfig;
        $frontendOptions ['lifetime'] = $htmlConfig ['lifetime'];
        $backendOptions = $backendConfig;
        $backendOptions ['cache_dir'] .= '/html/';

        $htmlCache = Zend_Cache::factory ( 'Output', 'File', $frontendOptions, $backendOptions );
        Zend_Registry::set ( 'Zend_Cache_Html', $htmlCache );

        // Config for persistent caching
        // http://framework.zend.com/manual/en/zend.cache.frontends.html#zend.cache.frontends.core.options
        $frontendOptions = $frontendConfig;
        $frontendOptions ['lifetime'] = NULL;
        $backendOptions = $backendConfig;
        $backendOptions ['cache_dir'] .= '/persistent/';

        $persitentCache = Zend_Cache::factory ( 'Core', 'File', $frontendOptions, $backendOptions );
        Zend_Registry::set ( 'Zend_Cache_Persistent', $persitentCache );

        /*
         * DB Metadata Cache
        * http://framework.zend.com/manual/en/zend.db.table.html#zend.db.table.metadata.caching
        */
        if (TRUE == $configCache ['enabled_db_metadata_cache']) {
            $frontendOptions = $frontendConfig;
            $frontendOptions ['lifetime'] = NULL;
            $backendOptions = $backendConfig;
            $backendOptions ['cache_dir'] .= '/schema/';

            $schemaCache = Zend_Cache::factory ( 'Core', 'File', $frontendOptions, $backendOptions );
            // set the cache to be used with all table objects
            Zend_Db_Table_Abstract::setDefaultMetadataCache ( $schemaCache );
        }

        /*
         * PluginLoader class Cache
        * http://framework.zend.com/manual/en/zend.loader.pluginloader.html#zend.loader.pluginloader.performance.example
        */
        if (TRUE == $configCache ['enabled_pluginloader_cache']) {
            $classFileIncCache = $backendConfig ['cache_dir'] . '/pluginLoaderCache.php';
            if (file_exists ( $classFileIncCache )) {
                include_once $classFileIncCache;
            }
            Zend_Loader_PluginLoader::setIncludeFileCache ( $classFileIncCache );
        }
    }

    /**
     * Register mail transportation
     */
    protected function _initMail() {
        $configMail = $this->getOption ( 'mail' );

        if ($configMail ['smtp'] == true) {
            $transport = new Zend_Mail_Transport_Smtp ( $configMail ['server'], $configMail ['auth'] );
        } else {
            $transport = new Zend_Mail_Transport_Sendmail ();
        }

        Zend_Mail::setDefaultTransport ( $transport );
    }

    /**
     * Register front controller plugins
     */
    protected function _initFrontPlugins() {
        $this->bootstrap ( 'frontController' );
    }

    /**
     * Add required routes to the router
     */
    protected function _initRoutes() {
        $router = $this->frontController->getRouter();
        //san pham - danh muc
        $route1_1 = new Zend_Controller_Router_Route(
                '/danh-muc/:category-name', 
                array(
                    'module' =>'site',
                    'controller' => 'san-pham',
                    'action'=> 'index'
                )
        );
        $router->addRoute('/danh-muc/', $route1_1 );
        //san pham - chi tiet
        $route1_2 = new Zend_Controller_Router_Route(
                '/:name', 
                array(
                    'module' =>'site',
                    'controller' => 'san-pham',
                    'action'=> 'chi-tiet'
                )
        );
        $router->addRoute('/san-pham/', $route1_2 );
        // tin tuc -  chi tiet
        $route1_3 = new Zend_Controller_Router_Route(
                '/bai-viet/:bai-viet', 
                array(
                    'module' =>'site',
                    'controller' => 'pages',
                    'action'=> 'chi-tiet'
                )
        );
        $router->addRoute('/bai-viet/', $route1_3 );
        // tin tuc - danh muc
        $route1_3 = new Zend_Controller_Router_Route(
                '/danh-muc-bai-viet/:danh-muc', 
                array(
                    'module' =>'site',
                    'controller' => 'pages',
                    'action'=> 'index'
                )
        );
        $router->addRoute('/danh-muc-bai-viet/', $route1_3 );
        //
        $route1_4 = new Zend_Controller_Router_Route(
                '/danh-sach-bai-viet/', 
                array(
                    'module' =>'site',
                    'controller' => 'pages',
                    'action'=> 'index'
                )
        );
        $router->addRoute('/danh-sach-bai-viet/', $route1_4 );
        
        $route1_5 = new Zend_Controller_Router_Route(
                '/admin/', 
                array(
                    'module' =>'admin',
                    'controller' => 'index',
                    'action'=> 'index'
                )
        );
        $router->addRoute('/admin/', $route1_5 );
        //
         //san pham - danh muc
        $route1_6 = new Zend_Controller_Router_Route(
                '/index', 
                array(
                    'module' =>'site',
                    'controller' => 'index',
                    'action'=> 'index'
                )
        );
        $router->addRoute('/index/', $route1_6 );
        // trang khac
        // tin tuc - danh muc
        $route1_7 = new Zend_Controller_Router_Route(
                '/pages/:danh-muc', 
                array(
                    'module' =>'site',
                    'controller' => 'pages',
                    'action'=> 'index'
                )
        );
        $router->addRoute('/pages/', $route1_7 );
        
         // tin tuc - danh muc
        $route1_8 = new Zend_Controller_Router_Route(
                '/pages/load-view-more', 
                array(
                    'module' =>'site',
                    'controller' => 'pages',
                    'action'=> 'load-view-more'
                )
        );
        $router->addRoute('/pages/', $route1_8 );

        $route1_9 = new Zend_Controller_Router_Route(
            '/tim-kiem', 
            array(
                'module' =>'site',
                'controller' => 'tim-kiem',
                'action'=> 'search'
            )
        );
        $router->addRoute('/tim-kiem/', $route1_9);
        
    }

    protected function _initActionHelper() {
        $writer = new Zend_Log_Writer_Null ();
        $logger = new Zend_Log ( $writer );
        Zend_Registry::set ( 'logger', $logger );
    }

    protected function _defineCommon() {
        $configURL = $this->getOption ( 'url' );
        define ( 'DOMAIN', $configURL ["domain"] );
        define ( 'SSLDOMAIN', $configURL ["ssldomain"] );
        define ( 'TIMEOUT', $configURL ["timeout"] );
        define ( 'UPLOAD_PATH', BASE_PATH.$configURL ["uploadPath"] );
        /*
         * Get COnfig API
         */
        $configAPI = $this->getOption ( 'api' );
        define ( 'API_URL', $configAPI ["url"] );
        define ( 'API_AUTH_URL', $configAPI ["auth_url"] );
        define ( 'API_CRM_URL', $configAPI ["crm_url"] );
        include_once 'Config.php';

    }

    public function run() {
        $this->_defineCommon ();
        $this->frontController->dispatch ();
    }

    protected function _initFrontController() {
        $front = $this->hasPluginResource ( 'FrontController' ) ? $this->getPluginResource ( 'FrontController' )->init () : Zend_Controller_Front::getInstance ();
        $plugin = new My_Controller_Plugin_ModularAuth( 'admin' );
        $front->registerPlugin( $plugin );
        return $front;
    }
}