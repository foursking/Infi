<?php
namespace esmeralda\base;

class Lock{
    private $domain;
    private $version;
    private $memcache;

    public function __construct($memcache, $domain){
        $this->memcache = $memcache;
        $this->domain = $domain;
        $this->version = 1;
    }

    protected function getKey($id){
        return "{$this->domain}[{$id}][{$this->version}]";
    }

    public function lock($id, $lifeTime = 0){
        return $this->memcache->add($this->getKey($id), true, false, $lifeTime);
    }

    public function unlock($id, $lifeTime = 0){
        return $this->memcache->delete($this->getKey($id), $lifeTime);
    }
}



