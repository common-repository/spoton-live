<?php

abstract class AbstractActionEvent
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $url;

    /**
     * AbstractActionEvent constructor.
     * @param string $apiKey
     */
    public function __construct($apiKey, $url)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    abstract public function priority();

    /**
     * @return mixed
     */
    abstract public function arguments();

    /**
     * @return mixed
     */
    abstract public function closure();

    /**
     * @param $action
     * @param $type
     * @param $payload
     */
    public function pushEvent($action, $type, $payload)
    {
        $body = [
            'apiKey' => $this->apiKey,
            'action' => $action,
            'type' => $type,
            'payload' => $payload,
        ];

        wp_remote_post($this->url, [
            'method' => 'POST',
            'timeout' => 45,
            'headers' => [],
            'body' => $body,
        ]);
    }
}