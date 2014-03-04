<?php

namespace lestore_coupon\app\model;

use lestore_base\app\model\AttributesCopier;

class Coupon{
    use AttributesCopier;

    public $code;
    public $target;

    public $type;
    public $value;

    public $bonus;
    public $bonusExchange;

    public $stime;
    public $etime;

    public $userId;

    public $usedTimestamp;
    public $usedTimes;
    public $canUseTimes;
    public $minimum;

    public $goodsId;
    public $catId;

    public function __construct(){
    }

    #public function __construct($order){
    #    $this->copyAttributes($order, true);
    #}

    public function charge($bill, $order, $exchanger){
        if(!$this->isValid($order)){
            return;
        }
        $bonus = 0;
        switch($this->target){
        case 'goods':
            if($this->goodsId != 0){
                $bonus = $this->chargeGoods($order);
            }else if($this->catId != 0){
                $bonus = $this->chargeCategory($order);
            }else if($this->goodsId == 0 && $this->catId == 0){
                $bonus = $this->chargeAll($order);
            }
            break;
        case 'shipping_fee':
            $bonus = $this->chargeShipping($order);
            break;
        default:
        }

		$bonus = number_format($bonus, 2, '.', '');
		$bonus = -1 * $bonus;

        $bill->bonus = $exchanger($bonus);        
    }

    private function chargeGoods($order, $bonusGoodsId){
        $bonus = 0;
        foreach($order->goods as $goods){
            if($goods->id = $bonusGoodsId){
                $bonus += $this->getBonus($goods->productSnapShot->shopPrice, $this->type, $this->value);
            }
        }
        return $bonus;
    }

    private function chargeCategory($order, $bonusCatId){
        $total = 0;
        foreach($order->goods as $goods){
            if($goods->catId = $bonusCatId){
                $total += $goods->productSnapShot->shopPrice; 
            }
        }
        return $this->getBonus($total, $this->type, $this->value);
    }

    private function chargeAll($order){
        $total = 0;
        foreach($order->goods as $goods){
            $total += $goods->productSnapShot->shopPrice; 
        }
        return $this->getBonus($total, $this->type, $this->value);
    }


    private function chargeShipping($order){
        return getBonus($order->shippingFee, $this->type, $this->value);
    }

    private function getBonus($base, $type, $value){
        switch($type){
        case 'percent':
            if($value > 0 && $value < 1){
                return $base * $value;
            }
            break;
        case 'value':
            if($value > 0){
                return $value > $base ? $base: $value;
            }
            break;
        }
        return 0;
    }

    public function isValid($order){
        $goodsAmount = $order->chargeGoods(function($fee){
            return $fee;
        });
        return $this->usableBy($order->user->userId) && $this->moreThanMin($goodsAmount) 
            && $this->relatedTo($order);
    }

    private function usableBy($userId){
        return $this->userId <= 0 || $this->userId == $userId;
    }

    private function moreThanMin($amount){
        return $amount >= $this->minimum;
    }

    private function relatedTo($order){
        return ($this->goodsId <= 0 || $order->hasGoods($this->goodsId))
            && ($this->catId <= 0 || $order->hasCategory($this->catId));
    }


    public function isUsable(){
        return $this->isAvailable() && $this->isActive(time());
    }

    public function isAvailable(){
        return $this->usedTimestamp <= 0 || $this->usedTimes < $this->canUseTimes;
    }

    public function isActive($time){
        return ($this->stime <= 0 || $this->stime < $time) 
            && ($this->etime <= 0 || $this->etime > $time);
    }

}
