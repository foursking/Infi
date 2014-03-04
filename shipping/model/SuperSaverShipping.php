<?php
namespace lestore_shipping\app\model;

class SuperSaverShipping extends AbstractShipping{
    public function __construct($id, $config, $feeConfig, $service){
        parent::__construct($id, $config, $feeConfig, $service);
    }

    public function charge($bill, $order, $exchanger){
        foreach($order->goods as $goods){
			if (!$goods->productSnapShot->isFreeShippingGoods()){
                return parent::charge($bill, $order, $exchanger);
			}
        }
    }

    public function isValid($order){
        // {{{ 由于 ems 涨价，商品总重量大于 400g 的不能使用 Super Saver Shipping - 取消($goods_weight > 400 && $vv['sm_id'] == 3)
        // 只要有衣服就不能用小包 和 4271塞尔维亚 3=Super Saver Shipping 不能用小包
        if ($order->hasDress() || $order->shippingAddress['country'] == 4271) {
            return false;
        }
        // 超过50美金（未减coupon之前）的不能使用小包
        if ($order->_goods_amount_no_coupon > 50) {
            return false;
        }

        return true;
    }
}
