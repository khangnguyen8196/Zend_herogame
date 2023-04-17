<?php
/**
 * Translate utilities
 *
 */
class UtilTranslator {
    
    /**
     * Translate
     * @param string $key
     * @param array $params
     */
    public static function translate( $key, $params = array() ) {
        $translator = My_View_Helper_Translate::getTranslator();
        return $translator->translate( $key, $params );
    }
    
    /**
     * Load translation
     * @param string $file_name
     * @param string $langCode
     */
    public static function loadTranslator( $file_name, $langCode = '' ) {
        $translator = My_View_Helper_Translate::getTranslator();
        return $translator->loadTranslator( $file_name, $langCode );
    }
}
