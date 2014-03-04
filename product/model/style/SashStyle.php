<?php
namespace esmeralda\product\model\style;

class SashStyle extends CommonStyle{

    public function view($langId){
        return $_LANG['page_common_sash_size'] . "={$this->value}" . $_LANG['page_common_length_inch'];
    }
}
