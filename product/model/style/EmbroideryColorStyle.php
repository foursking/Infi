<?php
namespace esmeralda\product\model\style;

class EmbroideryColorStyle extends CommonStyle{

    public function view($langId){
        $styleName = getStyleName($this->value, $langId);
        return $_LANG['page_common_embroidery_color'] . "={$styleName['value']}";
    }
}
