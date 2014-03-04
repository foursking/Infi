<?php
namespace lestore_product_list\app\service;

use lestore_product_list\app\model\ProductList;
use lestore_product_list\app\service\JsonCategoryProductListService;

class ProductListServiceFactory{
	private $useJson;
	private $jsonFile;
	private $jsonCatProdListService;
	
	public function __construct(){
		$this->useJson = true;
	}
	
	public function initJsonFile($jsonFile){
		$this->jsonFile = $jsonFile;
	}
	
	public function initCategoryDAO($dao){
		if (null == $this->jsonCatProdListService){
			$this->jsonCatProdListService = new JsonCategoryProductListService();
		}
		$this->jsonCatProdListService->initDAO($dao);
	}

	public function getCategoryProducts($catId, $offset, $size){
		if (null == $this->jsonCatProdListService){
			$this->jsonCatProdListService = new JsonCategoryProductListService();
		}
		if ($this->jsonCatProdListService->getJsonFile() != $this->jsonFile){
			$this->jsonCatProdListService->initJsonFile($this->jsonFile);
		}
		return $this->jsonCatProdListService->getProducts($catId, $offset, $size);
	}
	
	public function cat2Json(){
		return $this->jsonCatProdListService->toJson();
	}
}

