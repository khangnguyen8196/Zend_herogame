<?php
class My_View_Helper_Summary extends Zend_View_Helper_Abstract
{
    public function summary($msg, $length = 30, $postfix = "...")
    {
        if(strlen($msg)> $length)
        {
            $msg = substr($msg,0,$length);
            $msg .= $postfix;
        }
        return $msg;
    }
}