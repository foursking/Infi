<?php
namespace esmeralda\product\service;

use esmeralda\product\service\DbProductService;
use esmeralda\base\model\ObjectCache;
use esmeralda\base\service\G11N;

/**
 * A Wrapper used for caching results
 * @author zbingw
 *
 */
class DbProductServiceWrapper extends DbProductService{// extends AbstractProductService{
	public function __construct($options){
		parent::__construct($options);
	}

	/**
	 * get result directly from cache if exists
	 * @see \esmeralda\product\service\DbProductService::getProductsByIds()
	 */
	public function getProductsByIds($goodsIds, $onSale = true){
		$productRes = array();
		$idsMissInCache = array();
		// get from cache
		foreach ($goodsIds as $id){
			$cacheKey = "base$id";
			if(ObjectCache::contains($cacheKey)) {
				$productRes[] = ObjectCache::fetch($cacheKey);
			} else {
				$idsMissInCache[] = $id;
			}
		}
		// save miss object to cache
		$products = parent::getProductsByIds($idsMissInCache, $onSale);
		foreach ($products as $product){
			$cacheKey = "base$product->id";
			ObjectCache::save($cacheKey, $product);
		}
		$productRes = array_merge($productRes, $products);
		return $productRes;
	}

	/**
	 * get by offset
	 * @see \esmeralda\product\service\DbProductService::getProductsByOffset()
	 */
	public function getProductsByOffset($offset, $size, $onSale = true){
		$cacheKey = "base$offset:$size";
		if(ObjectCache::contains($cacheKey)) {
			return ObjectCache::fetch($cacheKey);
		} else {
			$products = parent::getProductsByOffset($offset, $size, $onSale);
			ObjectCache::save($cacheKey, $products);
			return $products;
		}
	}

	/**
	 * get details by ids
	 * @see \esmeralda\product\service\DbProductService::getProductDetailsByIds()
	 */
	public function getProductDetailsByIds($goodsIds, $onSale = true){
		$productRes = array();
		$idsMissInCache = array();
		foreach ($goodsIds as $id){
			$cacheKey = "details$id";
			if(ObjectCache::contains($cacheKey)) {
				$productRes[] = ObjectCache::fetch($cacheKey);
			} else {
				$idsMissInCache[] = $id;
			}
		}
		$products = parent::getProductDetailsByIds($idsMissInCache, $onSale);
		foreach ($products as $product){
			$cacheKey = "details$product->id";
			ObjectCache::save($cacheKey, $product);
		}
		$productRes = array_merge($productRes, $products);
		return $productRes;
	}

	/**
	 * get details by offset
	 * @see \esmeralda\product\service\DbProductService::getProductDetailsByOffset()
	 */
	public function getProductDetailsByOffset($offset, $size, $onSale = true){
		$cacheKey = "details$offset:$size";
		if(ObjectCache::contains($cacheKey)) {
			return ObjectCache::fetch($cacheKey);
		} else {
			$productDetails = parent::getProductDetailsByOffset($offset, $size, $onSale);
			ObjectCache::save($cacheKey, $productDetails);
			return $productDetails;
		}
	}
	
	/**
	 * get products nls (by ids) without cache
	 * TODO refine to cache product nls by default offset and size
	 * @see \esmeralda\product\service\DbProductService::getProductsNlsByIds()
	 */
	public function getProductsNlsByIds($goodsIds, $language = 'en'){
		return parent::getProductsNlsByIds($goodsIds, $language);
	}

	/**
	 * get product nls by offset
	 * @see \esmeralda\product\service\DbProductService::getProductsNlsByOffset()
	 */
	public function getProductsNlsByOffset($offset, $size, $language = 'en'){
		$cacheKey = "pnl/$language$offset:$size";
		if(ObjectCache::contains($cacheKey)) {
			return ObjectCache::fetch($cacheKey);
		} else {
			$productsNls = parent::getProductsNlsByOffset($offset, $size, $language);
			ObjectCache::save($cacheKey, $productsNls);
			return $productsNls;
		}
	}

	/**
	 * get attributesNls from cache if exists
	 * @see \esmeralda\product\service\DbProductService::getAttributesNls()
	 */
	public function getAttributesNls($attrIds, $language = 'en'){
		$langId = G11N::langId($language);
		$cacheKey = "anl/$language";
		if(ObjectCache::contains($cacheKey)) {
			$attributesNls =  ObjectCache::fetch($cacheKey);
		} else {
			$attributesNls = parent::getAttributesNls('*', $language);
			ObjectCache::save($cacheKey, $attributesNls);
		}
		$attributesNl = $attributesNls[$langId];
		$res = array($langId => array());
		foreach ($attrIds as $attrId){
			if (isset($attributesNl[$attrId]))
				$res[$langId][$attrId] = $attributesNl[$attrId];
		}
		return $res;
	}

	/**
	 * get stylesNls from cache if exists
	 * @see \esmeralda\product\service\DbProductService::getStylesNls()
	 */
	public function getStylesNls($styleIds, $language = 'en'){
		$langId = G11N::langId($language);
		$cacheKey = "snl/$language";
		if(ObjectCache::contains($cacheKey)) {
			$stylesNls = ObjectCache::fetch($cacheKey);
		} else {
			$stylesNls = parent::getStylesNls('*', $language);
			ObjectCache::save($cacheKey, $stylesNls);
		}
		$stylesNl = $stylesNls[$langId];
		$res = array($langId => array());
		foreach ($styleIds as $styleId){
			if (isset($stylesNl[$styleId]))
				$res[$langId][$styleId] = $stylesNl[$styleId];
		}
		return $res;
	}

}
