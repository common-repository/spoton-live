<?php

require_once('AbstractActionEvent.php');

class SettingsEvent extends AbstractActionEvent
{
    const SETTINGS_ADD_HOOK = 'added_option';
    const SETTINGS_UPDATE_HOOK = 'updated_option';

    private $defaultTaglines = [
        'endnu en wordpress-blog',
        'just another wordpress site'
    ];

    /**
     * Handles the changes
     */
    public function handle($option, $value)
    {
        $type = 'settings';

        switch ($option) {
            case 'permalink_structure':
                $action = 'permalink_structure';
                $payload = [$action => $value];
                break;

            case 'blogdescription':
                $action = 'tagline';
                $tagline = get_bloginfo('description');
                $default = in_array(strtolower($tagline), $this->defaultTaglines);
                $payload = [$action => $tagline, 'isDefault' => $default];
                break;

            case 'blog_public':
                $action = 'search_engine_visibility';
                $payload = ['is_enabled' => $value];
                break;

            default:
                return;
        }


        $this->pushEvent($action, $type, $payload);
    }

    /**
     * @return mixed
     */
    public function priority()
    {
        return 60;
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
        return function ($option, $value) {
            $this->handle($option, $value);
        };
    }
}