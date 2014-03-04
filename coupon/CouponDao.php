<?php

namespace lestore_coupon\app\dao;

use lestore_base\app\dao\BaseDao;

class CouponDao extends BaseDao{

    public function __construct($db){
        parent::__construct($db);
    }

    public function getCoupon($couponCode){
        $sql = "/* CouponDao.getCoupon */SELECT 
                c.coupon_code code, cc.coupon_config_coupon_type type, cc.coupon_config_apply_type target,
                c.can_use_times canUseTimes, c.used_timestamp usedTimestamp, c.used_times usedTimes,
                c.user_id userId, cc.coupon_config_minimum_amount minimum, 
                cc.coupon_config_value value, 
                cc.goods_id goodsId, cc.cat_id catId,
                cc.coupon_config_stime stime, cc.coupon_config_etime etime
        		FROM ".parent::_T('ok_coupon')." c 
        		LEFT JOIN ".parent::_T('ok_coupon_config')." cc ON c.coupon_config_id = cc.coupon_config_id 
        		WHERE c.coupon_code = :couponCode 
        		LIMIT 1 ";
        try{
            $pstmt = $this->db->prepare($sql);
            $pstmt->bindParam(':couponCode',$couponCode);
            if($pstmt->execute()){
                return $pstmt->fetchObject("lestore_coupon\app\model\Coupon");
            }
        }catch(PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return null;
    }
}
