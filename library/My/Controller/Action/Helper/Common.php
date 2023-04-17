<?php
/**
 * Controller Action Helper setup common functions for file and directory
 */

class My_Controller_Action_Helper_Common extends Zend_Controller_Action_Helper_Abstract
{
    public static function myTrim( $data )
    {
        //-------------------------------------------------------------------//
        // Check input
        //-------------------------------------------------------------------//
        if( empty( $data ) == true )
        {
            return;
        }

        //-------------------------------------------------------------------//
        // Data is a string
        //-------------------------------------------------------------------//
        if( is_array( $data ) == false )
        {
            return trim( $data );
        }
        //-------------------------------------------------------------------//
        // Data is a array
        //-------------------------------------------------------------------//
        else
        {
            $result = array();
            foreach( $data as $key => $value )
            {
                if( is_array( $value ) == false )
                {
                    $result[ $key ] = trim( $value );
                }
                else
                {
                    $temp = array();
                    $temp = self::myTrim( $value );

                    $result[ $key ] = $temp;
                }
            }

            return $result;
        }
    }
    /**
     * decode url
     * Jan 01, 2013
     * @param $data
     */
    public static function urldecode ($data) {
        //-------------------------------------------------------------------//
        // Check input
        //-------------------------------------------------------------------//
        if( empty( $data ) == true )
        {
            return;
        }

        //-------------------------------------------------------------------//
        // Data is a string
        //-------------------------------------------------------------------//
        if( is_array( $data ) == false )
        {
            return urldecode( $data );
        }
        //-------------------------------------------------------------------//
        // Data is a array
        //-------------------------------------------------------------------//
        else
        {
            $result = array();
            foreach( $data as $key => $value )
            {
                if( is_array( $value ) == false )
                {
                    $result[ $key ] = urldecode( $value );
                }
                else
                {
                    $temp = array();
                    foreach( $value as $key_sub => $value_sub )
                    {
                        $temp[ $key_sub ] = urldecode( $value_sub );
                    }

                    $result[ $key ] = $temp;
                }
            }

            return $result;
        }
    }

    /**
     * Set PHP sesion id to cookies
     * Function Name: setCookie
     * Programmer: hoangpm (GCS)
     * Create Date: Dec 31, 2008
     *
     * @param      void
     * @throws     Zend_Cache_Exception
     * @return     void
     * @Version V001 Dec 31, 2008 (hoangpm) New Create
     */
    public static function setCookie( $data, $name, $expire )
    {
        //-------------------------------------------------------------------//
        // Set cookie expire after closed browser
        //-------------------------------------------------------------------//
        setcookie( $name, $data, $expire, "/" );
    }

    /**
     * Set cookie to null
     * Function Name: detroyCookie
     * Programmer: hoangpm (GCS)
     * Create Date: Dec 31, 2008
     *
     * @param   void
     * @throws  Zend_Cache_Exception
     * @return  void
     * @Version V001 Dec 31, 2008 (hoangpm) New Create
     */
    public static function detroyCookie( $name )
    {
        $expire = time() - 1000;
        setcookie( $name, "", $expire, "/" );
    }

    /**
     * Check cookie is existed or expired
     * Function Name: checkCookie
     * Programmer: hoangpm (GCS)
     * Create Date: Dec 31, 2008
     *
     * @param      void
     * @throws     Zend_Cache_Exception
     * @return     TRUE if cookie is existed
     * @Version V001 Dec 31, 2008 (hoangpm) New Create
     */
    public static function checkCookie( $name )
    {
        //-------------------------------------------------------------------//
        // Make sure cookie has value
        //-------------------------------------------------------------------//
        if( empty( $name ) == true || mb_strlen( $name ) == 0 )
        {
            return false;
        }

        $value = $_COOKIE[ $name ];
        $value = trim( $value );
        if( mb_strlen( $value ) > 0 )
        {
            return true;
        }

        return false;
    }

    public static function getCookie( $name )
    {
        //-------------------------------------------------------------------//
        // Make sure cookie has value
        //-------------------------------------------------------------------//
        if( empty( $name ) == true || mb_strlen( $name ) == 0 )
        {
            return "";
        }

        return $_COOKIE[ $name ];
    }

    public static function unicode_convert($str)
    {
        if(!$str) return false;
        $unicode = array(
                'a'=>array('á','à','ả','ã','ạ','ă','ắ','ặ','ằ','ẳ','ẵ','â','ấ','ầ','ẩ','ẫ','ậ'),
                'A'=>array('Á','À','Ả','Ã','Ạ','Ă','Ắ','Ặ','Ằ','Ẳ','Ẵ','Â','Ấ','Ầ','Ẩ','Ẫ','Ậ'),
                'd'=>array('đ'),
                'D'=>array('Đ'),
                'e'=>array('é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ'),
                'E'=>array('É','È','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ'),
                'i'=>array('í','ì','ỉ','ĩ','ị'),
                'I'=>array('Í','Ì','Ỉ','Ĩ','Ị'),
                'o'=>array('ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ'),
                '0'=>array('Ó','Ò','Ỏ','Õ','Ọ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ','Ơ','Ớ','Ờ','Ở','Ỡ','Ợ'),
                'u'=>array('ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự'),
                'U'=>array('Ú','Ù','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự'),
                'y'=>array('ý','ỳ','ỷ','ỹ','ỵ'),
                'Y'=>array('Ý','Ỳ','Ỷ','Ỹ','Ỵ'),
                '-'=>array(' ','&quot;','.','/')
        );
        foreach($unicode as $nonUnicode=>$uni){
            foreach($uni as $value)
                $str = str_replace($value,$nonUnicode,$str);
        }
        return $str;
    }

    /**
     * Convert special characters to encoding
     * Function Name: convertHtmlEntities
     * Programmer: hoangpm (GCS)
     * Create Date: Jan 6, 2009
     *
     * @param      Array  $Data Post array you need to htmlspecialchars
     * @param      string  $Key[i] Some field you don't need to convert
     * @throws     Zend_Cache_Exception
     * @return     An array that converted htmlspecialchars
     * @Version V001 Jan 6, 2009 (hoangpm) New Create
     */
    function convertHtmlEntities( $Data, $Key1 = NULL,
            $Key2 = NULL, $Key3 = NULL,
            $Key4 = NULL, $Key5 = NULL )
    {
        //--------------------------------------------------------------------//
        // Check input data
        //--------------------------------------------------------------------//
        if( empty( $Data ) == TRUE )
        {
            return $Data;
        }

        //--------------------------------------------------------------------//
        // Make array keys
        //--------------------------------------------------------------------//
        $Key_Array = array();
        $Key1 = trim( $Key1 );
        if( mb_strlen( $Key1 ) > 0 )
        {
            $Key_Array[ $Key1 ] = $Key1;
        }

        $Key2 = trim( $Key2 );
        if( mb_strlen( $Key2 ) > 0 )
        {
            $Key_Array[ $Key2 ] = $Key2;
        }

        $Key3 = trim( $Key3 );
        if( mb_strlen( $Key3 ) > 0 )
        {
            $Key_Array[ $Key3 ] = $Key3;
        }

        $Key4 = trim( $Key4 );
        if( mb_strlen( $Key4 ) > 0 )
        {
            $Key_Array[ $Key4 ] = $Key4;
        }

        $Key5 = trim( $Key5 );
        if( mb_strlen( $Key5 ) > 0 )
        {
            $Key_Array[ $Key5 ] = $Key5;
        }

        //--------------------------------------------------------------------//
        // In case data is not array
        //--------------------------------------------------------------------//
        $Result_Array = array();
        if( is_array( $Data ) == FALSE )
        {
            return htmlentities( $Data, ENT_QUOTES, "UTF-8" );
        }
        else
        {
            foreach( $Data as $Key => $Value )
            {
                //------------------------------------------------------------//
                // One dimemsion array
                //------------------------------------------------------------//
                if( is_array( $Value ) == FALSE )
                {
                    if( array_key_exists( $Key, $Key_Array ) == true )
                    {
                        $Result_Array[$Key] = $Value;
                    }
                    else
                    {
                        $Result_Array[$Key] = htmlentities( $Value, ENT_QUOTES, "UTF-8");
                    }
                }
                //------------------------------------------------------------//
                // Two dimemsion array
                //------------------------------------------------------------//
                else
                {
                    foreach( $Value as $Key_2 => $Value_2 )
                    {
                        if( array_key_exists( $Key_2, $Key_Array ) == true )
                        {
                            $Result_Array[$Key][$Key_2] = $Value_2;
                        }
                        else
                        {
                            $Result_Array[$Key][$Key_2] = htmlentities( $Value_2,ENT_QUOTES, "UTF-8" );
                        }
                    }
                }
            }
        }
        return $Result_Array;
    }

    /**
     * [description for the function here]
     * Function Name: quoteLike
     * Programmer: boihn (GCS)
     * Create Date: 8 Jan 2009
     *
     * @param      string  var
     * @throws     Zend_Cache_Exception
     * @return     type  var [description here]
     * @Version V001 8 Jan 2009 (boihn) New Create
     */
    public function quoteLike( $SQL )
    {
        $Reg_Array = array( "\\\\", "'",   "\"",   "%",   "_" );
        $Cnv_Array = array( "\\\\", "\\'", "\\\"", "\\%", "\\_" );

        return self::quoteEscapeString( $SQL, $Reg_Array, $Cnv_Array );
    }

    /**
     * [description for the function here]
     * Function Name: quoteEscapeString
     * Programmer: boihn (GCS)
     * Create Date: 8 Jan 2009
     *
     * @param   string  var
     * @throws  Zend_Cache_Exception
     * @return  type  var [description here]
     * @Version V001 8 Jan 2009 (boihn) New Create
     */
    private function quoteEscapeString( $SQL, $Reg_Array, $Cnv_Array )
    {
        $Pattern = "/[\\x00-\\x08]|[\\x0B-\\x0C]|[\\x0E-\\x1F]/";
        $SQL     = preg_replace( $Pattern, "", $SQL );

        $XX = 0;

        $Count = count( $Reg_Array );

        while( $XX < $Count )
        {
            $SQL = mb_ereg_replace( $Reg_Array[$XX], $Cnv_Array[$XX], $SQL );
            ++$XX;
        }

        return $SQL;
    }
}