<?php

require_once('AbstractActionEvent.php');

class PostEvent extends AbstractActionEvent
{
    const SAVE_HOOK = 'save_post';

    /**
     * @param $postId
     */
    public function handle($postId)
    {
        if (wp_is_post_revision($postId)) {
            return;
        }

        $action = 'updated';
        $type = 'post';
        $post = get_post($postId);

        if ($post->post_status != 'publish') {
            return;
        }

        $this->pushEvent($action, $type, $post);
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
        return 1;
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return function ($postId) {
            $this->handle($postId);
        };
    }
}