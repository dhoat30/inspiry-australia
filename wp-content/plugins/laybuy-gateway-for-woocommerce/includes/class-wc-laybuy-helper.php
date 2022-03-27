<?php

class WC_Laybuy_Helper {

    const CURRENCY_CODE_NZ = 'NZD';
    const CURRENCY_CODE_AU = 'AUD';
    const CURRENCY_CODE_GB = 'GBP';
    const CURRENCY_CODE_US = 'USD';

    static public function get_currency_list() {
        return [
            self::CURRENCY_CODE_NZ => 'New Zealand Dollars',
            self::CURRENCY_CODE_AU => 'Australian Dollars',
            self::CURRENCY_CODE_GB => 'Great British Pounds',
            self::CURRENCY_CODE_US => 'US Dollars'
        ];
    }

    /**
     * Checks if WC version is less than passed in version.
     *
     * @since 4.1.11
     * @param string $version Version to check against.
     * @return bool
     */
    public static function is_wc_lt( $version ) {
        return version_compare( WC_VERSION, $version, '<' );
    }

    /**
     * Checks if WC version is greater than passed in version.
     *
     * @since 4.1.11
     * @param string $version Version to check against.
     * @return bool
     */
    public static function is_wc_gt( $version ) {
        return version_compare( WC_VERSION, $version, '>=' );
    }
}