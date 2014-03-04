<?php
namespace esmeralda\category;

use esmeralda\base\BaseDao;

class CategoryDao extends BaseDao{

    public function __construct($db, $projName){
        $this->projName = $projName;
        parent::__construct($db);
    }

	public function getCategories($langId){
		//@FIXME 如果有项目例外，那么此处需要修改
		$sql = "SELECT IFNULL(cdo.sort_order, c.sort_order) AS sort_order,
				c.cat_id, c.parent_id, c.config, c.is_accessory, IFNULL(cl.cat_name, c.cat_name) AS cat_name
				FROM ".parent::_T('category')." AS c
				LEFT JOIN ".parent::_T('category_display_order')." AS cdo
				    ON cdo.cat_id = c.cat_id AND cdo.is_display=1
				    AND cdo.location='SUB_MENU' AND cdo.project_name='Shop'
                LEFT JOIN ".parent::_T('category_languages')." As cl 
                    ON cl.cat_id = c.cat_id AND cl.languages_id = :langId 
			    WHERE c.is_show = 1
			    ORDER BY cdo.sort_order DESC , c.sort_order DESC";
        try{
            $pstmt = $this->db->prepare($sql);
            $pstmt->bindParam(':langId',$langId);
            if($pstmt->execute()){
                return $pstmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }catch(PDOException $e){
            echo 'DB Error ';// . $e->getMessage();
        }
		return array();
	}
}

