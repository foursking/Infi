<?php
namespace lestore_product_list\app\dao;

use lestore_base\app\dao\BaseDao;

class ProductListDao extends BaseDao{
	public function __construct($db){
		parent::__construct($db);
	}
	
	private function selector($ids){
		if(count($ids) > 1){
			return " in (" . substr(str_repeat("?,",count($ids)),0,-1) . ") ";
		}else if(count($ids) == 1){
			return "  = ? ";
		}else{
			return null;
		}
	}
	
	/**
	 * Get all products in specified category.
	 * @param int $catId
	 * @param string $projectName
	 */
	public function getCatProductList($catId, $projectName = 'JJsHouse'){
		$res = array();
		$selector = $this->selector($catId);
		if(null == $selector){
			return $res;
		}
		$sql = "SELECT gc.cat_id, group_concat(DISTINCT g.goods_id SEPARATOR ',') as goods_ids
            	FROM goods AS g 
	            	INNER JOIN goods_category AS gc ON g.goods_id = gc.goods_id
		            INNER JOIN goods_project AS gp ON gp.goods_id = g.goods_id 
						AND lower(gp.project_name) = '" . strtolower($projectName) . "'
	            WHERE g.is_on_sale = 1
		            AND g.is_display = 1
		            AND g.is_delete = 0
		            AND gp.goods_thumb != ''
		            AND gp.shop_price > 0
		            AND gc.cat_id $selector
				GROUP BY gc.cat_id";
		
		try{
			$pstmt = $this->db->prepare($sql);
			if($pstmt->execute(array($catId))){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
		}catch(\PDOException $e){
			echo 'DB Error: ' . $e->getMessage();
		}
		return $res;
	}
	
	public function getProductIds($projectName, $offset = '0', $size = '*', $onSale = true) {
		$res = array();
		if($size === "*" || $offset === "*")
			$offsetInfo = "";
		else
			$offsetInfo = "LIMIT $size OFFSET $offset";
		
		$sql = "SELECT lower(gp.project_name) as projectName,
				gp.goods_id as productId
				FROM ".parent::_T('goods')." AS g
				LEFT JOIN ".parent::_T('goods_project')." AS gp ON gp.goods_id = g.goods_id
				where gp.project_name = ?
					AND g.is_on_sale = ?
    				AND g.is_display = 1
    				AND g.is_delete = 0
    				AND gp.goods_thumb != ''
    				AND gp.shop_price > 0
				ORDER BY productId
				$offsetInfo";
		try{
			$pstmt = $this->db->prepare($sql);
			if($pstmt->execute(array_merge(array(strtolower($projectName)), array($onSale?1:0)))){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
		}catch(\PDOException $e){
			echo 'DB Error: ' . $e->getMessage();
		}
		return $res;
	}
	
	public function getAttributeIds() {
		$res = array();
		$sql = "SELECT a.attr_id
				FROM ".parent::_T('attribute')." a
						WHERE a.is_delete = 0
						AND a.is_show = 1
						GROUP BY a.attr_id";
	
		try{
			$pstmt = $this->db->prepare($sql);
			if($pstmt->execute()){
				return $pstmt->fetchAll(\PDO::FETCH_COLUMN);
			}
		}catch(\PDOException $e){
			echo 'DB Error: ' . $e->getMessage();
		}
		return $res;
	}
	
	public function getStyleIds() {
		$res = array();
		$sql = "SELECT s.style_id
				FROM ".parent::_T('style')." s
						WHERE s.is_show = 1
						GROUP BY s.style_id";
	
		try{
			$pstmt = $this->db->prepare($sql);
			if($pstmt->execute()){
				return $pstmt->fetchAll(\PDO::FETCH_COLUMN);
			}
		}catch(\PDOException $e){
			echo 'DB Error: ' . $e->getMessage();
		}
		return $res;
	}
	
// 	public function hasNewProduct($categoryId){
// 		$sql = "SELECT g.goods_id
//             	FROM goods AS g
//             	INNER JOIN goods_category AS gc ON g.goods_id = gc.goods_id
//             INNER JOIN goods_project AS gp ON gp.goods_id = g.goods_id
//             AND gp.project_name = '" . PROJECT_NAME_LOCAL . "'
//             WHERE g.is_new = 1
//             AND g.is_on_sale = 1
//             AND g.is_display = 1
//             AND g.is_delete = 0
//             AND gp.goods_thumb != ''
//             AND gp.shop_price > 0
//             AND (gc.cat_id = :cat_id || gc.cat_pid = :cat_id)
//             limit 1";
	
// 		try{
// 			$pstmt = $this->db->prepare($sql);
// 			$pstmt->bindParam(':cat_id',$categoryId);
// 			if($pstmt->execute()){
// 				return $pstmt->rowCount() > 0;
// 			}
// 		}catch(PDOException $e){
// 			echo 'DB Error: ' . $e->getMessage();
// 		}
// 		return false;
// 	}
	
}
