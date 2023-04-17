<?php
/**
 * Controller Action Helper setup common function for sending mail
 * Class Name:  My_Controller_Action_Helper_Mail
 */
class My_Controller_Action_Helper_Mail extends Zend_Controller_Action_Helper_Abstract
{
    //------------------------------------------------------------------------//
    // Keep send mail information
    //------------------------------------------------------------------------//
    protected $_data;

    //------------------------------------------------------------------------//
    // Zend_Mail object
    //------------------------------------------------------------------------//
    protected $_mail;

    public function __construct( array $data = array() )
    {
        $this->_mail = new Zend_Mail('utf-8');
        $this->_fromArray( $data );
    }

    /**
     * Convert send mail information from array
     * Function Name: _fromArray
     * Programmer: hoatt (PlanV)
     * Create Date: May 12, 2009
     *
     * @param  	array  $data Send mail information
     * @throws 	Zend_Cache_Exception
     * @return 	type  var description
     * @Version V001 May 12, 2009 (hoatt) New Create
     */
    protected function _fromArray( array $data )
    {
        if( empty($data) == false && is_array( $data ) == true )
        {
            //--------------------------------------------------------------------//
            // Check prammeter
            //--------------------------------------------------------------------//
            $this->to       = $data["to"];
            $this->from     = $data["from"];
            $this->cc       = $data["cc"];
            $this->bcc      = $data["bcc"];
            $this->subject  = $data["subject"];
            $this->tempMail = $data["tempMail"];
            //--------------------------------------------------------------------//
            // folder content template mail
            //--------------------------------------------------------------------//
            $this->mailPath   = $data["mailPath"];
            //--------------------------------------------------------------------//
            // flgText to specify whether to send in HTML format or Txt format
            // default is HTML
            //--------------------------------------------------------------------//
            $this->flgText    = $data["flgText"];
             
            //--------------------------------------------------------------------//
            // path of attached file
            //--------------------------------------------------------------------//
            $this->attachFile = $data["filePath"];
            //--------------------------------------------------------------------//
            // array content data which are out put in template mail
            //--------------------------------------------------------------------//
            $this->bodyData = $data["data"];
        }
    }

    protected function _toArray()
    {
        return $this->_data;
    }

    public function __set( $var, $val )
    {
        $this->_data[ $var ] = $val;
    }

    public function __get( $var )
    {
        return $this->_data[ $var ];
    }

    public function direct()
    {
        return $this->send();
    }

    /**
     * Send Mail
     * Function Name: send
     * Programmer: hoatt (PlanV)
     * Create Date: May 11, 2009
     * @param  void
     * @throws  Zend_Exception
     * @return
     * @Version V001 May 11, 2009 (hoatt) New Create
     */
    public function send( )
    {
        if( mb_strlen($this->to) <= 0 || mb_strlen($this->tempMail) <= 0 )
        {
            return false;
        }
         
        //--------------------------------------------------------------------//
        // Prepare body mail by render from template
        //--------------------------------------------------------------------//
        Zend_Loader::loadClass('Zend_View');
        $view = new Zend_View();
        $view->setScriptPath( $this->mailPath );
        $view->data = $this->data;

        //--------------------------------------------------------------------//
        // using view render
        //--------------------------------------------------------------------//
        $body  = $view->render( $this->tempMail );
        $this->_mail->addTo( $this->to );
        $this->_mail->setFrom( $this->from );
        $this->_mail->setSubject( $this->subject);

        //--------------------------------------------------------------------//
        // Check type of template default format template is HTML
        //--------------------------------------------------------------------//
        if( $this->flgText == true ){
            $this->_mail->setBodyText( $body );
        }
        else{
            $this->_mail->setBodyHtml( $body );
        }

        //--------------------------------------------------------------------//
        // Add cc mail
        //--------------------------------------------------------------------//
        if( mb_strlen($this->cc) > 0 )
        {
            $this->_mail->addCc($this->cc);
        }
        //--------------------------------------------------------------------//
        // Add bcc list
        //--------------------------------------------------------------------//
        if( mb_strlen($this->bcc) > 0)
        {
            $this->_mail->addBcc($this->bcc);
        }
        //--------------------------------------------------------------------//
        // Attached file
        //--------------------------------------------------------------------//
        if( mb_strlen( $this->attachFile) > 0 )
        {
            if( file_exists( $this->attachFile ) == true )
            {
                $fileBody = file_get_contents($this->attachFile);
                $fileName = basename($this->attachFile);
                $fileType = mime_content_type($attachFile);
                $at = $this->_mail->createAttachment($fileBody);
                $at->filename    = $fileName;
                $at->type        = $fileType;
                $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                $at->encoding    = Zend_Mime::ENCODING_BASE64;
            }
        }
        //---------------------------------------------------------------------//
        // Send mail
        //---------------------------------------------------------------------//
        $this->_mail->send();
    }
}
