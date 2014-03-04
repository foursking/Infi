<?php
namespace esmeralda\product\model\style;

class WrapStyle extends CommonStyle{

    public function view($langId){
        $styleName = getStyleName($this->value, $langId);
		$view = "{$styleName['name']}={$styleName['value']}";
        $view .= " (" . exchangeRate($v['wrap_price'], null, true) . ")";
        return $veiw;
    }

    public function charge($exchanger){
        return $exchanger($this->goods->wrap_price);
    }
}


