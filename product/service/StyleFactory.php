<?php
namespace esmeralda\product\service;

use esmeralda\product\model\style\CommonStyle;
use esmeralda\product\model\style\ColorStyle;
use esmeralda\product\model\style\BodiceColorStyle;
use esmeralda\product\model\style\EmbroideryColorStyle;
use esmeralda\product\model\style\SashStyle;
use esmeralda\product\model\style\SizeStyle;
use esmeralda\product\model\style\SkirtColorStyle;
use esmeralda\product\model\style\WrapStyle;

class StyleFactory{
    public function createStyles($goods, $stylesJson){
        $goodsStyles = array();
        $styles = json_decode($stylesJson, true);
        $extra = null;
        if (isset($styles['input']) && is_array($styles['input'])) {
            $extra = $styles['input'];
        }
        if (isset($styles['select']) && is_array($styles['select'])) {
            foreach ($styles['select'] as $sname => $svalue) {
                $sname = strtolower($sname);
                $svalue = strtolower($svalue);
                if($sname == 'color' &&
                    isset($styles['select']['bodice_color']) && intval($styles['select']['bodice_color']) > 0 
                ){
                    //TODO verify behavior
                    $sname = 'skirt_color'; 
                }
                $gs = $this->createStyle($goods, $sname, $svalue, $extra);
                $goodsStyles[$sname] = $gs;
            }
        }
        return $goodsStyles;
    }


    protected function createStyle($goods, $sname, $svalue, $extra){
        if(!$svalue){
            return null;
        }
        //$styleName = getStyleName($svalue, $current_language_id);
        switch ($sname) {
        case 'sash_size':
            return new SashStyle($goods, $sname, $svalue);
        case 'embroidery_color':
            return new EmbroideryColorStyle($goods, $sname, $svalue);
        case 'bodice_color':
            return new BodiceColorStyle($goods, $sname, $svalue);
        case 'skirt_color':
            return new SkirtColorStyle($goods, $sname, $svalue);
        case 'color':
            return new ColorStyle($goods, $sname, $svalue);
        case 'size': //todo : verify the name and the kname, add fee
            return new SizeStyle($goods, $sname, $svalue, $extra);
        case 'wrap':
            return new WrapStyle($goods, $sname, $svalue);
        }
    }
}
