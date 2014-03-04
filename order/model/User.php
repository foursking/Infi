<?php
namespace lestore_order\app\model;
use lestore_base\app\model\AttributesCopier;

class User{
    use AttributesCopier;

    public $userId;
    public $gender;
    public $consignee;
    public $email;

    public function __construct($order){
        $this->copyAttributes($order, true);
    }
}
