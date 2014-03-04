<?php

namespace lestore_order\app\dao;

use lestore_base\app\dao\BaseDao;

class OrderDao extends BaseDao{

    public function __construct($db){
        parent::__construct($db);
    }

    public function getOrder($orderSn, $userId = null){
        $sql = "/*OrderDao.getOrder*/SELECT 
            oi.party_id partyId,
            -- order
            oi.order_amount, oi.important_day importantDay,
            oi.order_sn orderSn, oi.order_id orderId, oi.order_time orderTime, oi.order_status orderStatus,
            -- goods
            oi.goods_amount, oi.goods_amount_exchange, 
            -- shipment
            oi.sm_id smId, oi.shipping_id shippingId, oi.shipping_status shippingStatus, 
            oi.country, oi.province, oi.city,
            -- b.region_name countryName, c.region_name provinceName, d.region_name cityName, 
            -- b.region_code countryCode, c.region_code provinceCode, d.region_code cityCode, 
            oi.shipping_fee shippingFee, oi.shipping_fee_exchange shippingFeeExchange,
            -- payment
            oi.payment_id paymentId, oi.pay_status payStatus,
            -- user
            oi.user_id userId, oi.gender, oi.consignee, oi.email,
            -- coupon
            oi.bonus, oi.bonus_exchange bonusExchange, oi.coupon_code couponCode,
            -- currency
            oi.rate, oi.display_currency_rate displayRate,
            e.currency, e.currency_local_symbol AS currencyLocalSymbol, 
            IFNULL(e1.currency, e.currency) AS display, 
            IFNULL(e1.currency_local_symbol, e.currency_local_symbol) AS displayLocalSymbol, 
            IFNULL(e1.currency_symbol, e.currency_symbol) AS displaySymbol 
            FROM ".parent::_T('order_info')." oi 
            LEFT JOIN ".parent::_T('region')." b ON oi.country = b.region_id
            LEFT JOIN ".parent::_T('region')." c ON oi.province = c.region_id
            LEFT JOIN ".parent::_T('region')." d ON oi.city = d.region_id
            LEFT JOIN ".parent::_T('currency')." AS e ON oi.order_currency_id = e.currency_id
            LEFT JOIN ".parent::_T('currency')." AS e1 ON oi.display_currency_id = e1.currency_id
            WHERE order_sn = :orderSn ";
        if(null != $userId){
            $sql .= ' AND oi.user_id = :userId ';
        }
        $sql .= ' LIMIT 1 ';

        try{
            $pstmt = $this->db->prepare($sql);
            $pstmt->bindParam(':orderSn',$orderSn);
            if(null != $userId){
                $pstmt->bindParam(':userId',$userId);
            }
            if($pstmt->execute()){
                return $pstmt->fetchObject("lestore_order\app\model\Order");
            }
        }catch(PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return null;
    }

    public function getGoods($orderSn, $userId = null){
        $sql = "/*OrderDao.getGoods*/SELECT og.rec_id id, oi.user_id userId, og.goods_id productId, 
            og.goods_number number, og.goods_sn productSn,
            og.goods_attr, og.is_real isReal, og.extension_code weeklyDeal, 
            /*og.parent_id, ????*/
            og.is_gift isGift, og.style_id styleId, og.styles input, og.sku sku, og.sku_id skuId,
            og.shop_price AS totalShopPrice, og.goods_price_original AS shopPrice,
            g.cat_id catId, g.goods_weight weight, g.wrap_price wrapPrice
            FROM order_info oi
            INNER JOIN order_goods og ON og.order_id = oi.order_id
            INNER JOIN goods g ON og.goods_id = g.goods_id
            WHERE oi.order_sn = :orderSn";

        if(null != $userId){
            $sql .= ' AND oi.user_id = :userId ';
        }

        try{
            $pstmt = $this->db->prepare($sql);
            $pstmt->bindParam(':orderSn',$orderSn);
            if(null != $userId){
                $pstmt->bindParam(':userId',$userId);
            }
            if($pstmt->execute()){
                return $pstmt->fetchAll(\PDO::FETCH_CLASS, "lestore_order\app\model\Goods");
            }
        }catch(PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return null;
    }

    public function saveOrder($order){
        $goods = $order->goods;
        unset($order->goods);
        $a_order = self::bruiser($order);
        $a_goods = self::bruiser($goods);
        try{
            $this->notorm->transaction = "BEGIN";
            $insertedOrder = $this->notorm->order_info()->insert($a_order);
            $insertedGoods = $this->notorm->order_goods()->insert_multi($a_goods);
            $insertedOrder->goods = $insertedGoods;
            $this->notorm->transaction = "COMMIT";
            return $insertedOrder;
        }catch(PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
            $this->notorm->transaction = "ROLLBACK";
        }
        return null;
    }
}

