<?php

class Laybuy_SupportRequest
{
    const API_ENDPOINT = 'https://laybuy-support.staging.overdose.digital/api/support-request';
    const API_KEY = 'ThnaJEEWp7zqJkEhYrGbdBJQLjLkUxgu';

    public function send()
    {
        $body = [
            'platform' => 'woocommerce',
            'meta' => [
                'website' => get_site_url(),
                'date' => date('Y-m-d H:i:s', time())
            ],
            'env' => [
                'wp_version' => get_bloginfo( 'version' ),
                'wc_version' => WC_VERSION,
                'laybuy_version' => WC_LAYBUY_VERSION
            ],
            'settings' => get_option( 'woocommerce_laybuy_settings', true),
            'log' => null
        ];

        $logfile = WC_LOG_DIR . WC_Log_Handler_File::get_log_file_name('laybuy');

        if (file_exists($logfile)) {
            $body['log'] = base64_encode(file_get_contents($logfile));
        } else {

            $logFiles = WC_Log_Handler_File::get_log_files();

            // try to find relevant logs for the last 14 days
            $relevantLogFiles = [];
            for ($i = 1; $i <= 14; $i++) {
                $relevantLogFiles[] = 'laybuy-' . date('Y-m-d', strtotime("-{$i} days"));
            }

            $logFiles = array_filter($logFiles, function ($logFile) use ($relevantLogFiles) {
                $result = (bool) strstr($logFile, 'laybuy');

                if (!$result) {
                    return false;
                }

                foreach ($relevantLogFiles as $relevantLogFile) {
                    if (false !== strpos($logFile, $relevantLogFile)) {
                        return true;
                    }
                }
            });

            if (count($logFiles) > 0) {

                $logFiles = array_reverse($logFiles);
                $logFiles = array_values(array_slice($logFiles, 0, 2));

                $body['log'] = base64_encode(
                    file_get_contents(WC_LOG_DIR . DIRECTORY_SEPARATOR . $logFiles[0]) . "\n\n\n" .
                    file_get_contents(WC_LOG_DIR . DIRECTORY_SEPARATOR . $logFiles[1])
                );
            }
        }

        $laybuySupportResponse = wp_remote_post(self::API_ENDPOINT, [
            'headers' => [
                'x-access-token' => self::API_KEY
            ],
            'body' => $body
        ]);

        if ( is_wp_error( $laybuySupportResponse ) ) {
            wp_send_json_error();
        }

        $laybuySupportResponse = json_decode($laybuySupportResponse['body'], true);

        if (!isset($laybuySupportResponse['success']) || !$laybuySupportResponse['success']) {
            wp_send_json_error();
        }

        $to = 'overdose@laybuy.com';
        $subject = 'Woo Support Request from ' . get_site_url();
        $body = $laybuySupportResponse['link'];
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $result = wp_mail( $to, $subject, $body, $headers );

        if (!$result) {
            wp_send_json_error();
        }

        wp_send_json_success();
    }
}