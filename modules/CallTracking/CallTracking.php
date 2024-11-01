<?php

require_once(dirname(__FILE__) . '/../AbstractModule.php');

class Module_CallTracking extends AbstractModule
{
    const JAVASCRIPT_URL = 'freespee/accounts/javascript';

    /**
     * Attach module
     */
    public function attach()
    {
        if ($this->getOption('call_tracking')) {
            $this->appendToHeader();
        }

        $this->ajaxCheck();
        $this->ajaxActivate();
        $this->ajaxDeactivate();
    }

    /**
     * Append to footer
     */
    public function appendToHeader()
    {
        $this->on('wp_head', function () {
            echo $this->getOption('call_tracking');
        }, 100);
    }

    /**
     * Activate
     */
    public function ajaxActivate()
    {
        $this->on('wp_ajax_spoton_call_tracking_activate', function () {

            $response = wp_remote_post(parent::API_URL . self::JAVASCRIPT_URL, [
                'method' => 'GET',
                'timeout' => 45,
                'headers' => [],
                'body' => [
                    'apiKey' => $this->getOption('key'),
                ],
            ]);

            if (!$response['response']['code'] == 200) {
                echo '0';
                wp_die();
            }

            $responseArray = json_decode($response['body'], true);

            $this->setOption('call_tracking', $responseArray['javascript']);

            echo '1';

            wp_die();
        });
    }

    /**
     * Deactivate
     */
    public function ajaxDeactivate()
    {
        $this->on('wp_ajax_spoton_call_tracking_deactivate', function () {
            $this->setOption('call_tracking', null);

            echo '1';
            wp_die();
        });
    }

    /**
     * Ajax validate key
     */
    public function ajaxCheck()
    {
        $this->on('wp_ajax_spoton_call_tracking_check', function () {
            echo (int) $this->isActivated();
            wp_die();
        });
    }

    /**
     * Check if activated
     *
     * @return bool
     */
    public function isActivated()
    {
        return (!empty($this->getOption('call_tracking')));
    }
}
