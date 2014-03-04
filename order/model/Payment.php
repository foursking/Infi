<?php
namespace lestore_order\app\model;
use lestore_base\app\model\AttributesCopier;

class Payment{
    use AttributesCopier;

    public $paymentId;
    public $payStatus;

    public function __construct($order){
        $this->copyAttributes($order, true);
    }
}
