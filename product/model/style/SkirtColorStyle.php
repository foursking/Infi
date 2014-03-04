<?php
namespace esmeralda\product\model\style;

class SkirtColorStyle extends CommonStyle{

    public function view($langId){
        $styleName = getStyleName($this->value, $langId);
		return $_LANG['page_common_skirt_color']  . "={$styleName['value']}";
    }
}
