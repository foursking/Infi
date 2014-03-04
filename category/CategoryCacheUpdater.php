<?php
namespace esmeralda\category;

use esmeralda\base\G11N;

class CategoryCacheUpdater{
    public function __construct($container){
        $this->container = $container;
    }

    public function update($langs = null){
        if($langs == null){
            $langs = G11N::$langId2Code;
        }
        $categoryS = $this->container['category'];
        foreach($langs as $lang){
            $categoryS->getNl($lang);
        }
    }
}
