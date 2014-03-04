<?php
namespace lestore_product_list\app\service;

class SearchProductListService implements ProductListService{
    private $url;

    public function __construct($url){
        $this->url= $url;
    }

    public function getProducts($query){
        $searchGoods = $this->doSearch($query);
        var_dump($searchGoods['total']);
        var_dump($searchGoods['goodsIdList']);
        $total = $searchGoods['total'];
        // 分页
        #$pager_string = pager_rewrite($total, $pagesize, null, null, 5, 'p', 'pp');
        #if ($p > ceil($total / $pagesize))
        #{ // pagenumber超出，则显示最后一页
        #    $offset = (ceil($total / $pagesize) - 1) * $pagesize;
        #}

        if ($total <= 0) {
            return array();
        } else {
            //$goods_list = filter_goods_by_goodsIds($searchGoods['goodsIdList']);
            return $searchGoods['goodsIdList'];
        }
    }


    protected function doSearch($query){
        var_dump($query);
        $searchUrl = $this->url . 'search&json=' . urlencode(json_encode($query));
        //$searchUrl = str_replace('domain=' . PROJECT_NAME, 'domain=' . PROJECT_NAME_LOCAL, $this->url);

        var_dump($searchUrl);

        $apiResult = $this->curl_get($searchUrl, $errorString, 5);

        $searchGoods = array();
        if ($apiResult === FALSE) {
            // 时间 错误类型 返回值 查询字符串 ip 项目 agent
            //$logContent = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\n",
                //date("Y-m-d H:i:s"), 'Get API Error', $errorString, $_SERVER["QUERY_STRING"], real_ip(), PROJECT_NAME, $_SERVER['HTTP_USER_AGENT']);
            //searchLog($logContent);
        } else {
            $searchGoods = json_decode($apiResult, true);
            if(!isset($searchGoods['total']))
            {
                $searchGoods = array(
                    'total' => 0,
                    'goodsIdList' => array()
                );
            }
            $searchCount = !empty($searchGoods['total']) && is_numeric($searchGoods['total']) ? intval($searchGoods['total']) : 0;
            if ($searchCount == 0) {
                //$logContent = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\n",
                    //date("Y-m-d H:i:s"), 'Result Empty', $apiResult, $_SERVER["QUERY_STRING"], real_ip(), PROJECT_NAME, $_SERVER['HTTP_USER_AGENT']);
                //searchLog($logContent);
            }
        }

        return $searchGoods;
    }

    private function curl_get($url, &$error='', $timeout = 30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch) ;
        if($result === false){
            $error = curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
}
