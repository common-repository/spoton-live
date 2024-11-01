<?php

require_once(dirname(__FILE__) . '/../AbstractActionEvent.php');

class AfterPurchaseEvent extends AbstractActionEvent
{
    const PURCHASE_HOOK = 'woocommerce_thankyou';

    /**
     * @param $orderId
     */
    public function handle($orderId)
    {
        $order = wc_get_order($orderId);

        $type = 'woocommerce';
        $action = 'purchase';

        $this->pushEvent($action, $type, $order->get_data());
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
        return 1;
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return function ($orderId) {
            $this->handle($orderId);
        };
    }
}