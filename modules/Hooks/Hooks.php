<?php

require_once(dirname(__FILE__) . '/../AbstractModule.php');
require_once('Events/UpgradeProcessEvent.php');
require_once('Events/PluginEvent.php');
require_once('Events/ThemeEvent.php');
require_once('Events/PostEvent.php');
require_once('Events/SettingsEvent.php');
require_once('Events/WooCommerce/AfterPurchaseEvent.php');

class Module_Hooks extends AbstractModule
{
    const WEBHOOK_URL = 'webhooks/wordpress-actions';

    /**
     * @var array
     */
    private $events = [
        /**
         * Wordpress events
         */
        UpgradeProcessEvent::class => [
            UpgradeProcessEvent::HOOK,
        ],

        PluginEvent::class => [
            PluginEvent::ACTIVATE_HOOK,
            PluginEvent::DEACTIVATE_HOOK,
        ],

        ThemeEvent::class => [
            ThemeEvent::SWITCH_HOOK,
        ],

        PostEvent::class => [
            PostEvent::SAVE_HOOK,
        ],

        SettingsEvent::class => [
            SettingsEvent::SETTINGS_ADD_HOOK,
            SettingsEvent::SETTINGS_UPDATE_HOOK,
        ],

        /**
         * Woocommerce events
         */
        AfterPurchaseEvent::class => [
            AfterPurchaseEvent::PURCHASE_HOOK,
        ]
    ];

    /**
     * Attach module
     */
    public function attach()
    {
        if (!$this->isActive('hooks')) {
            return;
        }

        $this->bindListeners();
    }

    /**
     * Bind listeners
     */
    public function bindListeners()
    {
        $url = parent::API_URL . self::WEBHOOK_URL;
        $apiKey = $this->getOption('key');

        if (!$apiKey) {
            return;
        }

        foreach ($this->events as $eventClass => $hooks) {
            /** @var AbstractActionEvent $event */
            $event = new $eventClass($apiKey, $url);

            foreach ($hooks as $hook) {
                $this->on(
                    $hook,
                    $event->closure(),
                    $event->priority(),
                    $event->arguments()
                );
            }
        }
    }
}
