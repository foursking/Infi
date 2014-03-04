<?php
namespace esmeralda\product\model\style;

class BodiceColorStyle extends CommonStyle{

    public function view($langId){
        $styleName = getStyleName($this->value, $langId);
        return $_LANG['page_common_bodice_color'] . "={$styleName['value']}";
    }
}
