<?php

/**
 * Cache utilities
 *
 */
class UtilCache{

    /**
     * Get normal cache by key
     * @param string $key
     * @return mixed|NULL
     */
    public static function loadNormalCache( $key ) {
        return self::loadCache( $key, 'Zend_Cache_Normal' );
    }

    /**
     * Clear normal cache by key
     * @param string $key
     */
    public static function clearNormalCache( $key ) {
        self::clearCache( $key, 'Zend_Cache_Normal' );
    }

    /**
     * Save normal cache by key
     * @param string $key
     * @param array $data
     * @param int $lifetime
     */
    public static function saveNormalCache( $key, $data, $lifetime = 0, $tags = array() ) {
        self::saveCache( $key, $data, $lifetime, 'Zend_Cache_Normal', $tags );
    }

    /**
     * Get html cache by key
     * @param string $key
     * @return mixed|NULL
     */
    public static function loadHtmlCache( $key ) {
        return self::loadCache( $key, 'Zend_Cache_Html' );
    }

    /**
     * Clear html cache by key
     * @param string $key
     */
    public static function clearHtmlCache( $key ) {
        self::clearCache( $key, 'Zend_Cache_Html' );
    }

    /**
     * Save html cache by key
     * @param string $key
     * @param array $data
     * @param int $lifetime
     */
    public static function saveHtmlCache( $key, $data, $lifetime = 0, $tags = array() ) {
        self::saveCache( $key, $data, $lifetime, 'Zend_Cache_Html', $tags );
    }

    /**
     * Get persistent cache by key
     * @param string $key
     * @return mixed|NULL
     */
    public static function loadPersistentCache( $key ) {
        return self::loadCache( $key, 'Zend_Cache_Persistent' );
    }

    /**
     * Clear persistent cache by key
     * @param string $key
     */
    public static function clearPersistentCache( $key ) {
        self::clearCache( $key, 'Zend_Cache_Persistent' );
    }

    /**
     * CLean cache by key
     * @param string $key
     */
    public static function cleanPersistentCache( $mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array() ) {
        self::cleanCache('Zend_Cache_Persistent', $mode, $tags);
    }
    
    /**
     * Save persistent cache by key
     * @param string $key
     * @param array $data
     */
    public static function savePersistentCache( $key, $data, $tags = array() ) {
        self::saveCache( $key, $data, 0, 'Zend_Cache_Persistent', $tags );
    }

    /**
     * Get user cache by key
     * @param string $key
     * @return mixed|NULL
     */
    public static function loadUserCache( $key ) {
        return self::loadCache( $key, 'Zend_Cache_User' );
    }

    /**
     * Clear user cache by key
     * @param string $key
     */
    public static function clearUserCache( $key ) {
        self::clearCache( $key, 'Zend_Cache_User' );
    }

    /**
     * Save user cache by key
     * @param string $key
     * @param array $data
     */
    public static function saveUserCache( $key, $data, $lifetime = 0, $tags = array() ) {
        self::saveCache( $key, $data, $lifetime, 'Zend_Cache_User', $tags );
    }
    
    /**
     * CLean cache by key
     * @param string $key
     */
    public static function cleanCacheUser( $mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array() ) {
        self::cleanCache('Zend_Cache_User', $mode, $tags);
    }

    /**
     * Get cache by key
     * @param string $key
     * @return mixed|NULL
     */
    public static function loadCache( $key, $type ) {
        $cache = Zend_Registry::get( $type );
        $login_info = UtilAuth::getLoginInfo();
        if( empty( $cache ) == false && empty( $login_info ) == false && $login_info["disable_cache"] == CACHE_ENABLED  && false ) {
            $cacheData = $cache->load( $key );
            if( empty( $cacheData ) == false ) {
                return $cacheData;
            }
        }
        return null;
    }

    /**
     * Clear cache by key
     * @param string $key
     */
    public static function clearCache( $key, $type ) {
        $cache = Zend_Registry::get( $type );
        if( empty( $cache ) == false ) {
            $cache->remove( $key );
        }
    }

    /**
     * Save cache by key
     * @param string $key
     * @param array $data
     * @param int $lifetime
     */
    public static function saveCache( $key, $data, $lifetime = 0, $type, $tags = array() ) {
        $cache = Zend_Registry::get( $type );
        $login_info = UtilAuth::getLoginInfo();
        if( empty( $cache ) == false && empty( $login_info ) == false && $login_info["disable_cache"] == CACHE_ENABLED ) {
            if( intval( $lifetime > 0 ) ) {
                $cache->setLifetime( $lifetime );
            }
            $cache->save( $data, $key, $tags );
        }
    }

    /**
     * CLean cache by key
     * @param string $key
     */
    public static function cleanCache( $type, $mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array() ) {
        $cache = Zend_Registry::get( $type );
        if( empty( $cache ) == false ) {
            if($mode == Zend_Cache::CLEANING_MODE_ALL) {
                $cache->clean();
            } else {
                $cache->clean($mode, $tags);
            }
        }
    }
    
    /*
     * Set up Cache User
     */
    public static function setupCacheUser( ) {
        self::setupCache('Zend_Cache_User');
    }

    /*
     * Set up Cache
     */
    public static function setupCache( $type ) {
        $config = Zend_Registry::get( 'config' );
        $configCache = $config ['cache'];

        // Config for user caching
        $frontendOptions = $configCache ['frontend'];
        $backendOptions = $configCache ['backend'];
        $backendOptions ['cache_dir'] .= '/user/';

        $file = new My_Controller_Action_Helper_File();
        $file->createDir( $backendOptions ['cache_dir'] );

        $userCache = Zend_Cache::factory( 'Core', 'File', $frontendOptions, $backendOptions );
        Zend_Registry::set( $type, $userCache );
    }

    /**
     * set list session
     * @param string $key
     * @param mixed $data
     */
    public static function setListCache( $key, $data, $lifetime = 0, $type ) {
        $list = self::loadCache( $key, $type );
        if( empty( $list ) == false ) {
            if( in_array( $data, $list ) == false ) {
                $list[] = $data;
            }
        } else {
            $list[] = $data;
        }
        
        self::saveCache( $key, $list, $lifetime, $type );
    }

    /**
     * Clear list normal cache by key. Ex: Zend_Cache_Normal, ...
     * @param string $key
     */
    public static function clearListCache( $key, $type ) {
        try {
            $list = self::loadCache( $key, $type );
            if( empty( $list ) == false ) {
                $cache = Zend_Registry::get( $type );
                foreach( $list as $k => $v ) {
                    $cache->remove( $v );
                }
            }
        } catch( Exception $exc ) {
            
        }
    }

    /**
     * set list session
     * @param string $key
     * @param mixed $data
     */
    public static function setListCacheUser( $key, $data, $lifetime = 0 ) {
        self::setListCache( $key, $data, $lifetime, 'Zend_Cache_User' );
    }

    /**
     * Clear list normal cache by key. Ex: Zend_Cache_Normal, ...
     * @param string $key
     */
    public static function clearListCacheUser( $key ) {
        self::clearListCache( $key, 'Zend_Cache_User' );
    }

}
