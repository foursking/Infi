<?php
namespace esmeralda_product\dao;

use esmeralda\base\BaseDao;
use esmeralda\product\model\Product;

class ProductDao extends BaseDao{
	public function __construct($db){
		parent::__construct($db);
	}

    private function idsSelector($ids){
		if(count($ids) > 1){
			return " in (" . substr(str_repeat("?,",count($ids)),0,-1) . ") ";
		}else if(count($ids) == 1){
            return "  = ? ";
        }else{
            return null;
        }
    }

    /**
     * get product base info
     * @param unknown_type $ids
     * @param unknown_type $projectName
     * @param unknown_type $onSale
     * @return multitype:
     */
    public function getProducts($ids, $projectName, $onSale = true){
    	$projectName = strtolower($projectName);
    	$res = array();
    	$idsSelector = $this->idsSelector($ids);
    	if(null == $idsSelector){
    		return $res;
    	}
    	$sql = "/* lib_goods.php NULL get_goods_info 2 SELECT */SELECT
            	g.goods_id id, 
    			g.goods_sn sn, 
    			ifnull(gp.goods_thumb, '') thumb,
            	ifnull(gp.shop_price, 0) price, 
    			ifnull(g.market_price, 0) marketPrice,
            	ifnull(gic.color_number,0) colorNo, 
    			ifnull(gsp.comment_count, 0) commentsNo, 
    			ifnull(gsp.comment_avg_rating, 0) rating,
				g.is_on_sale onSale, g.sku sku,
            	ifnull(g.is_new, 0) isNew, 
    			ifnull(g.wrap_price, 0) wrapPrice, 
    			ifnull(g.goods_weight, 0) weight,
            	ifnull(gsp.question_count,0) questionNo, 
    			ifnull(g.model_card,'') modelCard, 
    			ifnull(ge.ext_value,'') AS weeklyDeal,
            	GROUP_CONCAT(CAST(gc.cat_id AS CHAR) SEPARATOR ' ') AS catIds,
				g.add_time addTime, UNIX_TIMESTAMP(g.last_update_time) as updateTime,
				ifnull(go.sales_order, 0) + CASE true WHEN ISNULL(go.effective_cat_id)
					THEN ifnull(go.goods_order,0) + ifnull(go.virtual_sales_order,0)
					WHEN go.effective_cat_id = '%s' THEN ifnull(go.goods_order,0) + ifnull(go.virtual_sales_order,0)
					ELSE 0 END AS salesOrder,
				ifnull(go.goods_order, 0) AS goodsOrder
            FROM ".parent::_T('goods')." AS g
            INNER JOIN ".parent::_T('goods_category')." AS gc ON gc.goods_id = g.goods_id
            LEFT JOIN ".parent::_T('goods_project')." AS gp ON gp.goods_id = g.goods_id AND lower(gp.project_name) = ?
            LEFT JOIN ".parent::_T('goods_stat_project')." AS gsp ON gsp.goods_id = g.goods_id AND lower(gsp.project_name) = ?
            LEFT JOIN ".parent::_T('goods_info_cache')." gic ON gic.goods_id = g.goods_id
			LEFT JOIN ".parent::_T('goods_extension')." AS ge ON ge.goods_id = g.goods_id AND ge.ext_name = 'weekly_deal' AND ge.is_display = 1
			LEFT JOIN ".parent::_T('goods_display_order')." AS go ON go.goods_id = g.goods_id
    			WHERE g.is_on_sale = ?
    			AND g.is_display = 1
    			AND g.is_delete = 0
    			AND gp.goods_thumb != ''
    			AND gp.shop_price > 0
    			AND g.goods_id $idsSelector
    			GROUP BY g.goods_id";
    	
    	try{
    		$pstmt = $this->db->prepare($sql);
    		if($pstmt->execute(array_merge(array($projectName, $projectName, $onSale?1:0),$ids))){
    			return $pstmt->fetchAll(\PDO::FETCH_CLASS, "esmeralda\product\model\Product");
    		}
    	}catch(\PDOException $e){
    		echo 'DB Error: ' . $e->getMessage();
    	}
    	return $res;
    }
   
    /**
     * get product nls of specified language
     * @param unknown_type $ids
     * @param unknown_type $langId
     * @return multitype:
     */
	public function getProductsNls($ids, $langId){
        $res = array();
        $idsSelector = $this->idsSelector($ids);
        if(null == $idsSelector){
            return $res;
        }

        $sql = "/* ProductDao.getProductsNls */SELECT g.goods_id id, gl.languages_id langId,
        			ifnull(gl.goods_name, '') name, 
                    ifnull(gl.goods_url_name, '') urlName,
        			ifnull(gl.goods_details,'') details,
        			ifnull(gl.goods_desc,'') des, ifnull(gl.keywords,'') keywords
				FROM ".parent::_T('goods')." AS g
					LEFT JOIN ".parent::_T('goods_languages')." AS gl ON g.goods_id = gl.goods_id
				WHERE g.goods_id $idsSelector
					AND gl.languages_id = $langId
        		ORDER BY g.goods_id";
        
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($ids)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;
	}

	//TODO
 	public function getProductsNlsSEO($ids, $projectName){
 		$projectName = strtolower($projectName);
        $res = array();
        $idsSelector = $this->idsSelector($ids);
        if(null == $idsSelector){
            return $res;
        }

        $sql = "/* */SELECT goods_name name, goods_url_name urlName, goods_desc des, 
                            goods_details details, keywords
 				FROM ".parent::_T('goods_languages_project')."
 				WHERE lower(project_name) = ?
                    AND g.goods_id $idsSelector
        		ORDER BY g.goods_id";
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute(array_merge(array($projectName),$ids))){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;
 	}
	
 	/**
 	 * get product attributes by attributeIDs
 	 * @param unknown_type $ids
 	 * @return multitype:
 	 */
 	public function getAttributes($ids) {
 		$res = array();
 		$idsSelector = $this->idsSelector($ids);
 		if(null == $idsSelector){
 			return $res;
 		}
        $sql = "SELECT ga.goods_id, al.attr_name, a.parent_id attr_kid, 
                       group_concat(al.attr_values SEPARATOR ',') as attr_values,
                       group_concat(al.attr_id SEPARATOR ',') as attr_vids
 				FROM ".parent::_T('goods_attr')." ga,
                    ".parent::_T('attribute')." a 
                    LEFT JOIN ".parent::_T('attribute_languages')." al on a.attr_id = al.attr_id
 			 	WHERE a.is_delete = 0
                AND a.is_show = 1
                AND ga.attr_id = a.attr_id
                AND ga.is_show = 1
                AND ga.is_delete = 0
                AND al.languages_id = 1
                AND ga.goods_id $idsSelector
 				GROUP BY ga.goods_id, al.attr_name
                ORDER BY a.display_order DESC, a.parent_id";
        
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($ids)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;
 	}

 	/**
 	 * get attribute nls
 	 * @param unknown_type $attrIds
 	 * @param unknown_type $langId
 	 * @return multitype:
 	 */
 	public function getAttributesNls($attrIds, $langId) {
        $res = array();
        if($attrIds === '*'){
        	$idsSelector = '> -1';
        	$attrIds = array();
        }else
        	$idsSelector = $this->idsSelector($attrIds);
        if(null == $idsSelector){
            return $res;
        }

        $sql = "SELECT al.attr_id id, al.attr_name name, al.attr_values value, al.languages_id langId
 				FROM ".parent::_T('attribute')." a 
                    LEFT JOIN ".parent::_T('attribute_languages')." al on a.attr_id = al.attr_id
 			 	WHERE a.is_delete = 0
                AND a.is_show = 1
                AND al.languages_id = $langId
                AND a.attr_id $idsSelector
        		ORDER BY al.attr_id";
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($attrIds)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;
 	}


    //TODO
    public function getGallery($id, $projectName) {
    	$projectName = strtolower($projectName);
        $sql = "/* */SELECT goods_thumb,img_type 
            FROM goods_project 
            WHERE project_name = ?
            AND goods_id $idsSelector";
        
	$sql = "/* lib_goods.php NULL get_goods_gallery_by_type 6 SELECT */SELECT * 
				FROM {$ecs->table('goods_gallery')}
	            WHERE goods_id = $goods_id
	            	AND is_display = 1
	            	AND img_type = '$img_type'
					AND is_delete = 0 
					$condition
	            ORDER BY sequence ASC";
        
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($ids)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;

    }

    public function getRelatedProducts($id){
    }

    public function getProbabilityBuy($id){
    }
    
    /**
     * get product styles by styleIDs
     * @param unknown_type $ids
     * @return multitype:
     */
    public function getStyles($ids){
    	$res = array();
    	$idsSelector = $this->idsSelector($ids);
    	if(null == $idsSelector){
    		return $res;
    	}
        $sql = "/* ProductDao.getStyles */SELECT g.goods_id, s.style_id, s.parent_id, s.cat_id, s.display_order,
                s.name AS kname,
                s.value AS kvalue,
                IFNULL(sl.name, s.name) AS name,
                IFNULL(sl.value, s.value) AS value
            FROM ".parent::_T('style')." AS s
            LEFT JOIN ".parent::_T('style_languages')." AS sl ON sl.style_id = s.style_id
            LEFT JOIN ".parent::_T('goods')." AS g ON g.cat_id = s.cat_id
            WHERE s.is_show = 1
                AND languages_id = 1
            	AND g.goods_id $idsSelector
                ORDER BY s.display_order ASC, s.style_id ASC";
        
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($ids)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return array();
    }
	
    /**
     * get style nls
     * @param unknown_type $styleIds
     * @param unknown_type $langId
     * @return multitype:
     */
 	public function getStylesNls($styleIds, $langId) {
        $res = array();
        if($styleIds === '*'){
        	$idsSelector = '> -1';
        	$styleIds = array();
        }else
        	$idsSelector = $this->idsSelector($styleIds);
        if(null == $idsSelector){
            return $res;
        }

        $sql = "/* ProductDao.getStyles */SELECT s.style_id id, sl.languages_id langId,
                IFNULL(sl.name, s.name) AS name,
                IFNULL(sl.value, s.value) AS value
            FROM ".parent::_T('style')." AS s
            LEFT JOIN ".parent::_T('style_languages')." AS sl ON sl.style_id = s.style_id
            WHERE s.style_id $idsSelector
            	AND sl.languages_id = $langId
        	ORDER BY s.style_id";

        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($styleIds)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;
 	}
 	
 	/**
 	 * get tags by specified language
 	 * @param unknown_type $ids
 	 * @param unknown_type $langId
 	 * @return multitype:
 	 */
 	public function getTags($ids, $langId) {
 		$res = array();
 		$idsSelector = $this->idsSelector($ids);
 		if(null == $idsSelector){
 			return $res;
 		}
 		$sql = "SELECT g.goods_id, gt.language_id langId, 
 					group_concat(gt.tag SEPARATOR ',') as goods_tag
				FROM goods g, goods_tag gt
				WHERE g.goods_id = gt.goods_id AND g.goods_id $idsSelector
					AND g.is_on_sale <> 0
					AND g.is_delete = 0
					AND g.is_display = 1
					AND gt.is_delete = 0
					AND gt.is_display = 1
 					AND gt.language_id = $langId
 					group by gt.language_id, g.goods_id
 					ORDER BY g.goods_id";
 		try{
 			$pstmt = $this->db->prepare($sql);
 			if($pstmt->execute($ids)){
 				return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
 			}
 		}catch(\PDOException $e){
 			echo 'DB Error: ' . $e->getMessage();
 		}
 		return $res;
 	}
 	
 	/**
 	 * get recommendation of products
 	 * @param unknown_type $ids
 	 * @param unknown_type $projectName
 	 * @return multitype:
 	 */
 	public function getRecommendation($ids, $projectName = 'JJsHouse') {
 		$projectName = strtolower($projectName);
 		$res = array();
    	$idsSelector = $this->idsSelector($ids);
    	if(null == $idsSelector){
    		return $res;
    	}
        $sql = "select goods_id, cat_id, display_order from goods_display_order_recommendation 
        		WHERE goods_id $idsSelector
        			AND lower(project_name) = '$projectName'
        		ORDER BY goods_id";
        
        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute($ids)){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return array();
 	}

}
