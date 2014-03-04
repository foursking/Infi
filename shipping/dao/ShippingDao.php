<?php
namespace lestore_shipping\app\dao;

use lestore_base\app\dao\BaseDao;

class ShippingDao extends BaseDao{
	public function __construct($db){
		parent::__construct($db);
	}

    public function getShippingMethods(){
		$sql = "/* ShippingDao.getShippingMethods */SELECT sm.sm_id smId, sm.config 
            FROM ".parent::_T('shipping_method')." sm 
				WHERE sm.disabled = 0 ORDER BY sm.display_order DESC ";
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute()){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return array();
    }

    public function getShippingFee(){
        $sql = "/* ShippingDao.getShippingFee */SELECT sm_id, smf.* 
            FROM ".parent::_T('shipping_method_fee')." smf
            LEFT JOIN (SELECT sm_id, sm_area FROM ".parent::_T('shipping_method_area')." WHERE sma_id in 
                (SELECT min(sma_id) FROM ".parent::_T('shipping_method_area')." GROUP BY sm_id, sm_area)
            ) ia on smf.sm_area = ia.sm_area
            ";

        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute()){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return array();
    }

    public function getAreaMap($regionId = null){
        $sql = "/* ShippingDao.getShippingFee */SELECT region_id, sm_id, sm_area  
            FROM ".parent::_T('shipping_method_area')." smf
            ";
        if(null != $regionId){
            $sql .= " WHERE region_id = :regionId ";
        }

        try{
            $pstmt = $this->db->prepare($sql);
            if(null != $regionId){
                $pstmt->bindParam(":regionId", $regionId);
            }
            if($pstmt->execute()){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
        return array();
    }
}
