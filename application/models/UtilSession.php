<?php

/**
 * Session utilities
 */
class UtilSession{

    /**
     * get session
     * @param string $key
     * @param array $params
     */
    public static function get( $key ) {
        $session = new My_Controller_Action_Helper_Session ();
        return $session->getSession( $key );
    }

    /**
     * set session
     * @param string $key
     * @param mixed $data
     */
    public static function set( $key, $data ) {
        $session = new My_Controller_Action_Helper_Session ();
        $session->setSession( $key, $data );
    }

    /**
     * set list session
     * @param string $key
     * @param mixed $data
     */
    public static function setList( $key, $data ) {
        $list = self::get( $key );
        if( empty( $list ) == false ) {
            if( in_array( $data, $list ) == false ) {
                $list[] = $data;
            }
        } else {
            $list[] = $data;
        }
        self::set( $key, $list );
    }

}
