<?php
namespace lestore_product\app\service;

use lestore_product\app\dao\ProductDao;
use lestore_product\app\service\AbstractProductService;
use lestore_product\app\model\Product;
use lestore_product\app\model\ProductDetail;
use lestore_product\app\service\JsonProductService;
use lestore_product\app\service\DbProductService;

class ProductServiceFactory{
	
	private $jsonProductService;
	private $dbProductService;
	
	private $useJson;
	
	public function __construct(){
		$this->useJson = true;
	}
	
	public function initJsonFile($jsonFile){
		if ($this->useJson) {
			if (null == $this->jsonProductService){
				$this->jsonProductService = new JsonProductService();
			}
			$this->jsonProductService->initJsonFile($jsonFile);
		}
	}
	
	public function getProduct($id){
		if ($this->useJson) {
			return $this->jsonProductService->getProduct($id);
		} else {
			return $this->dbProductService->getProduct($id);
		}
	}
	
	public function getProductDetail($id){
		if ($this->useJson) {
			return $this->jsonProductService->getProductDetail($id);
		} else {
			return $this->dbProductService->getProductDetail($id);
		}
	}
	
	public function getProductsNl($language){
		if ($this->useJson) {
			return $this->jsonProductService->getProductsNl($language);
		} else {
			return $this->dbProductService->getProductsNl($language);
		}
	}

	public function getProductsDetailNl($language){
		if ($this->useJson) {
			return $this->jsonProductService->getProductsDetailNl($language);
		} else {
			return $this->dbProductService->getProductsDetailNl($language);
		}
	}

	/**
	 * 
	 * @param unknown_type $product
	 * @param unknown_type $nl
	 * @return unknown
	 */
	public function nlize($product, $nl){
		$product->copyNonEmptyVFrom($nl[$product->getId()]);
		return $product;
	}

}
