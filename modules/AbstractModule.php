<?php

abstract class AbstractModule
{
    const API_URL = 'https://api.spotonlive.dk/';

    /**
     * Attach module
     *
     * @return void
     */
    abstract public function attach();

    /**
     * Return whether the module is active or not
     *
     * @param $module
     * @return bool
     */
    public function isActive($module)
    {
        switch ($module) {
            case 'hooks':
            case 'forms':
                $default = true;
                break;

            default:
                $default = false;
                break;
        }

        return (bool) $this->getOption(sprintf('activate_%s', $module), $default);
    }

    /**
     * Bind event on post save
     *
     * @param Closure $closure
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function onSavePost(Closure $closure, $priority = 10, $acceptedArgs = 2)
    {
        $this->on('save_post', $closure, $priority, $acceptedArgs);
    }

    /**
     * Bind event on admin init
     *
     * @param Closure $closure
     */
    public function onAdmin(Closure $closure)
    {
        $this->on('admin_init', $closure);
    }

    /**
     * Bind event on init
     *
     * @param Closure $closure
     */
    public function onInit(Closure $closure)
    {
        $this->on('init', $closure);
    }

    /**
     * Bind closure to action
     *
     * @param string $action
     * @param Closure $closure
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function on($action, Closure $closure, $priority = 10, $acceptedArgs = 1)
    {
        add_action($action, $closure, $priority, $acceptedArgs);
    }

    /**
     * Get option
     *
     * @param string $key
     * @param bool $default
     * @return mixed
     */
    public function getOption($key, $default = false)
    {
        return get_option(sprintf('spoton_%s', $key), $default);
    }

    /**
     * Set option
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setOption($key, $value)
    {
        return update_option(sprintf('spoton_%s', $key), $value);
    }
}
