<?php
namespace esmeralda\category\attribute;
use esmeralda\base\Node;
use esmeralda\base\G11N;

class DbAttributeService extends AbstractAttributeService{
    private $categoryId;
    private $isFilter;
    protected $dao;

    public function __construct($dao, $categoryId, $isFilter){
        $this->dao= $dao;
        $this->categoryId = $categoryId;
        $this->isFilter = $isFilter;
        parent::__construct(); 
    }

	protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $childRelation[$this->root_node_id] = array();

        $prices = array();

	    $langId = G11N::langId('en');
        //backward compatibility for PHP 5.3
        $self = $this;
        $this->dao->getAttributes($this->categoryId,$langId,$this->isFilter,function($row) 
            use(&$map, &$childRelation, &$parentRelation, &$prices, $self){
            $attributeId = $row['parent_id'];
            if(!isset($map[$attributeId])){
                $node = new Node($attributeId);
                $node->uname = $row['attr_name_en'];
                $map[$attributeId] = $node;
                $childRelation[$self->getRootNodeId()][] = $attributeId;
                $parentRelation[$attributeId][] = $self->getRootNodeId();
            }

            $valueId = $row['attr_id'];

            $attrItem = new Node($valueId);
            $attrItem->uvalue = $row['attr_values_en'];
            if(isset($row['goods_nos'])){
                $attrItem->bs = new BitSet(gmp_init($row['goods_nos'], AttributeDao::BITSET_BASE));
            }

            if(strtolower($row['attr_name_en']) == 'price'){
                $attrItem->value= $row['attr_values'];
             if(!isset($prices[$attributeId])){
                    $prices[$attributeId] = array();
                }
                $prices[$attributeId][$valueId] = $attrItem;
            }else{
             if(isset($childRelation[$attributeId])){
                    $childRelation[$attributeId][] = $valueId;
                }else{
                    $childRelation[$attributeId] = array($valueId);
                }
                $parentRelation[$valueId] = array($attributeId);
                $map[$valueId] = $attrItem;
            }
        });

        $this->formatPrices($prices, $map, $childRelation, $parentRelation);
        $id = 'DB';
    }

    private function formatPrices(&$prices, &$map, &$childRelation, &$parentRelation){
        $formatted_price_avs = array();
        $price_attr_id;

        foreach($prices as $priceAttrId => $priceItems){
            $prices = array();
            foreach($priceItems as $item){
                $prices[] = substr($item->value, 2);
            } 
            $this->_map_price($prices, $priceB2T, $priceT2B);


            $priceAttrValues = array();

            foreach($priceItems as $item){
                $_op = substr($item->value, 0, 2); 
                $_price = substr($item->value, 2);
                $pBottom = ($_op == '<=') ? $priceT2B[$_price] : $_price;

                if(isset($priceAttrValues[$pBottom])){
                    $pAttrV = &$priceAttrValues[$pBottom];
                    if(isset($pAttrV->bs) && isset($item->bs)){
                        $pAttrV->bs = BitSet::_and($pAttrV->bs, $item->bs);
                    }
                    //always use the id of '>=' item, except 0-x
                    if($_op == '>='){
                        $pAttrV->id = $item->id;
                    }
                }else{
                    unset($item->value);// = $pBottom . '-' . $priceB2T[$pBottom];
                    $priceAttrValues[$pBottom] = $item;
                }
            }



            foreach($priceAttrValues as $price => $pav){
                if(isset($childRelation[$priceAttrId])){
                    $childRelation[$priceAttrId][] = $pav->id();
                }else{
                    $childRelation[$priceAttrId] = array($pav->id());
                }
                $parentRelation[$pav->id()] = array($priceAttrId);
                $pav->uvalue = $price . '-' . $priceB2T[$price];
                $map[$pav->id()] = $pav;
            }
        }
    }

    public function getNl($language){
        $nl = array();
        $prices = array();
        $priceName = '';
	    $langId = G11N::langId($language);
        $this->dao->getAttributes($this->categoryId, $langId, $this->isFilter, function($row) use (&$prices, &$nl, &$priceName){
            $id = $row['attr_id'];
            $pid = $row['parent_id'];
            $value = $row['attr_values'];
            $name = $row['attr_name'];
            if(!isset($nl[$pid])){
                $nl[$pid] = array('name' => $name);
            }
            if(strtolower($row['attr_name_en']) == 'price'){
                if(!isset($prices[$pid])){
                    $prices[$pid] = array();
                }
                $prices[$pid][$id] = $value;
                $priceName = $name;
            }else{
                $nl[$id] = array('value' => $value);
            }
        }); 

        foreach($prices as $pid => $values){
            $priceNl = $this->formatPriceNl($values);
            foreach($priceNl as $id => $value){
                $nl[$id] = array('value' => $priceName.$value);
            }
        }
        return $nl;
    }

    private function _map_price(&$prices, &$b2t, &$t2b){
        $prices = array_unique($prices);
        sort($prices);
        $pricesTop = $prices;
        $pricesTop[] = '999999';
        $pricesBottom = $prices;
        array_unshift($pricesBottom, '0');
        $b2t= array_combine($pricesBottom, $pricesTop);
        $t2b= array_combine($pricesTop, $pricesBottom);
    }
    private function formatPriceNl($values){
        $prices = array();
        foreach($values as $value){
            $prices[] = substr($value, 2);
        } 
        $this->_map_price($prices, $priceB2T, $priceT2B);

        $priceAttrValues = array();
        foreach($values as $id => $value){
            $_op = substr($value, 0, 2); 
            $_price = substr($value, 2);
            if($_op == '>='){
                $pBottom = $_price;
                $pTop = $priceB2T[$pBottom];
                $priceAttrValues[$id] =  $pBottom;
                if($pTop != '999999'){
                    $priceAttrValues[$id] .= '-' . $pTop;
                }
            }else if($_price == $priceB2T['0']){
                $priceAttrValues[$id] = '0';
                $pTop = $_price;
                if($pTop != '999999'){
                    $priceAttrValues[$id] .= '-' . $pTop;
                }
            }
        }
        return $priceAttrValues;
    }
}
