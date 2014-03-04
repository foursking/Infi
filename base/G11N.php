<?php
namespace esmeralda\base;

class G11N{
    public static function langId($langCode){
        return G11N::$langCode2Id[$langCode];
    }

    public static function langCode($langId){
        return G11N::$langId2Code[$langId];
    }

    private static $langCode2Id = array(
        'en'=>1,
        'de'=>2,
        'es'=>3,
        'fr'=>4,
        'se'=>5,
        'no'=>6,
        'it'=>7,
        'pt'=>8,
        'da'=>9,
        'fi'=>10,
        'ru'=>11,
        'nl'=>12,
        'ar'=>13,
        'be'=>14,
        'hr'=>15,
        'cs'=>16,
        'et'=>17,
        'el'=>18,
        'ht'=>19,
        'he'=>20,
        'hu'=>21,
        'is'=>22,
        'ga'=>23,
        'ja'=>24,
        'ko'=>25,
        'lt'=>26,
        'ms'=>27,
        'mt'=>28,
        'pl'=>29,
        'sk'=>30,
        'sl'=>31,
        'tr'=>32,
    );

    public static $langId2Code = array(
        'en', 'de', 'es', 'fr', 'se', 'no', 'it', 'pt', 'da', 'fi', 'ru', 'nl', 'ar', 'be', 'hr', 'cs', 
        'et', 'el', 'ht', 'he', 'hu', 'is', 'ga', 'ja', 'ko', 'lt', 'ms', 'mt', 'pl', 'sk', 'sl', 'tr',
    );
}
