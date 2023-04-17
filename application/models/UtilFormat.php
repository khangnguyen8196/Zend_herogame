<?php
/**
 * Format utilities
 *
 */
class UtilFormat{

    /**
     * Format datetime
     * @param unknown_type $date
     */
    public static function formatTimeForCreatedDate( $date, $format = "Y/m/d" ) {
        if ( empty( $date ) == false ) {
            return date( $format, strtotime( $date ) );
        } else {
            return "";
        }
    }
    
    /**
     * Format number as money
     * @param string $data
     * @param string $format
     * @return string
     */
    public static function formatMoney( $data, $format = 2 ){
    	$formatData = intval($format);
    	if( isset($data) == true && is_numeric($data) == true ){
    		return number_format( $data , $formatData );
    	} else {
    		return "";
    	}
    }
    
    /**
     * Format day number as sequence of a month
     * @param string $day
     * @return string
     */
    public static function formatDayInMonth( $day ){
		$j = $day % 10;
		$k = $day % 100;
        if ($j == 1 && $k != 11) {
            return $day."st";
        }
        if ($j == 2 && $k != 12) {
            return $day."nd";
        }
        if ($j == 3 && $k != 13) {
            return $day."rd";
        }
        return $day."th";
    }
    public static function secondsToTime( $seconds ){
        $time = '';
        // extract hours
        $hours = floor($seconds / (60 * 60));
        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);
     
        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);
        //
        if( $hours < 10 ){
            $hours = '0'.$hours;
        }
        if( $minutes < 10 ){
            $minutes = '0'.$minutes;
        }
        if( $seconds < 10 ){
            $seconds = '0'.$seconds;
        }
        //
        if( $hours != '00' ){
            $time = $hours.'h '.$minutes.'m '.$seconds.'s';
        } else {
            if( $minutes != '00' ){
                $time = $minutes.'m ';
            } elseif( $seconds != '00' ) {
                $time = $time.$seconds.'s';
            }
        }
        return $time;
    }
}
