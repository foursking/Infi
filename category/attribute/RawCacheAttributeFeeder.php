<?php
namespace esmeralda\category\attribute;

use esmeralda\base\G11N;
use esmeralda\base\LogFactory;

class RawCacheAttributeFeeder{
    CONST CACHE_FILTER    = 'cache_attribute_raw_';
    CONST CACHE_FILTER_NL = 'cache_attribute_raw_nl_';

    CONST FLUSH_THRESHOLD = 0.8;
    CONST LOCK_TIMEOUT    = 60;

    private $container;
    private $cache;

    public function __construct($container){
        $this->container = $container;
        $this->cache = $container['cache'];
    }

    protected function get($key, $getFromSrc){
        $cached = $this->cache->fetch($key);
        $logger = LogFactory::get('attr_cache');
        if(false === $cached){
            $logger->debug("Cache Miss. key:$key");
            $data = call_user_func($getFromSrc);
            if(isset($data['base'])){
                foreach($data['base'] as $k => &$node){
                    if(isset($node->bs)){
                        $node->bs_s = $node->bs->jsonSerialize();
                    }
                }
            }
            $timeout = call_user_func($this->cache->timeout);
            $toCache = array('timeout' => time() + intval($timeout * self::FLUSH_THRESHOLD), 'data'=>$data);
            $this->cache->save($key, $toCache, $timeout);
            return $data;
        }else{
            $timeout = $cached['timeout'];
            $now = time();
            if($now > $timeout){
                $updateKey = $key.'__update__';
                $lock = $this->container['lock'];
                if(true == $lock->lock($updateKey, self::LOCK_TIMEOUT)){
                    $logger->debug("Cache Update. key:$key");
                    $data = call_user_func($getFromSrc);
                    if(isset($data['base'])){
                        foreach($data['base'] as $k => &$node){
                            if(isset($node->bs)){
                                $node->bs_s = $node->bs->jsonSerialize();
                            }
                        }
                    }
                    $timeout = call_user_func($this->cache->timeout);
                    $flushtime = time() + intval($timeout * self::FLUSH_THRESHOLD);
                    $toCache = array('timeout' => $flushtime, 'data'=>$data);
                    $this->cache->save($key, $toCache, $timeout);
                    $lock->unlock($updateKey);
                    return $data;
                }else{
                    $logger->debug("Cache Locked. key:$key");
                }
            }else{
                $logger->debug("Cache Hit. key:$key");
            }
            $data = $cached['data'];
            if(isset($data['base'])){
                foreach($data['base'] as $k => &$node){
                    if(isset($node->bs_s)){
                        $node->bs->parse($node->bs_s);
                        unset($node->bs_s);
                    }
                }
            }
            return $cached['data'];
        }
    }

    public function getRaw($catId){
        $key = self::CACHE_FILTER.$catId;
        $container = $this->container;
        return $this->get($key, function() use ($container, $catId){
            $siteConf = $container['siteConf'];
            $dao = new AttributeDao($container['db'], $siteConf['domain']);
            $attributeS = new DbAttributeService($dao,$catId,true);
            if(0 == count($attributeS->getAllNodes())){
                $attributeS = new DbAttributeService($dao,$catId,false);
            }
            return $attributeS->raw();
        });
    }

    public function getRawNl($catId, $lang){
        $key = self::CACHE_FILTER_NL."{$catId}_{$lang}";
        $container = $this->container;
        return $this->get($key, function() use ($container, $catId, $lang){
            $siteConf = $container['siteConf'];
            $dao = new AttributeDao($container['db'], $siteConf['domain']);
            $attributeS = new DbAttributeService($dao,$catId,true);
            if(0 == count($attributeS->getAllNodes())){
                $attributeS = new DbAttributeService($dao,$catId,false);
            }
            return $attributeS->rawNl($lang);
        });
    }
}
