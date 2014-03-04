<?php
namespace esmeralda\product\service;

use esmeralda\product\dao\ProductDao;
use esmeralda\product\service\AbstractProductService;
use esmeralda\product\model\Product;
use esmeralda\product\model\ProductDetail;
use esmeralda\base\G11N;

class JsonProductService extends AbstractProductService{
	const ID = '__JSON_PRODUCT_SERVICE__';
	const PRINT_TAG = JSON_PRETTY_PRINT;
	
	private $jsonFile;
	private $productList;
	private $productDetailList;
	
	public function __construct(){
		parent::__construct();
	}
	
	//--------------- initialization ---------------
	
	public function initJsonFile($jsonFile){
		$this->jsonFile = $jsonFile;
		$jsonStr = file_get_contents($jsonFile);
		$json = json_decode($jsonStr,true);
		if ('base' == $json['type']){
			$this->productList = $this->createProductList($jsonFile);
		} elseif ('detail' == $json['type']){
			$this->productDetailList = $this->createProductDetailList($jsonFile);
		} else {
			echo 'error exists in JsonProductService::initJsonFile().\n';
		}
	}
	
	//--------------- DB to Json ---------------
	
	public function base2Json(){
		$goodsIds = '*';
		$goodsInfoList = $this->dao->queryProductList($goodsIds);
		return json_encode(
				array('id' => 'all', 'type' => 'base', 'product'=>$goodsInfoList)
				, self::PRINT_TAG
		);
	}
	
	public function detail2Json(){
		$goodsIds = '*';
		$goodsInfoList = $this->dao->queryProductDetailList($goodsIds);
		return json_encode(
				array('id' => 'all', 'type' => 'detail', 'product'=>$goodsInfoList)
				, self::PRINT_TAG
		);
	}
	
	public function base2NlJson($language){
		$goodsIds = '*';
		$onSale = true;
		$langId = G11N::langId($language);
		$goodsInfoList = $this->dao->queryProductList($goodsIds, $onSale, $langId);
		return json_encode(
				array('id' => 'all', 'type' => 'base', 'nl' => array($language => $goodsInfoList))
				, self::PRINT_TAG
		);
	}
	
	public function detail2NlJson($language){
		$goodsIds = '*';
		$onSale = true;
		$langId = G11N::langId($language);
		$goodsInfoList = $this->dao->queryProductDetailList($goodsIds, $onSale, $langId);
		return json_encode(
				array('id' => 'all', 'type' => 'detail', 'nl' => array($language => $goodsInfoList))
				, self::PRINT_TAG
		);
	}
	
	//--------------- mulit-languages & service ---------------
	
	/**
	 * Get multi-language info of products.
	 * @param unknown_type $language
	 * @return mixed|multitype:
	 */
	public function getProductsNl($language){
		$jsonStr = file_get_contents($this->jsonFile . '.nl.' . $language);
		$json = json_decode($jsonStr,true);
		$nl = $json['nl'];
		$productList = array();
		if(isset($nl[$language])){
			foreach($nl[$language] as $nl){
				$product = new Product();
				$product->initProductInfo($nl);
				$productList[$nl['goods_id']] = $product;
			}
		}
		return $productList;
	}
	
	/**
	 * Get multi-language detial info of products.
	 * @param unknown_type $language
	 * @return mixed|multitype:
	 */
	public function getProductsDetailNl($language){
		$jsonStr = file_get_contents($this->jsonFile . '.nl.' . $language);
		$json = json_decode($jsonStr,true);
		$nl = $json['nl'];
		$productList = array();
		if(isset($nl[$language])){
			foreach($nl[$language] as $nl){
				$productDetail = new ProductDetail();
				$productDetail->initProductDetailInfo($nl);
				$productList[$nl['goods_id']] = $productDetail;
			}
		}
		return $productList;
	}
	
	public function getProduct($id){
		$product = $this->productList["$id"];
		return $product;
	}
	
	public function getProductDetail($id){
		$product = $this->productDetailList["$id"];
		return $product;
	}
	
	//--------------- private functions ---------------
	
	private function createProductList($jsonFile){
		$jsonStr = file_get_contents($jsonFile);
		$json = json_decode($jsonStr,true);
		$products = $json['product'];
	
		$productList = array();
		foreach($products as $productInfo){
			$product = new Product();
			$product->initProductInfo($productInfo);
			$productList[$productInfo['goods_id']] = $product;
		}
		return $productList;
	}
	
	private function createProductDetailList($jsonFile){
		$jsonStr = file_get_contents($jsonFile);
		$json = json_decode($jsonStr,true);
		$products = $json['product'];
		$productDetailList = array();
		foreach($products as $productInfo){
			$product = new ProductDetail();
			$product->initProductDetailInfo($productInfo);
			$productDetailList[$productInfo['goods_id']] = $product;
		}
		return $productDetailList;
	}
}
