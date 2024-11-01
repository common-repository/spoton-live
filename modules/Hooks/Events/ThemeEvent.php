<?php

require_once('AbstractActionEvent.php');

class ThemeEvent extends AbstractActionEvent
{
    const SWITCH_HOOK = 'after_switch_theme';

    /**
     * Handles the event
     */
    public function handle()
    {
        $type = 'theme';
        $action = 'activated';

        $theme = wp_get_theme();

        $namespaces = ['Name', 'ThemeURI', 'Description', 'Author', 'AuthorURI', 'Version', 'Template', 'Status'];

        $details = [];
        foreach ($namespaces as $namespace) {
            $details[lcfirst($namespace)] = $theme->get($namespace);
        }

        $payload = [
            'theme' => $details['name'],
            'details' => $details,
        ];

        $this->pushEvent($action, $type, $payload);
    }

    /**
     * @return mixed
     */
    public function priority()
    {
        return 20;
    }

    /**
     * @return mixed
     */
    public function arguments()
    {
        return 0;
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return function() {
            return $this->handle();
        };
    }
}