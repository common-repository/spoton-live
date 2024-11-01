<?php

require_once(dirname(__FILE__) . '/../AbstractModule.php');

class Module_UTMTracking extends AbstractModule
{
    /**
     * Attach module
     */
    public function attach()
    {
        $this->registerAssets();
    }

    /**
     * Register UTM tracking
     */
    public function registerAssets()
    {
        // JavaScript
        wp_register_script(
            'spoton-utm',
            plugins_url('scripts/utm.js', __FILE__),
            [],
            false,
            true
        );

        wp_enqueue_script('spoton-utm');
    }
}
