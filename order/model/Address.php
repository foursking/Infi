<?php

namespace lestore_order\app\model;

use lestore_base\app\model\AttributesCopier;

class Address{
    use AttributesCopier;

    public $country;
    public $countryCode;

    public $province;
    public $provinceCode;

    public $city;
    public $cityCode;

    public function __construct($order){
        $this->copyAttributes($order, true);
    }
}
