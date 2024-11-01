<?php

require_once(__DIR__ . '../../AbstractModule.php');

class Module_Core extends AbstractModule
{
    const SETTINGS_GROUP = 'spotonmarketing';

    const VALIDATION_URL = 'tokens/validate';

    /** @var string */
    protected $version;

    /**
     * Attach module
     */
    public function attach()
    {
        $this->registerAdminPage();
        $this->registerSettings();
        $this->checkForMissingApiKey();
        $this->ajaxValidateKey();
        $this->setMetaVersion();
    }

    /**
     * Set version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Register admin page
     */
    public function registerAdminPage()
    {
        if (!is_admin()) {
            return;
        }
        
        $this->on('admin_menu', function () {
            add_menu_page(
                'SpotOn Live: ' . __('Settings'),
                'SpotOn Live',
                'administrator',
                __FILE__,
                [$this, 'settingsPage'],
                plugin_dir_url(__FILE__) . '/images/icon.png'
            );
        });
    }

    /**
     * Register settings
     */
    public function registerSettings()
    {
        $this->on('admin_init', function () {
            register_setting( self::SETTINGS_GROUP, 'spoton_call_tracking');
            register_setting( self::SETTINGS_GROUP, 'spoton_activate_hooks');
            register_setting( self::SETTINGS_GROUP, 'spoton_activate_forms');
            register_setting( self::SETTINGS_GROUP, 'spoton_key');
            register_setting( self::SETTINGS_GROUP, 'spoton_exclude_forms');
        });
    }

    /**
     * Settings page
     */
    public function settingsPage()
    {
        include(dirname(__FILE__ ) . '/views/settings.php');

        // JavaScript
        wp_register_script(
            'spoton-admin',
            plugins_url('scripts/admin.js', __FILE__),
            [],
            false,
            true
        );

        wp_enqueue_script('spoton-admin');
    }

    /**
     * Admin notice on missing api key
     */
    public function checkForMissingApiKey()
    {
        if ($this->getOption('key')) {
            return;
        }

        if (
            !$this->isActive('hooks') &&
            !$this->isActive('forms')
        ) {
            return;
        }

        $this->on('admin_notices', function () {
            echo sprintf(
                '<br /><div class="notice notice-error is-dismissible" style="padding: 10px 15px;"><p><strong>%s</strong> %s</p></div>',
                'SpotOn Marketing',
                __('Du har aktiveret et eller flere moduler, som kræver en API nøgle. Du kan få en API-nøgle ved at gå til spotonlive.dk')
            );
        });
    }

    /**
     * Ajax validate key
     */
    public function ajaxValidateKey()
    {
        $this->on('wp_ajax_spoton_validate_key', function () {
            /*
             * This value is sent directly to wp_remote_post, and is not
             * stored in the local database
             */
            $key = (isset($_POST['key'])) ? $_POST['key'] : null;

            echo ($this->validateKey($key)) ? '1' : '0';

            wp_die();
        });
    }

    /**
     * Set meta version
     */
    public function setMetaVersion()
    {
        $this->on('wp_head', function() {
            $metaName = 'slp_version'; // => SpotOnLive Plugin version
            $version = !empty($this->version) ? $this->version : 'unknown';

            echo sprintf(
                '<meta name="%s" content="%s"/>',
                $metaName,
                $version
            );
        });
    }

    /**
     * Validate key
     *
     * @param string $key
     * @return bool
     */
    public function validateKey($key)
    {
        if (empty($key)) {
            return false;
        }

        $response = wp_remote_post(parent::API_URL . self::VALIDATION_URL, [
            'method' => 'POST',
            'timeout' => 45,
            'headers' => [],
            'body' => [
                'key' => $key,
            ],
        ]);

        return ($response['response']['code'] == 200);
    }
}
