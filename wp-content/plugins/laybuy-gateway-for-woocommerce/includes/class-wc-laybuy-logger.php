<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Log all things!
 *
 * @since 4.0.0
 * @version 4.0.0
 */
class WC_Laybuy_Logger {

    public static $logger;

    public static $enabled;

    const WC_LOG_FILENAME = 'laybuy';

    /**
     * Utilize WC logger class
     *
     * @since 4.0.0
     * @version 4.0.0
     */
    public static function log( $message, $start_time = null, $end_time = null ) {

        if ( ! class_exists( 'WC_Logger' ) ) {
            return;
        }

        if ( !self::$enabled ) {
            return;
        }

        if ( apply_filters( 'wc_laybuy_logging', true, $message ) ) {
            if ( empty( self::$logger ) ) {
                if ( WC_Laybuy_Helper::is_wc_lt( '3.0' ) ) {
                    self::$logger = new WC_Logger();
                } else {
                    self::$logger = wc_get_logger();
                }
            }

            if ( ! is_null( $start_time ) ) {

                $formatted_start_time = date_i18n( get_option( 'date_format' ) . ' g:ia', $start_time );
                $end_time             = is_null( $end_time ) ? current_time( 'timestamp' ) : $end_time;
                $formatted_end_time   = date_i18n( get_option( 'date_format' ) . ' g:ia', $end_time );
                $elapsed_time         = round( abs( $end_time - $start_time ) / 60, 2 );

                $log_entry  = "\n" . '====Laybuy Version: ' . WC_LAYBUY_VERSION . '====' . "\n";
                $log_entry .= '====Start Log ' . $formatted_start_time . '====' . "\n" . $message . "\n";
                $log_entry .= '====End Log ' . $formatted_end_time . ' (' . $elapsed_time . ')====' . "\n\n";

            } else {

                $log_entry  = "\n" . '====Laybuy Version: ' . WC_LAYBUY_VERSION . '====' . "\n";
                $log_entry .= '====Start Log====' . "\n" . $message . "\n" . '====End Log====' . "\n\n";
            }

            if ( WC_Laybuy_Helper::is_wc_lt( '3.0' ) ) {
                self::$logger->add( self::WC_LOG_FILENAME, $log_entry );
            } else {
                self::$logger->debug( $log_entry, array( 'source' => self::WC_LOG_FILENAME ) );
            }
        }
    }

    public static function info($message, $start_time = null, $end_time = null) {
        return self::log("Info: {$message}", $start_time, $end_time);
    }

    public static function error($message, $start_time = null, $end_time = null) {
        return self::log("Error: {$message}", $start_time, $end_time);
    }
}