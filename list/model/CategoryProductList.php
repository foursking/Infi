<?php
namespace lestore_product_list\app\model;

use lestore_base\app\model\Object;

class CategoryProductList{
    private $catId;
    private $productList;
    
    public function __construct(){
    	$this->catId = null;
    	$this->productList = null;
    }
    
    public function initProductList($catProducts){
    	$this->catId = $catProducts['cat_id'];
    	$this->productList = explode(",", $catProducts['goods_ids']);
    }
    
    public function setCatId($catId){
    	$this->catId = $catId;
    }
    
    public function setProductList($productList){
    	$this->productList = $productList;
    }
    
    public function getCatId(){
    	return $this->catId;
    }
    
    public function getProductList(){
    	return $this->productList;
    }
    
	public function getAllAttrs(){
		$attrs = array(
				'catId' => $this->catId,
				'productList' => $this->productList);
		return $attrs;
	}
}
