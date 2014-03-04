<?php

namespace lestore_product_list\app\service;

abstract class AbstractCategoryProductListService{
	const ID = '__CATEGORY_PRODUCT_LIST_SERVICE__';
   
    protected $catProductsMap;
    protected $dao;
    
    private $categoryId;
    
    
    public function __construct(){
    }
    
    protected abstract function createCatProductsMap($resource);
    public abstract function getProducts($catId, $offset, $size);
//     public abstract function getNl($language);
    
//     public function nlize($object, $nl){
//     	return $object->copyNonEmptyVFrom($nl[$object->getCatId()]);
//     }
}
