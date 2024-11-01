<?php

require_once('AbstractActionEvent.php');

class PluginEvent extends AbstractActionEvent
{
    const ACTIVATE_HOOK = 'activated_plugin';
    const DEACTIVATE_HOOK = 'deactivated_plugin';

    /**
     * @param $plugin
     * @param $network
     */
    public function handle($plugin, $network)
    {
        $type = 'plugin';
        $action = current_filter() == 'deactivated_plugin' ? 'deactivated' : 'activated';

        $pluginDirectory = sprintf('%s/plugins/%s', WP_CONTENT_DIR, $plugin);
        $pluginData = get_plugin_data($pluginDirectory, false);
        $details = [];

        foreach ($pluginData as $key => $value) {
            $details[lcfirst($key)] = $value;
        }

        $payload = [
            'plugin' => $plugin,
            'details' => $details,
        ];

        $this->pushEvent($action, $type, $payload);
    }

    /**
     * @return mixed
     */
    public function priority()
    {
        return 10;
    }

    /**
     * @return mixed
     */
    public function arguments()
    {
        return 2;
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return function ($plugin, $network) {
            $this->handle($plugin, $network);
        };
    }
}