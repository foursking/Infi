<?php

namespace lestore_order\app\model;

class Order{
    public $orderId;
    public $orderSn;

    public $couponCode;

    public $goods;
    public $gifts;
    public $shipment;
    public $payment;
    public $importantDay;
    public $currency; 
    public $user;

    private $coupon;

    public function __construct(){
        $this->user = new User($this); 
        $this->currency = new Currency($this); 
        //$this->coupon = new Coupon($this); 
        $this->shipment = new Shipment($this); 
        $this->payment = new Payment($this);
    }

    public function charge($bill, $exchanger){
        foreach($this->goods as $goods){
            $goods->charge($bill, $exchanger);
        }

        $this->shipment->charge($bill, $this, $exchanger);

        $coupon = $this->getCoupon(); 
        if(null != $coupon){
            $this->coupon->charge($bill, $this, $exchanger);
        }

        $bill->orderAmount = $bill->goodsAmount + $bill->shippingFee + $bill->bonus;
    }

    public function getCoupon(){
        if($this->coupon == null){
            global $container;
            $coupon = $container['coupon']->getCoupon($this->couponCode);
            if($coupon != false && $coupon != null){
                $this->coupon = $coupon;
            }
        }
        return $this->coupon;
    }

    public function getWeight(){
        $weight = 0;
        foreach($this->goods as $goods){
            $weight += $goods->productSnapShot->weight * $goods->number;
        }
        return $weight;
    }

    public function chargeGoods($exchanger){
        $gb = new Bill();
        foreach($this->goods as $goods){
            $goods->charge($gb, $exchanger);
        }
        return $gb->goodsAmount;
    }
}
