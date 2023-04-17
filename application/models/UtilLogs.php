<?php
/**
 * Logs utilities
 *
 */
class UtilLogs {
    
    /**
     * Write log into log file with log level = INFO
     * @param string $file
     * @param string $content
     * @return multitype:boolean string NULL
     */
    public static function logInfo( $file = '', $content = '' ) {
        $retData = array();
        $retData['result'] = false;
        $retData['message'] = '';
        try {
            $dir = '../data/logs';
            // Set log file name
            $file_path = $dir.'/'.$file."_".date( 'Ymd' ).'.csv';
            $writer = new Zend_Log_Writer_Stream( $file_path );
            // set format for log record: data and time
            $logger = new Zend_Log( $writer );
            $logger->info( $content );
            $retData['result'] = true;
        } catch( Exception $ex ) {
            $retData['message'] = $ex->getMessage();
        }
        return $retData;
    }
    
    /**
     * Log send email request to file
     * @param string $string
     */
    public static function logsApiEmail( $string ) {
        self::logsApi( $string, dirname( $_SERVER['DOCUMENT_ROOT'] ).'/data/logs/logs_email_'.date( 'Ymd' ).'.csv' );
    }
    
    /**
     * Log a string to file with log level = INFO
     * @param string $string
     * @param string $file
     * @throws Exception
     */
    private static function logsApi( $string, $file ) {
        $stream = fopen( $file, 'a' );
        if( !$stream ) {
            throw new Exception( 'Failed to open stream' );
        }
        $writer = new Zend_Log_Writer_Stream( $stream );
        $logger = new Zend_Log( $writer );
        $logger->info( $string );
    }
    
    /**
     * Log API request to file
     * @param int $start
     * @param int $end
     * @param int $period
     * @param string $url
     * @param array $result
     * @throws Exception
     */
    public static function logApiForPerformance( $start, $end, $period, $url, $result ) {
        if( LOG_ENABLE && $period >= LOG_TIME_EXHAUST ) {
            $dir = dirname( filter_input( INPUT_SERVER, 'DOCUMENT_ROOT' ) );
    
            $logFormat = new Zend_Log_Formatter_Simple( "%message%".PHP_EOL );
            $string = date( 'Y-m-d H:i:s' ).','.RANDOM_KEY.','.$start.','.$end.','.$period.','.$url.',';
            if( LOG_RESULT_FILE ) {
                $id = uniqid();
                $fileName = $id."_".$start.".log";
                $resultStream = fopen( $dir.'/data/logs/'.$fileName, 'w' );
                if( !$resultStream ) {
                    throw new Exception( 'Failed to open stream' );
                }
    
                $resultWriter = new Zend_Log_Writer_Stream( $resultStream );
                $resultWriter->setFormatter( $logFormat );
                $resultLogger = new Zend_Log( $resultWriter );
                $resultLogger->log( $result, Zend_Log::INFO );
                $string .= $fileName;
            }
            $stream = fopen( $dir.'/data/logs/'.date( 'dmY' ).'_logs.csv', 'a' );
            if( !$stream ) {
                throw new Exception( 'Failed to open stream' );
            }
    
            $writer = new Zend_Log_Writer_Stream( $stream );
            $writer->setFormatter( $logFormat );
            $logger = new Zend_Log( $writer );
            $logger->info( $string );
        }
    }
    
    /**
     * Log history
     * @param string $action
     * @param string $requestUrl
     * @param array $response
     * @param array $params
     */
    public static function logHistory( $action, $object, $object_seq = 0, $requestUrl = '', $response = array(), $params = array() ) {
        $loginInfo = UtilAuth::getLoginInfo();
        $userId = 0;
        if ( empty( $loginInfo ) == false ) {
            $userId = $loginInfo["id"];
        }
        $requestParams = "";
        if ( isset( $params ) ) {
            if ( $action == LOG_ACTION_LOGIN || $action == LOG_ACTION_CHANGE_PASS ) {
                if ( is_array( $params ) ) {
                    unset($params["Password"]);
                    unset($params["OldPassword"]);
                    unset($params["RenewPassword"]);
                } else {
                    $params = json_decode( $params, true );
                    unset($params["Password"]);
                    unset($params["OldPassword"]);
                    unset($params["RenewPassword"]);
                }
            }
            if ( is_array( $params ) ) {
                $requestParams = json_encode( $params );
            } else {
                $requestParams = $params;
            }
        }
        $result = "";
        if ( isset( $response ) ) {
            if ( is_array( $response ) ) {
                $result = json_encode( $response );
            } else {
                $result = $response;
            }
        }
        $data = array();
        $data["user_id"] = $userId;
        $data["action"] = $action;
        $data["object"] = $object;
        $data["object_seq"] = $object_seq;
        $data["request_url"] = $requestUrl;
        $data["request_para"] = $requestParams;
        $data["response_api"] = $result;
        $data["date_created"] = new Zend_Db_Expr("NOW()");
        $historyLog = new HistoryLog();
        $result = $historyLog->add( $data );
        return $result;
    }
    /**
     * Log history
     * @param string $action
     * @param string $requestUrl
     * @param array $response
     * @param array $params
     */
    public static function updateLogHistory( $logId, $data ) {
    	$historyLog = new HistoryLog();
    	$result = $historyLog->updateLog( $logId, $data );
    	return $result;
    }
}
