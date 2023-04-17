<?php
/**
 * Controller Action Helper setup common function for getting xml file
 * Class Name:  My_Controller_Action_Helper_Xml
 */

class My_Controller_Action_Helper_Xml extends Zend_Controller_Action_Helper_Abstract
{
    protected $_xml_path;
    protected $_xml_file;

    public function __construct( $xml_path = "", $xml_file = "" )
    {
        if(mb_strlen($xml_path) > 0)
        {
            $this->_xml_path = $xml_path;
        }
        else
        {
            $this->_xml_path = APPLICATION_PATH . "/xml";
        }

        if(mb_strlen($xml_file) > 0)
        {
            $this->_xml_file = $xml_file;
        }
        else
        {
            $this->_xml_file = "config.xml";
        }
    }

    private function _getValue( $id, $xml_file = "" )
    {
        //-----------------------------------------------------------//
        //Check valid input
        //-----------------------------------------------------------//
        if( mb_strlen( $id ) <= 0 )
        {
            return;
        }

        //-----------------------------------------------------------//
        // Get xml folder location
        //-----------------------------------------------------------//
        if( mb_strlen( $xml_file ) <= 0 )
        {
            $xml_file = $this->_xml_file;
        }

        //-----------------------------------------------------------//
        // Full xml path
        //-----------------------------------------------------------//
        $xmlPath = $this->_xml_path . "/" . $xml_file;

        //-----------------------------------------------------------//
        // Check valid xml file
        //-----------------------------------------------------------//
        if (is_readable( $xmlPath ) == false)
        {
            throw new Exception("Can not open the file: '".
                    $xmlPath."'");
            return;
        }
        $ret = new Zend_Config_Xml( $xmlPath, $id);
        if( $ret  == false )
        {
            return;
        }
        else
        {
            $ret = $ret->toArray();
            if(count( $ret ) <= 1)
            {
                $ret = $ret[$id];
            }

            return $ret;
        }
    }

    private function _getValueList( $ID_List, $xml_file = "" )
    {
        if( is_array( $ID_List ) == false )
        {
            return null;
        }

        $returnArray = array();

        //Loop for all array items
        foreach( $ID_List as $key => $value )
        {
            $returnArray[$key] = self::_getValue( $value, $xml_file );
        }
        return $returnArray;
    }

    /**
     * Public function for accessing private methods
     * Function Name: getByKeys
     * Programmer: mynnt (GCS)
     * Create Date: Dec 2, 2008
     *
     * @keys      string or array  Key(s) to get data from xml file
     * $xml_file string Xml file name
     * @return  array or string output values
     * @Version V001 Dec 2, 2008 (mynnt) New Create
     */
    public function getValueByKeys( $keys, $xml_file = "" )
    {
        if( is_array( $keys ) == true )
        {
            return self::_getValueList( $keys, $xml_file );
        }
        else
        {
            return self::_getValue( $keys, $xml_file );
        }
    }

    private function _getMessage( $id )
    {
        if( mb_strlen( $id ) <= 0 )
        {
            return;
        }

        // Full xml path
        $xmlPath = $this->_xml_path . "/message.xml";

        // Check valid xml file
        if (is_readable( $xmlPath ) == false)
        {
            throw new Exception("Can not open the file: '".
                    $xmlPath."'");
            return;
        }

        //Prepare data to check whether there is any substring in the input $id
        $subId = explode( "|", $id );
        $msgCode    = $subId[0];

        if( mb_strlen( $msgCode ) <= 0 )
        {
            return;
        }
        unset( $subId[0] );

        //Get message value from xml file
        $msgConfig = new Zend_Config_Xml( $xmlPath, $msgCode);
        $message = "";

        if( $msgConfig  == false )
        {
            //----------------------------------------------------------------//
            //Message Id is not defined
            //----------------------------------------------------------------//
            $message = $msgCode;
        }
        else
        {
            //----------------------------------------------------------------//
            //Message Id is defined
            //----------------------------------------------------------------//
            $message = $msgConfig->$msgCode;
            if( empty( $subId ) == false )
            {
                $message = vsprintf( $message, $subId );
            }
        }
        return $message;
    }

    /**
     * Return the defined message list with the given list of $id
     * Function Name: __getMessageList
     * Programmer: mynnt (GCS)
     * Create Date: Dec 2, 2008
     *
     * @ID_List      type  ${array} array contains list of IDs
     * @throws  Zend_Cache_Exception
     * @return  type  array array messages
     * @Version V001 Dec 2, 2008 (mynnt) New Create
     */
    private function _getMessageList( $keys )
    {
        if( is_array( $keys ) == false )
        {
            return;
        }

        $returnArray = array();

        //Loop for all array items
        foreach( $keys as $key => $value )
        {
            $returnArray[$key] = self::_getMessage( $value );
        }
        return $returnArray;
    }

    public function getMessageByKeys( $keys )
    {
        if( is_array( $keys ) == true )
        {
            return self::_getMessageList( $keys );
        }
        else
        {
            return self::_getMessage( $keys );
        }
    }
}