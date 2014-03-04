<?php
namespace esmeralda\category;

use esmeralda\base\LogFactory;

class RawCacheCategoryFeeder{
    CONST CACHE_CAT_BASE = 'cache_cat_base_raw';
    CONST CACHE_CAT_NL = 'cache_cat_raw_nl_';

    CONST FLUSH_THRESHOLD = 0.8;
    CONST LOCK_TIMEOUT = 60;

    private $cache;
    private $container;

    public function __construct($container){
        $this->cache = $container['cache'];
        $this->container = $container;
    }

    public function get($key, $getFromSrc){
        $cached = $this->cache->fetch($key);
        $logger = LogFactory::get('cat_cache');
        if(false === $cached){
            $logger->debug("Cache Miss. key:$key");
            $data = call_user_func($getFromSrc);
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
            return $cached['data'];
        }
    }

    public function getRaw(){
        $container = $this->container;
        return $this->get(self::CACHE_CAT_BASE, function() use ($container){
            $siteConf = $container['siteConf'];
            $dao = new CategoryDao($container['db'],$siteConf['domain']);
            $catS = new DbCategoryService($dao);
            return $catS->raw();
        });
    }
    
    public function getRawNl($lang){
        $container = $this->container;
        return $this->get(self::CACHE_CAT_NL.$lang, function() use ($container, $lang){
            $siteConf = $container['siteConf'];
            $dao = new CategoryDao($container['db'],$siteConf['domain']);
            $catS = new DbCategoryService($dao);
            return $catS->rawNl($lang);
        });
    }

    private function invalidateNl($catId){
        foreach(G11N::$langId2Code as $lang){
            $key = self::CACHE_CAT_NL.$lang;
            $this->cache->delete($key);
        }
    }
}
