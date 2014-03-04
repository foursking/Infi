<?php
namespace esmeralda\category;

class FileAttributeFeeder{
    public function __construct($root){
        $this->root = $root;
    }
    public function getJson($catId, $isFilter){
        $type = $isFilter? 'filter':'cate';
        return file_get_contents($this->root . '.' . $type. '.' . $catId);
    }

    public function getNlJson($lang){
        return file_get_contents($this->root. '.nl.' . $lang);
    }
}
