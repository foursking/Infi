<?php
namespace esmeralda\product\service;

use esmeralda\product\dao\ProductDao;
use esmeralda\product\model\Product;
use esmeralda\product\service\AbstractProductService;
use esmeralda\product\model\ProductDetail;
use esmeralda\plist\dao\ProductListDao;
use esmeralda\plist\service\DBDomainProductListService;
use Doctrine\Common\Cache\MemcacheCache;
use esmeralda\base\G11N;

class DbProductService{// extends AbstractProductService{
	private $dao;
	private $projectName;
	private $productList;

	public function __construct($dao){
		$this->dao = $dao;
		//parent::__construct();
	}

	/**
	 * initialize productlist service to get all product ids of specified project
	 * @param unknown_type $dao
	 */
	public function setDomainInfo($projectName, $productList){
		$this->projectName = $projectName;
		$this->productList = $productList[strtolower($this->projectName)];
	}

	// get product
	public function getProductsByIds($goodsIds, $onSale = true){
		return $this->getProducts($goodsIds, $this->projectName, $onSale);
	}

	public function getProductsByOffset($offset, $size, $onSale = true){
		$ids = $this->getIdsByOffset($offset, $size);
		return $this->getProducts($ids, $this->projectName, $onSale);
	}

	// get product details
	public function getProductDetailsByIds($goodsIds, $onSale = true){
		return $this->getProductDetails($goodsIds, $this->projectName, $onSale);
	}

	public function getProductDetailsByOffset($offset, $size, $onSale = true){
		$ids = $this->getIdsByOffset($offset, $size);
		return $this->getProductDetails($ids, $this->projectName, $onSale);
	}

	// get product nls
	public function getProductsNlsByIds($goodsIds, $language = 'en'){
		return $this->getProductsNls($goodsIds, $language);
	}

	public function getProductsNlsByOffset($offset, $size, $language = 'en'){
		$ids = $this->getIdsByOffset($offset, $size);
		return $this->getProductsNls($ids, $language);
	}

	// get product attribute nls
	public function getAttributesNls($attrIds, $language = 'en'){
		$langId = G11N::langId($language);
		$rs = $this->dao->getAttributesNls($attrIds, $langId);
		$res = array($langId => array());
		array_walk($rs, function(&$item) use (&$res){
			if(isset($res[$item['langId']])){
				$nls = &$res[$item['langId']];
			}else{
				$nls = array();
				$res[$item['langId']] = &$nls;
			}
			$nls[$item['id']] = $item;
		});
		return $res;
	}

	// get product style nls
	public function getStylesNls($styleIds, $language = 'en'){
		$langId = G11N::langId($language);
		$rs = $this->dao->getStylesNls($styleIds, $langId);
		$res = array($langId => array());
		array_walk($rs, function(&$item) use (&$res){
			if(isset($res[$item['langId']])){
				$nls = &$res[$item['langId']];
			}else{
				$nls = array();
				$res[$item['langId']] = &$nls;
			}
			$nls[$item['id']] = array('value' => $item['value']);
		});
		return $res;
	}

	// get product tag nls
	public function getTagsNlsByIds($goodsIds, $language = 'en'){
		return $this->getTagsNls($goodsIds, $language);
	}

	public function getTagsNlsByOffset($offset, $size, $language = 'en'){
		$ids = $this->getIdsByOffset($offset, $size);
		return $this->getTagsNls($ids, $language);
	}

	// nlize product by nls
	public function nlize($base, $nls){
		$nl = $nls[$base->id()];
		foreach($nl as $key => $value){
			$base->$key = $value;
		}
		return $base;
	}

	// get product base
	private function getProducts($goodsIds, $projectName, $onSale){
		return $this->dao->getProducts($goodsIds, $projectName, $onSale);
	}

	// get product details
	private function getProductDetails($goodsIds, $projectName, $onSale){
		$products = $this->dao->getProducts($goodsIds, $projectName, $onSale);

		$attributes = $this->getAttributes($goodsIds);
		$styles = $this->getStyles($goodsIds);
		$tags = $this->getTags($goodsIds);
		$recommendation = $this->getRecommendation($goodsIds);

		foreach ($products as $product){
			$product->attributes = isset($attributes[$product->id])==1?$attributes[$product->id]:array();
			$product->styles = isset($styles[$product->id])==1?$styles[$product->id]:array();
			$product->tags = isset($tags[$product->id])==1?$tags[$product->id]:array();
			$product->recommendation = isset($recommendation[$product->id])==1?$recommendation[$product->id]:array();
		}

		return $products;
	}

	// get product nls
	private function getProductsNls($goodsIds, $language){
		$langId = G11N::langId($language);
		$rs = $this->dao->getProductsNls($goodsIds, $langId);
		$res = array($langId => array());
		array_walk($rs, function(&$item) use (&$res){
			if(isset($res[$item['langId']])){
				$nls = &$res[$item['langId']];
			}else{
				$nls = array();
				$res[$item['langId']] = &$nls;
			}
			$nls[$item['id']] = $item;
		});
		return $res;
	}

	// get product attributes
	private function getAttributes($goodsIds){
		$rs = $this->dao->getAttributes($goodsIds);
		$res = array();
		array_walk($rs, function(&$item) use (&$res) {//, &$ids){
			if(isset($res[$item['goods_id']])){
				$attrs = &$res[$item['goods_id']];
			}else{
				$attrs = array();
				$res[$item['goods_id']] = &$attrs;
			}
			if($item['attr_kid'] != 0){
				$vids = explode(',',$item['attr_vids']);
				$attrs[$item['attr_kid']] = $vids;
			}
		});
		return $res;
	}

	// get product styles
	private function getStyles($goodsIds){
		$rs = $this->dao->getStyles($goodsIds);
		$res = array();
		array_walk($rs, function(&$item) use (&$res){
			if($item['parent_id'] != 0){
				if(isset($res[$item['goods_id']][$item['parent_id']])){
					$style_values = &$res[$item['goods_id']][$item['parent_id']];
				}else{
					$style_values = array();
					$res[$item['goods_id']][$item['parent_id']] = &$style_values;
				}
				$style_values[] = $item['style_id'];
			}
		});
// 		var_dump($res);
		return $res;
	}

	// get product tags
	private function getTags($goodsIds){
		$rs = $this->dao->getTags($goodsIds, 1);
		$res = array();
		array_walk($rs, function(&$item) use (&$res){
			if($item['goods_id'] != 0){
				$res[$item['goods_id']] = explode(',', $item['goods_tag']);
			}
		});
		return $res;
	}

	// get product tag nls
	private function getTagsNls($goodsIds, $language){
		$langId = G11N::langId($language);
		$rs = $this->dao->getTags($goodsIds, $langId);
		$res = array($langId => array());
		array_walk($rs, function(&$item) use (&$res){
			if(isset($res[$item['langId']])){
				$nls = &$res[$item['langId']];
			}else{
				$nls = array();
				$res[$item['langId']] = &$nls;
			}
			$nls[$item['goods_id']] = explode(',', $item['goods_tag']);
		});
		return $res;
	}

	// get product recommendation info
	private function getRecommendation($goodsIds){
		$rs = $this->dao->getRecommendation($goodsIds);
		$res = array();
		array_walk($rs, function(&$item) use (&$res) {//, &$ids){
			if(isset($res[$item['goods_id']])){
				$recomm = &$res[$item['goods_id']];
			}else{
				$recomm = array();
				$res[$item['goods_id']] = &$recomm;
			}
			$recomm[$item['cat_id']] = $item['display_order'];
		});
		return $res;
	}

	private function getIdsByOffset($offset, $size){
		$ids = array();
		for ($i = $offset; $i < $size+$offset && $i < count($this->productList); $i++) {
			$ids[] = $this->productList[$i];
		}
		return $ids;
	}
}
