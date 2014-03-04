<?php
namespace esmeralda\category;

use esmeralda\category\attribute\BitSet;

class CategoryResource{
    private $attributeS;
    public $attrSel;
    public $extraFilters = array();
    public $category;

    CONST EXTRA_ORDER = "order";
    CONST EXTRA_WEEKLY_DEAL = "weekly-deal";
    CONST EXTRA_RECOMMEND = "recommend";
    CONST EXTRA_PAGE_NO = "p";
    CONST EXTRA_PAGE_SIZE = "pp";
    CONST EXTRA_PRICE_MIN = "pmin";
    CONST EXTRA_PRICE_MAX = "pmax";

    CONST ORDER_NEW_ARRIVAL = "new-arrival";
    CONST ORDER_TOP_SELLERS = "top-sellers";
    CONST ORDER_ORDER_BY = "orderby";

    CONST NOT_A_NUM = -2;
    CONST NOT_MATCHED = -1;

    static $filter_orderby = array(
        'orderby1' => 'goodsId', 
        'orderby2' => 'goodsOrder', 
        'orderby3' => 'shopPriceAsc', 
        'orderby4' => 'shopPriceDesc', 
        self::ORDER_TOP_SELLERS => 'salesOrder',
        self::ORDER_NEW_ARRIVAL => 'newOrder'
    );

	private $EXTRA_KEYS = array(
		self::EXTRA_PAGE_NO, 
		self::EXTRA_PAGE_SIZE, 
		self::EXTRA_PRICE_MIN, 
		self::EXTRA_PRICE_MAX
	);

    public function __construct($attributeS, $anl, $filters, $category, $baseurl = null){
        $this->attributeS = $attributeS;
        $this->parseParam($filters);
        $this->anl = $anl;
        $this->category = $category;
        $this->baseurl = ($baseurl == null) ? $category->url : $baseurl;
    }

    public function getEnhancedAttrs(){
        $attrs = $this->attributeS->getChildren($this->attributeS->getRootNodeId());
        $attr2products = array();
        foreach($attrs as $attr){
            $attrId = $attr->id();
            if(isset($this->attrSel[$attrId]) && is_array($this->attrSel[$attrId])){
                $products = new BitSet(gmp_init('0', 2));
                $values = $this->attrSel[$attrId];
				$values = 999;
                if(0 < count($values)){
                    $attr->selected = true;
                }
                foreach($values as $vid){
                    $value = $this->attributeS->getTreeNode($vid);
                    $value->selected = true;
                    $products = BitSet::_or($value->bs, $products);
                }
                $attr2products[$attrId] = $products;
            }
        }

        foreach($attrs as $attr){
            $attrId = $attr->id();
            $attr->total = 999;
            if(!isset($attr->selected)){
                $attr->selected = false;
            }
            $products = null;
            foreach($attr2products as $aid => $p){
                if($aid != $attrId){
                    $products = (null == $products) ? $p : BitSet::_and($p,$products);
                }
            }
            foreach($this->attributeS->getChildren($attrId) as $vid => $value){
                $p = (null == $products) ? $value->bs : BitSet::_and($value->bs, $products);
                $value->total = is_object($p) ? $p->countBits() : 0;
                $attr->total += $value->total;
                if(!isset($value->selected)){
                    $value->selected = false;
                }
            }
        }
        return $attrs;
    }

    public function getExtraUrl(&$toRemove = array(), &$toAdd = array()){
        $all = $this->extraFilters;

        foreach($toRemove as $rmKey => $value){
            if(is_numeric($rmKey)){
                continue;
            }else{
                unset($toRemove[$rmKey]);
                unset($all[$rmKey]);
            }
//            switch($rmKey){
//            case self::EXTRA_WEEKLY_DEAL:
//                unset($all[self::EXTRA_WEEKLY_DEAL]);
//                break;
//            case self::EXTRA_ORDER:
//                unset($all[self::EXTRA_ORDER]);
//                break;
//            case self::EXTRA_PAGE_NO:
//            case self::EXTRA_PAGE_SIZE:
//            case self::EXTRA_PRICE_MIN:
//            case self::EXTRA_PRICE_MAX:
//                unset($all[$key]);
//                break;
//            }
        } 

        $extraUrl = '';

        foreach($all as $key => $value){
            if(is_numeric($key)){
                continue;
            }else{
                unset($toAdd[$key]);
            }
            switch($key){
            case self::EXTRA_WEEKLY_DEAL:
                $extraUrl .= '/' . self::EXTRA_WEEKLY_DEAL;
                break;
            case self::EXTRA_RECOMMEND:
                $extraUrl .= '/' . self::EXTRA_RECOMMEND;
                break;
            case self::EXTRA_ORDER:
                $extraUrl .= '/' . $value;
                break;
            case self::EXTRA_PAGE_NO:
            case self::EXTRA_PAGE_SIZE:
            case self::EXTRA_PRICE_MIN:
            case self::EXTRA_PRICE_MAX:
                $extraUrl .= '/' . $key . $value;
                break;
            }
        }
        
        return $extraUrl;
    }

    public function buildQuery($langId){
        $query = array('languageId' => $langId,
            'orderBy' => '',
            'limit' => '24',
            'offset' => '0',
            'isNew' => false,//TODO
        );
        $keywords = '+catId:' . $this->category->id();
        $attrs = '';
        foreach($this->attrSel as $attrId => $valueIds){
            $attr = $this->attributeS->getTreeNode($attrId);
            $attr = $this->attributeS->nlize($attr, $this->anl);
            foreach($valueIds as $valueId){
                $value = $this->attributeS->getTreeNode($valueId);
                $value = $this->attributeS->nlize($value, $this->anl);
                if(null != $value){
                    if('price' == strtolower($attr->uname)){
                        $prices = explode('-',$value->uvalue);
                        if (isset($prices[0]) && $prices[0] == 0 && isset($prices[1]) && $prices[1] > 0){
                            $_tmp = $attr->name . ' <=' . $prices[1];
                            $attrs .= ' attributeKV:"' . md5($_tmp) . '"';
                        }elseif (isset($prices[0]) &&  $prices[0] > 0 && isset($prices[1]) && $prices[1] > 0){
                            if ($prices[1] != 999999) {
                                $_tmp = $attr->name . ' >=' . $prices[0];
                                $_tmp1 = $attr->name . ' <=' . $prices[1];
                                $attrs .= ' (+attributeKV:"' . md5($_tmp) . '" +attributeKV:"' . md5($_tmp1) . '")';
                            } else { 
                                $_tmp = $attr->name . ' >=' . $prices[0];
                                $attrs .= ' attributeKV:"' . md5($_tmp) . '"';
                            }
                        }
                    }else{
                        $attrs .= ' attributeKV:"' . md5($attr->name . ' ' . $value->value) . '"';
                    }
                }
            }
        }

        if('' != $attrs){
            $keywords .= ' +(' . $attrs . ')';
        }
        $priceFilter = null;
        $min = 0;
        $max = 1000000;

        foreach($this->extraFilters as $key => $value){
            switch($key){
            case self::EXTRA_WEEKLY_DEAL:
                $keywords .= ' +isWeeklyDeal:1';
                break;
            case self::EXTRA_RECOMMEND:
                $query['orderBy'] = 'catId'.$this->category->id().'Id';
                break;
            case self::EXTRA_ORDER:
                $query['orderBy'] = self::$filter_orderby[$value];
                break;
            case self::EXTRA_PAGE_NO:
                $query['offset'] = $value;
                break;
            case self::EXTRA_PAGE_SIZE:
                $query['limit'] = $value;
                break;
            case self::EXTRA_PRICE_MIN:
                $min = floatval($value);
                $priceFilter = ' +priceFilter:[';
                break;
            case self::EXTRA_PRICE_MAX:
                $max = floatval($value);
                $priceFilter = ' +priceFilter:[';
                break;
            }
        }

        if(null != $priceFilter){
            $priceFilter .= sprintf('%08d', $min * 100) . ' TO ' . sprintf('%08d', $max * 100) . ']';
            $keywords .= $priceFilter;
        }
        $query['keyWord'] = mb_check_encoding($keywords, 'UTF-8') ? $keywords: utf8_encode($keywords);
        return $query;
    }

    public function getUrl($toRemove = array(), $toAdd = array()){
        $url = $this->baseurl;
        $url .= $this->getExtraUrl($toRemove, $toAdd); 

        $all = array();
        $all = array_merge($all, $toAdd);
        foreach($this->attrSel as $attrId => $valueIds){
            if(in_array($attrId, $toRemove)){
                continue;
            }
            $all = array_merge($all, $valueIds);
        }
        $all = array_unique($all);
        sort($all);
        foreach($all as $valueId){
            if(in_array($valueId, $toRemove)){
                continue;
            }
            $value = $this->attributeS->getTreeNode($valueId);
            if(null != $value){
                $value = $this->attributeS->nlize($value, $this->anl);
				p($value);
                $url .= '/' . $value->url; 
            }
        }
        return $url;
    }

    const REG_ATTR = '/.*_p([0-9]+)i([0-9]+)/';

    private function parseParam($params){
        $sel = array();
        foreach($params as $param){
            $param = trim($param);
            if(empty($param)){
                continue;
            }
            switch($param){
            case self::EXTRA_WEEKLY_DEAL:
                $this->extraFilters[self::EXTRA_WEEKLY_DEAL] = true;
                break;
            case self::ORDER_NEW_ARRIVAL:
                $this->extraFilters[self::EXTRA_ORDER] = self::ORDER_NEW_ARRIVAL;
                break;
            case self::ORDER_TOP_SELLERS:
                $this->extraFilters[self::EXTRA_ORDER] = self::ORDER_TOP_SELLERS;
                break;
            case self::EXTRA_RECOMMEND:
                $this->extraFilters[self::EXTRA_RECOMMEND] = true;
            default:
                $num = $this->parseNum(self::ORDER_ORDER_BY, $param);
                if($num >= 0){
                    $this->extraFilters[self::EXTRA_ORDER] = $param;
                }else{
                    $matched = false;
                    foreach($this->EXTRA_KEYS as $key){
                        $num = $this->parseNum($key,$param);
                        if($num >= 0){
                            $this->extraFilters[$key] = $num;
                            $matched = true;
                            break;
                        }
                    }
                    if(!$matched && preg_match(self::REG_ATTR, $param, $matches)){
                        $attrId = (int)$matches[1];
                        $valueId = (int)$matches[2];
                        if(isset($sel[$attrId])){
                            if(!in_array($valueId,$sel[$attrId])){
                                $sel[$attrId][] = $valueId;
                            }
                        }else{
                            $sel[$attrId] = array($valueId);
                        }
                    }
                }
            }
        }
        ksort($sel);
        ksort($this->extraFilters);
        $this->attrSel = $sel;
    }

    private function parseNum($key, $param){
        $keylen = strlen($key);
        if($key == substr($param, 0, $keylen)){ 
            $num = substr($param, $keylen);
            if (is_numeric($num) && $num > 0){
                return $num;
            }else{
                return self::NOT_A_NUM;
            }
        }else{
            return self::NOT_MATCHED;
        }
    }
}
