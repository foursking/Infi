<?php
namespace esmeralda\product\model;

class Product{
    public $id;
    public $sn;
    public $thumb;
    public $price;
    public $marketPrice;
    public $colorNo;
    public $commentsNo;
    public $rating;
    //nls
    public $name;
    public $urlName;
	public $details;
	public $des;
	public $keywords;
    
    public function __construct(){
    }
    
    public function id(){
        return $this->id;
    }

    public function getPriceOff(){
    	$goods_off = ($this->marketPrice - $this->price) / $this->marketPrice * 100;
    	return round($goods_off / 5) * 5;
    }
}
