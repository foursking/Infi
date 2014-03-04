<?php
namespace esmeralda\category\attribute;
//backward compatibility for PHP 5.3
//use JsonSerializable;

class BitSet {//implements JsonSerializable{
    private $gmp;
    const BASE = 62;

    public function __construct($gmp = null){
        if($gmp == null){
            $this->gmp = gmp_init(0);
        }else{
            $this->gmp = $gmp;
        }
    }

    public function jsonSerialize(){
        return gmp_strval($this->gmp, self::BASE);
    }

    public function parse($str){
        $this->gmp = gmp_init($str, self::BASE);
    }

    public function countBits(){
        $str = gmp_strval($this->gmp, 2);
        $str = str_replace('0','',$str);
        return strlen($str);
    }

    public function _countBits(){
        $zero = gmp_init('0');
        $one= gmp_init('1');

        $gmp = $this->gmp;
        $count = 0;
        while(0 != gmp_cmp($gmp,$zero)){
            $gmp_1 = gmp_sub($gmp, $one);
            $gmp = gmp_and($gmp, $gmp_1);
            ++$count;
        }
        return $count;
    }

    public static function _or($bs1, $bs2){
        $bs = new BitSet(null);
        $bs->gmp = gmp_or($bs1->gmp, $bs2->gmp);
        return $bs;
    }

    public static function _and($bs1, $bs2){
        $bs = new BitSet(gmp_and($bs1->gmp, $bs2->gmp));
        return $bs;
    }
}
