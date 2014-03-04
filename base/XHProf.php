<?php

namespace esmeralda\base;

use \XHProfRuns_Default;

class XHProf{

    /*
     * XHPROF_FLAGS_NO_BUILTINS 不记录内置的函数
     * XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY 同时分析CPU和Mem的开销
     */
    public function __construct($params = 0, $baseurl = '/xhprof_html/index.php', $src = 'esmeralda'){
        $this->baseurl = $baseurl;
        $this->src = $src;
        xhprof_enable($params); 
    }

    public function done($echo = true){
        $xhprof_data = xhprof_disable();
        $xhprof_runs = new XHProfRuns_Default(); 
        $run_id = $xhprof_runs->save_run($xhprof_data, $this->src);

        $url = $this->baseurl."?run={$run_id}&source=".$this->src;
        if($echo){
            echo '<a href="'.$url.'" target="_blank">XHProf</a>';
        }
        return $url;
    }
}

