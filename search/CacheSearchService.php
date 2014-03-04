<?php
namespace esmeralda\search;

class CacheSearchService extends SearchService{

    CONST CACHE_SEARCH_COUNT = 'cache_search_count_';
    private $cache;

    public function __construct($endpoint, $domain, $cache){
        parent::__construct($endpoint, $domain);
        $this->cache = $cache;
    }

    public function count($query){
        $query['orderBy'] = '';
        $query['limit'] = 1;
        $query['offset'] = 0;

        $key = self::CACHE_SEARCH_COUNT . md5(json_encode($query));

        $cached = $this->cache->fetch($key);
        if(false === $cached){
            $rs = parent::count($query);
            $this->cache->save($key, $rs, call_user_func($this->cache->timeout));
            return $rs;
        }else{
            return intval($cached);
        }
    }
}
