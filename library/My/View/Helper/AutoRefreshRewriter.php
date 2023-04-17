<?php
class My_View_Helper_AutoRefreshRewriter extends Zend_View_Helper_Abstract {
    
    public function autoRefreshRewriter($filePath) {
        if (strpos ( $filePath, '/' ) !== 0) {
            // path has no leading '/'
            return $filePath;
        } elseif (file_exists ( $_SERVER ['DOCUMENT_ROOT'] . $filePath )) {
            // file exists under normal path
            // so build path based on this
            $mtime = filemtime ( $_SERVER ['DOCUMENT_ROOT'] . $filePath );
            return $filePath.'?'.$mtime;
        } else {
            // fetch directory of index.php file (file from all others are
            // included)
            // and get only the directory
            $indexFilePath = dirname ( current ( get_included_files () ) );
            	
            // check if file exist relativ to index file
            if (file_exists ( $indexFilePath . $filePath )) {

                // get timestamp based on this relativ path
                $mtime = filemtime ( $indexFilePath . $filePath );

                // write generated timestamp to path
                // but use old path not the relativ one
                return $filePath.'?'.$mtime;
            } else {

                return $filePath;
            }
        }
    }

}
