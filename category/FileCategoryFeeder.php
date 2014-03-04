<?php
namespace esmeralda\category;

class FileCategoryFeeder{
    public function __construct($root){
        $this->root = $root;
    }
    public function getJson(){
        return file_get_contents($this->root);
    }

    public function getNlJson($lang){
        return file_get_contents($this->root. '.nl.' . $language);
    }
}
