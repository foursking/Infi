<?php
namespace esmeralda\product\service;

use esmeralda\base\G11N;

abstract class AbstractProductService{
	const ID = '__PRODUCT_SERVICE__';
	const PRINT_TAG = JSON_PRETTY_PRINT;
    
    public abstract function getProducts($id);
    public abstract function getProductsDetail($id);
    
    public abstract function getProductsNl($language);
    public abstract function getProductsDetailNl($language);
}
