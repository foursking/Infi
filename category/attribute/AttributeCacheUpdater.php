<?php
namespace esmeralda\category\attribute;

use esmeralda\base\G11N;

class AttributeCacheUpdater{
    public function __construct($container){
        $this->container = $container;
    }
    public function update($catId = null, $langs = null){
        if($langs == null){
            $langs = G11N::$langId2Code;
        }
        $feeder = new RawCacheAttributeFeeder($this->container);
        if($catId == null){
            $categoryS = $this->container['category'];
            foreach($categoryS->getAllNodes() as $cat){
                $attributeS = new RawAttributeService($feeder, $cat->id());
                foreach($langs as $lang){
                    $anl = $attributeS->getNl($lang);
                }
            }
        }else{
            $catId = intval($catId);
            $attributeS = new RawAttributeService($feeder, $catId);
            foreach($langs as $lang){
                $anl = $attributeS->getNl($lang);
            }
        }
    }
}
