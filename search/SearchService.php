<?php
namespace esmeralda\search;

class SearchService{
    private $url;

    public function __construct($endpoint, $domain){
        $this->url = "http://$endpoint/SearchServlet?proj=jjshouse&domain=$domain&action=";
    }

    public function search($query){
        $searchUrl = $this->url. 'search&json=' . urlencode(json_encode($query));
        $apiResult = $this->curl_get($searchUrl, $errorString, 5);

        if ($apiResult === FALSE) {
            //TODO error
            return false;
        } else {
            $searchGoods = json_decode($apiResult, true);
            if(isset($searchGoods['total'])){
                return $searchGoods;
            }
        }
        return array(
            'total' => 0,
            'goodsIdList' => array()
        );
    }

    public function count($query){
        $rs = $this->search($query);
		$count = !empty($rs['total']) && is_numeric($rs['total']) ? intval($rs['total']) : 0;
        return $count;
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
