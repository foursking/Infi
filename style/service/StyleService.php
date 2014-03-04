<?php
namespace lestore_style\app\service;
use lestore_base\app\service\AbstractTreeService;

use lestore_style\app\model\SizeStyle;
use lestore_style\app\model\WrapStyle;

class StyleService { //extends AbstractTreeService {
    public function __construct($dao){
        $this->dao = $dao;
    }

    public function getStyles($styleIds){
        $styles = array();
        $rs = $this->dao->getStyles($styleIds);
        foreach($rs as $style){
            switch($style->name){
            case 'size':
                $style = new SizeStyle($style);
                break;
            case 'wrap':
                $style = new WrapStyle($style);
                break;
            }
            $styles[$style->id] = $style;
        }
        return $styles;
    }

}
