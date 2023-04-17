<?php
/*
 * Temporary to add for using language->_() method in layout
*/
class My_Zend_Translate extends Zend_Translate{
    public function _( $key, $params = array() ) {
        $translator = My_View_Helper_Translate::getTranslator();
        return $translator->translate( $key, $params );
    }

    public function translate( $key, $params = array() ) {
        return $this->_( $key, $params );
    }
}

/**
 * 
 */
class My_View_Helper_Translate extends Zend_View_Helper_Abstract {

    private static $_translationData = array();
    private static $_translationMessages = array();
    private static $_translationMasters = array();
    private static $_translator = null;

    /*
     * Use as singlon
    */
    public static function getTranslator() {

        if( self::$_translator === null ) {
            self::$_translator = new My_View_Helper_Translate();
        }

        return self::$_translator;
    }
    /**
      * Description: get translation and format return string
     * @param  string $key
     * @param  array $params
      * @return string $translation
       */
    public function translate( $key, $params = array() ) {
        // Check and return the message if it is loaded

        $key = strtolower( $key );
        if( isset( My_View_Helper_Translate::$_translationMessages[$key] ) ) {
            return $this->getMessage( $key, $params );
        }

        // Start loading translation common file
        $translate = array();
        // Check language registry is exist
        if (Zend_Registry::offsetExists( 'language' ) ) {
            $translate = Zend_Registry::get( 'language' );
        }

        // Get translation from translation file or cache
        if ( empty( $translate ) ) {

            $translate = self::loadTranslator( 'language' );
            Zend_Registry::set( 'language', $translate );
        }

        // Get translation content by key and format return key
        $translate = $translate->getMessages();
        $translate = array_change_key_case( $translate, CASE_LOWER );

        // Store local message for next use
        $this->appendTranslation( $translate );

        return $this->getMessage( $key, $params );
    }

    /*
     * get message from language array
    */
    protected function getMessage( $key, $params ){

        $translate = My_View_Helper_Translate::$_translationMessages;

        $key = strtolower( $key );
        if ( !empty( $translate ) && !empty( $translate[$key] )) {
            $translation = $translate[$key];

            if( !empty( $params)){
                if( is_array( $params ) ){
                    $translation = vsprintf( $translation, $params );
                }else{
                    $translation = sprintf( $translation, $params );
                }
            }
            return $translation;

        } else {

            //TODO: Log missing key
            $this->logMissingKey( $key );

            return $key;
        }
    }
    /*
     * Merge input translation (key, value ) into main list
    * @param $array translation message (key, value )
    */
    protected function appendTranslation( $array ){
        self::$_translationMessages = array_merge(
                self::$_translationMessages,
                $array
        );
    }
    /*
     * Load translation lanage
    * @param $fileName: language file name without extention (.tmx)
    */
    public function loadTranslator( $fileName, $langCode = '', $isPrivate = false ){

        if( empty( $langCode ) ){
            $langCode = LANG_CODE;
        }
        $cache_file = $fileName.'_' . $langCode;

        if( isset( self::$_translationMasters[$cache_file]) ) {
            return self::$_translationMasters[$cache_file];
        }


        $cache = Zend_Registry::get( 'Zend_Cache_Persistent' );
        $language_file = LANG_PATH . '/'.$fileName.'.tmx';
        $translator = $cache->load( $cache_file );
        if ( empty( $translator ) ) {
            $translator = new My_Zend_Translate( 'tmx', $language_file , $langCode );
            $cache->save( $translator, $cache_file );
        } else {
            // Check and load the translation if the original file is changed
            $filetime = filemtime( LANG_PATH . '/'.$fileName.'.tmx' );
            $cache_time = $cache->getMetadatas( $cache_file );
            if( $cache_time['mtime'] < $filetime ) {
                $translator = new My_Zend_Translate( 'tmx', $language_file , $langCode );
                $cache->save( $translator, $cache_file );
            }
        }
        $translator = new My_Zend_Translate( 'tmx', $language_file , $langCode );

        // Get translation content by key and format return key
        $translate = $translator->getMessages();
        $translate = array_change_key_case( $translate, CASE_LOWER );

        if( $isPrivate == false ){
            // Store the message into local for next use
            $this->appendTranslation( $translate );
        }

        self::$_translationMasters[$cache_file] = $translator;

        return $translator;
    }

    private function logMissingKey( $key ){
        $filename = BASE_PATH . "/data/logs/missing_language.log";
        if ( empty( self::$_translationData ) === TRUE ) {
            if( file_exists( $filename ) == false ) {
                $f = fopen( $filename, 'aw' );
                fclose( $f );
            }

            @$contents = file_get_contents( $filename );
            self::$_translationData = @unserialize( $contents );
        }
        self::$_translationData[$key] = $key;
        file_put_contents( $filename, serialize( self::$_translationData ) );
    }
}
