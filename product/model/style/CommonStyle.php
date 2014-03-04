<?php
namespace esmeralda\product\model\style;

class CommonStyle{
    protected $goods;
    protected $name;
    protected $value;
    protected $extra;

    public function __construct($goods, $name, $value, $extra = null){
        $this->goods = $goods;
        $this->name = $name;
        $this->value = $value;
    }

    protected function getStyleName($langId){
        return getStyleName($this->value, $langId);
    } 

    public function view($langId){
        $styleName = $this->getStyleName($langId);
        if($styleName){
            return "{$styleName['name']}={$styleName['value']}";
        }else{
            return $_LANG['page_common_' . $name] . "={$value}".$_LANG['page_common_length_inch'];
        }
    }

    public function charge($exchanger){
        return $exchanger(0);
    }
}


