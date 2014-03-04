<?php
namespace lestore_order\app\model;

use lestore_base\app\model\AttributesCopier;

class ProductSnapShot{
    use AttributesCopier;

    public $productId;
    public $productSn;
    //public $productName;
    public $catId;
    //public $parentCatId;
    public $weight;

    public $isReal;
    public $weeklyDeal;

    public $shopPrice;
    public $totalShopPrice;

    public function __construct($goods){
        $this->copyAttributes($goods, true);
    }

    public function isFreeShippingGoods(){
        global $container;
        $siteConf = $container['siteConf'];
        return in_array($this->id, $siteConf['goods_free_shipping']);
    }

    public function isFreeShippingCat(){
        global $container;
        $siteConf = $container['siteConf'];
        return in_array($this->catId, $siteConf['category_free_shipping']);
    }

    public function isAccessory(){
        global $container;
        $categoryService = $container['category']; 
        $category = $container['category']->getCategory($this->catId);
        return $category->isAccessory();
    }

    public static function snapshot($product){
        return new ProductSnapShot($product);
    }
}
