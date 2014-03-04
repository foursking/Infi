<?php

namespace lestore_product_list\app\service;

class DBDomainProductListService {
	
    
    public function __construct($dao){
    	$this->dao = $dao;
    }

//     public function getProductIdsList($domains, $offset = '0', $size = '*'){
//     	foreach ($domains as $domain){
//     		$domain = strtolower($domain);
//     	}
//     	$productIdsList = $this->dao->getProductIds($domains, $offset, $size);
    	
// //     	var_dump($productIdsList);
    	
//     	$res = array();
//     	array_walk($productIdsList, function(&$item) use (&$res){
//     		if(isset($res[$item['projectName']])){
//     			$ids = &$res[$item['projectName']];
//     		}else{
//     			$ids = array();
//     			$res[$item['projectName']] = &$ids;
//     		}
//     		$ids[] = $item['productId'];
//     	});
//     	return $res;
//     }
    
    public function getProductIds($domain, $offset = '0', $size = '*'){
    	$productIdsList = $this->dao->getProductIds($domain, $offset, $size);
    	
    	$res = array();
    	array_walk($productIdsList, function(&$item) use (&$res){
    		if(isset($res[$item['projectName']])){
    			$ids = &$res[$item['projectName']];
    		}else{
    			$ids = array();
    			$res[$item['projectName']] = &$ids;
    		}
    		$ids[] = $item['productId'];
    	});
    	return $res;
    }
    
    public function getAttributeIds($offset = '0', $size = '*'){
    	return $this->dao->getAttributeIds();
    }
    
    public function getStyleIds($offset = '0', $size = '*'){
    	return $this->dao->getStyleIds();
    }
}
