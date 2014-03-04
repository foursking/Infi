<?php

namespace esmeralda\currency;

use esmeralda\base\BaseDao;

class CurrencyDao extends BaseDao{

    public function __construct($db){
        parent::__construct($db);
    }

    public function getCurrencies(){
        $sql = '/* CurrencyDao.getCurrencies */SELECT c.currency_id id, c.currency name, c.currency_symbol symbol, 
            c.currency_local_symbol localSymbol, (cr.exchange / cr.base) rate, c.disabled disabled
                FROM currency c
                LEFT JOIN currency_rate cr on cr.currency_id = c.currency_id
                WHERE cr.base_id = 1
                ORDER BY c.display_order';
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute()){
                return $pstmt->fetchAll(\PDO::FETCH_CLASS, "esmeralda\currency\Currency");
            }
        }catch(PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return null;
    }
}
