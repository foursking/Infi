<?php
namespace lestore_order\app\model;

use lestore_base\app\model\AttributesCopier;
use lestore_product\app\service\StyleFactory;

class Customization{
    use AttributesCopier;

    public $styleId;
    public $sku;
    public $skuId;
    public $input;

    //public $styles;
    //public $extra;

    public function __construct($goods){
        $this->copyAttributes($goods, true);
        //$styleFactory = new StyleFactory();
        //$this->styles = $styleFactory->createStyles($goods, $this->input);
        $this->parseStyles($this->input);
    }

    public function charge($bill, $goods, $exchanger){
        foreach($this->styles as $style){
            $style->charge($bill, $goods, $exchanger);
        }
    }

    protected function parseStyles($stylesJson){
        //$goodsStyles = array();
        $styleIds = array();
        $styles = json_decode($stylesJson, true);
        if (isset($styles['input']) && is_array($styles['input'])) {
            $this->extra = $styles['input'];
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
                if($svalue){
                    $styleIds[] = $svalue;
                }
                //$gs = $this->createStyle($goods, $sname, $svalue, $extra);
                //$goodsStyles[$sname] = $gs;
            }
        }
        //return $goodsStyles;
        global $container;
        $this->styles = $container['style']->getStyles($styleIds);
    }
}
