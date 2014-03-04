<?php
namespace esmeralda\product\model;

class ProductDetail extends Product{
	public $onSale;
	public $sku;
	public $isNew;
	public $wrapPrice;
	public $weight;
	public $questionNo;
	public $modelCard;
	public $weeklyDeal;
	public $catIds;
	
	public $addTime;
	public $updateTime;
	public $salesOrder;
	public $goodsOrder;

	public $imgs;
	public $attributes;
	public $styles;
	public $tags;
	public $recommendation;
	
	public function setAttributes($attributes){
		$this->attributes = $attributes;
	}
	
	public function setStyles($styles){
		$this->styles = $styles;
	}
	
	public function getImgs(){
		return $this->imgs;
	}
	
	public function getAttributes(){
		return $this->attributes;
	}
	
	public function getStyles(){
		return $this->styles;
	}
}
