<?php
/**
 * Email utilities
 *
 */
class UtilEmail extends Zend_Mail {
    
    /**
     * Constructor
     * @return UtilEmail
     */
    public function __construct() {
        return parent::__construct( "utf-8" );
    }
    
    /**
     * Add sender
     * @see Zend_Mail::setFrom()
     */
    public function setFrom( $from, $name = '' ) {
        if ( empty( $from ) == true ) {
            $from = DEFAULT_EMAIL;
        }
        if ( $name == '' ) {
            $name = '';
        }
        parent::setFrom( $from, $name );
    }
    
    /**
     * Add receivers
     * @see Zend_Mail::addTo()
     */
    public function addTo( $address, $name = '' ) {
        if ( is_array( $address ) == true && count( $address ) > 0 ) {
            foreach ( $address as $value ) {
                parent::addTo( $value, $name );
            }
        } else {
            parent::addTo( $address, $name );
        }
    }
    
    /**
     * Set subject
     * @see Zend_Mail::setSubject()
     */
    public function setSubject( $subject ) {
        parent::setSubject( $subject );
    }
    
    /**
     * Set body
     * @see Zend_Mail::setBodyHtml()
     */
    public function setBodyHtml( $body, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE ) {
        parent::setBodyHtml( $body, $charset, $encoding );
    }
    
    /**
     * Add cc
     * @see Zend_Mail::addCc()
     */
    public function addCc( $address, $name = '' ) {
        if ( is_array( $address ) == true && count( $address ) > 0 ) {
            foreach ( $address as $value ) {
                parent::addCc( $value, $name );
            }
        } else {
            parent::addCc( $address, $name );
        }
    }
    
    /**
     * Add bcc
     * @see Zend_Mail::addBcc()
     */
    public function addBcc( $address ) {
        if ( is_array( $address ) == true && count( $address ) > 0 ) {
            foreach ( $address as $value ) {
                parent::addBcc( $value );
            }
        } else {
            parent::addBcc( $address );
        }
    }
    
    /**
     * Add attachment
     * @see Zend_Mail::addAttachment()
     */
    public function addAttachments( $attachment ) {
        if ( is_array( $attachment ) == true && count( $attachment ) > 0 ) {
            foreach ( $attachment as $value ) {
                parent::addAttachment( $value );
            }
        } else {
            parent::addAttachment( $attachment );
        }
    }
    
    /**
     * send an email
     * @param string $from
     * @param string $address
     * @param string $subject
     * @param string $body
     * @param array $options
     * @return NULL
     */
    public static function sendMail( $from, $address, $subject, $body, $options = array() ) {
        if( empty( $address ) == true ) {
            return NULL;
        }
        try {
            // create mail object
            $mail = new UtilEmail();
            $mail->setFrom( $from );
            $mail->addTo( $address );
            $mail->setSubject( $subject );
            $mail->setBodyHtml( $body );
            if ( empty( $options ) == false && empty( $options["cc"] ) == false ) {
                $mail->addCc( $options["cc"] );
            }
            if ( empty( $options ) == false && empty( $options["bcc"] ) == false ) {
                $mail->addBcc( $options["bcc"] );
            }
            if ( empty( $options ) == false && empty( $options["attachment"] ) == false ) {
                $mail->addAttachments( $options["attachment"] );
            }
            $sent = true;
            $mail->send();
            
        } catch ( Exception $exc ) {
            $string = ',' . $exc->getCode() . ',' . $exc->getMessage() . ',' . $exc->getTraceAsString();
            $sent = false;
        }
        return $sent;
    }
}
