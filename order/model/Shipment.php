<?php
namespace lestore_order\app\model;
use lestore_base\app\model\AttributesCopier;

class Shipment{
    use AttributesCopier;

    public $address;
    public $smId;
    public $shippingId;
    public $shippingStatus;
    public $shippingFee;
    public $shippingFeeExchange;

    public function __construct($order){
        $this->address = new Address($order);

        $this->copyAttributes($order, true);
    }

    public function charge($bill, $order, $exchanger){
        global $container;
        $shipping = $container['shipping']->getShipping($this->smId);
        return $shipping->charge($bill, $order, $exchanger);
    }
}
