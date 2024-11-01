<?php

require_once('AbstractActionEvent.php');

class UpgradeProcessEvent extends AbstractActionEvent
{
    const HOOK = 'upgrader_process_complete';

    /**
     * @param $upgrader
     * @param $options
     */
    public function handle($upgrader, $options)
    {
        $type = $options['type'];
        $action = $options['action'];

        $payload = [];
        foreach (end($options) as $value) {
            $payload[] = $this->specifyPayloadData($type, $value);
        }

        $this->pushEvent($action, $type, $payload);
    }

    /**
     * @param $type
     * @param $payload
     * @return mixed
     */
    private function specifyPayloadData($type, $payload)
    {
        switch ($type) {
            case 'plugin':
                $pluginDirectory = sprintf('%s/plugins/%s', WP_CONTENT_DIR, $payload);
                $pluginData = get_plugin_data($pluginDirectory, false);
                $details = [];

                foreach ($pluginData as $key => $value) {
                    $details[lcfirst($key)] = $value;
                }

                return [
                    'plugin' => $payload,
                    'details' => $details,
                ];

            case 'theme':
                $theme = wp_get_theme($payload);

                $namespaces = ['Name', 'ThemeURI', 'Description', 'Author', 'AuthorURI', 'Version', 'Template', 'Status'];
                $details = [];

                foreach ($namespaces as $namespace) {
                    $details[lcfirst($namespace)] = $theme->get($namespace);
                }

                return [
                    'theme' => $payload,
                    'details' => $details,
                ];

            default:
                return $payload;
        }
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
        return function ($upgrader, $options) {
            $this->handle($upgrader, $options);
        };
    }
}