<?php
/**
 * Encrypt utilities
 *
 */
class UtilEncryption {
    
    protected static $key = 'f842f57422fa1f3053df75976d97b55f';
    
    /**
     * Encrypt password
     * @param string $password
     * @return string
     */
    public static function encryptPassword( $password ) {
        return sha1( $password );
    }
    
    /**
     * Encrypt
     * @return string
     */
    public static function encrypt( $code ) {
        if( empty($code) == false ) {
            //To Encrypt:
            $encrypted = mcrypt_encrypt( MCRYPT_RIJNDAEL_256, self::$key, $code, MCRYPT_MODE_ECB );
            return base64_encode($encrypted);
        }
    
        return '';
    }
    
    /**
     * Decrypt
     * @return string
     */
    public static function decrypt( $encode ) {
        $decrypted = $encode;
        if( strlen($encode) > 4 ) {
            $encode = base64_decode($encode);
            //To Decrypt:
            $decrypted = mcrypt_decrypt( MCRYPT_RIJNDAEL_256, self::$key, $encode, MCRYPT_MODE_ECB );
        }
    
        return @trim($decrypted);
    }
    
    /**
     * Encrypt function for cache
     * @param string $string
     * @return string
     */
    public static function generateKeyCache( $string ) {
        return md5( $string );
    }
}
