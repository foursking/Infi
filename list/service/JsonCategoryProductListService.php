<?php

namespace lestore_product_list\app\service;

use lestore_product_list\app\model\CategoryProductList;
use lestore_product_list\app\service\AbstractCategoryProductListService;

class JsonCategoryProductListService extends AbstractCategoryProductListService{
	const PRINT_TAG = JSON_PRETTY_PRINT;
	
	private $jsonFile;
    
    
    public function __construct(){
    	parent::__construct();
    }
    
    public function initDAO($dao){
    	$this->dao = $dao;
    }
    
    public function initJsonFile($jsonFile){
    	$this->jsonFile = $jsonFile;
    	$this->catProductsMap = $this->createCatProductsMap($jsonFile);
    }
    
    public function getJsonFile(){
    	return $this->jsonFile;
    }

    public function getProducts($catId, $offset, $size){
		$startIndex = $offset;
		$endIndex = $offset + $size - 1;
		$allProductList = $this->catProductsMap["$catId"]->getProductList();
		$availProductList = array();
		for ($i=$startIndex; $i<=$endIndex; $i++) {
			$availProductList[] = $allProductList["$i"];
		}
		$productList = new CategoryProductList();
		$productList->setCatId($catId);
		$productList->setProductList($availProductList);
    	return $productList;
    }
    
    protected function createCatProductsMap($jsonFile){
    	$jsonStr = file_get_contents($jsonFile);
    	$json = json_decode($jsonStr,true);
    	$catProductsList = $json['category_products'];
    	$catProductsMap = array();
    	foreach($catProductsList as $catProducts){
    		$productList = new CategoryProductList();
    		$productList->initProductList($catProducts);
    		$catProductsMap[$catProducts['cat_id']] = $productList;
    	}
    	return $catProductsMap;
    }

    
    public function toJson(){
    	$catIds = '*';
    	$productListInfo = $this->dao->getCatProductList($catIds);
    	return json_encode(
    			array('id' => 'all', 'category_products'=>$productListInfo)
    			, self::PRINT_TAG
    	);
    }
    
//     public function getNl($language){
//     	$jsonStr = file_get_contents($this->jsonFile . '.nl.' . $language);
//     	$json = json_decode($jsonStr,true);
//     	$nl = $json['nl'];
//     	if(isset($nl[$language])){
//     		return $nl[$language];
//     	}else{
//     		return array();
//     	}
//     }
    
}
