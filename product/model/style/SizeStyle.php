<?php
namespace esmeralda\product\model\style;

class SizeStyle extends CommonStyle{

    private $customization = array();

    public function __construct($goods,$name,$value,$extra){
        parent::__construct($goods,$name,$value,$extra);
        if(null != $extra){
            $this->parseCustomizedSize($extra);
        }
    }

    public function view($langId){
        $view = array();
        if($this->isCustomized()){
            //customized
            //TODO verify behavior
		    $view[] = runLangVar($_LANG['page_cart_compute_custom_fee']);
            foreach($this->customization as $customizeKey => $customizeValue){
                $view[] = $_LANG['page_common_' . $customizeKey] . 
                    "={$customizeValue}".$_LANG['page_common_length_inch'];
            }
        }else{
            //normal
            $styleName = getStyleName($this->value, $langId);
            $v = "{$styleName['name']}={$styleName['value']}";
            //plus size
            if ($this->isPlusSize()){
                $v .= "(" . exchangeRate($siteConf['plussize_fee'], null, true) . ")";
            }
            $view[] = $v;
            //size chart
            $sizeChart = SizeChart::getSizeChart($categoryId);
            if (isset($sizeChart[$this->value])) {
                foreach ($sizeChart[$this->value] as $sk => $sv) {
                    if ($sv > 0) {
                        $view[] = $_LANG['page_common_' . $sk] . "={$sv}".$_LANG['page_common_length_inch'];
                    }
                }
            }
        }
        return $view;    
    }

    private function isPlusSize(){
        global $container;
        $styles = $container['style']->getStyles(array($this->value));
        $value = $styles[$this->value]->value;
        return $value > 0 && preg_match('/^\d+W/', $value);
    }

    public function isCustomized(){
        return count($this->customization) != 0;
    }

    private function parseCustomizedSize($input){
        foreach ($input as $customizeKey => $customizeValue) {
            if ($customizeValue) {
                $this->customization[$customizeKey] = $customizeValue;
                $tmpx[] = $_LANG['page_common_' . $kk] . "={$vv}".$_LANG['page_common_length_inch'];
            }
        }
    }


    public function charge($exchanger){
        global $container;
        $siteConf = $container['siteConf'];
        if($this->isPlusSize()){
            return $exchanger($siteConf['plussize_fee']);
        }
        if($this->isCustomized()){
            if(!$this->goods->isAccessory() && $siteConf['custom_fee'] > 0){
                return $exchanger($siteConf['custom_fee']);
            }
        }
        return $exchanger(0);
    }
}


