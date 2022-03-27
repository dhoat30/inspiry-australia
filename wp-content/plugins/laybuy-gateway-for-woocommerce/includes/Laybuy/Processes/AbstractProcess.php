<?php

abstract class Laybuy_Processes_AbstractProcess
{
    const PAYMENT_METHOD_LAYBUY = 'laybuy';

    protected $processManager;

    abstract public function execute();

    public function setProcessManager(Laybuy_ProcessManager $processManager)
    {
        $this->processManager = $processManager;
        return $this;
    }

    public function getProcessManager()
    {
        return $this->processManager;
    }

    /**
     * Function for encoding data for storage as WP Post Meta.
     *
     * @return	string
     */
    protected function _encode($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * Function for decoding data from storage as WP Post Meta.
     * @return	mixed
     */
    protected function _decode($string)
    {
        return unserialize(base64_decode($string));
    }

    protected function _makeUniqueReference($id) {
        return '#' . uniqid() . $id . time();
    }

    protected function _buildReturnUrl($quote_id, $nonce, $extra_args = []) {

        $site_url = get_site_url();
        $site_url_components = parse_url($site_url);
        $return_url = '';

        // Scheme

        if (isset($site_url_components['scheme'])) {
            $return_url .= $site_url_components['scheme'] . '://';
        }

        // Host

        if (isset($site_url_components['host'])) {
            $return_url .= $site_url_components['host'];
        }

        // Port

        if (isset($site_url_components['port'])) {
            $return_url .= ':' . $site_url_components['port'];
        }

        // Path

        if (isset($site_url_components['path'])) {
            $return_url .= rtrim($site_url_components['path'], '/') . '/';
        } else {
            $return_url .= '/';
        }

        // Query

        $existing_args = array();

        if (isset($site_url_components['query'])) {
            parse_str($site_url_components['query'], $existing_args);
        }

        $args = array(
            'gateway_id' => constant('WC_GATEWAY_LAYBUY_ID'),
            'quote_id' => $quote_id,
            'post_type' => 'laybuy_quote',
            '_wpnonce' => $nonce
        );

        $args = array_merge($existing_args, $args, $extra_args);

        $return_url .= '?' . http_build_query($args);

        // Fragment

        if (isset($site_url_components['fragment'])) {
            $return_url .= '#' . $site_url_components['fragment'];
        }

        return $return_url;
    }
}