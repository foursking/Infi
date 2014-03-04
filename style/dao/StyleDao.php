<?php
namespace lestore_style\app\dao;

use lestore_base\app\service\G11N;
use lestore_base\app\dao\BaseDao;
use lestore_style\app\model\Style;

class StyleDao extends BaseDao{
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

    public function getStyles($styleKeys){
        $res = array();
        $selector = $this->selector($styleKeys);
        if(null == $selector){
            return $res;
        }

        $sql = "/* ProductDao.getStyles */SELECT s.style_id id, s.parent_id keyId,
                IFNULL(sl.name, s.name) AS name,
                IFNULL(sl.value, s.value) AS value,
                s.cat_id AS groupId 
            FROM ".parent::_T('style')." AS s
            LEFT JOIN ".parent::_T('style_languages')." AS sl ON sl.style_id = s.style_id
            WHERE sl.languages_id = ".G11N::langId('en')." AND
                  s.is_show = 1 AND
                  ( s.style_id $selector OR s.parent_id $selector )
            ORDER BY s.display_order";

        try{
            $pstmt = $this->db->prepare($sql);
            if($pstmt->execute(array_merge($styleKeys,$styleKeys))){
                return $pstmt->fetchAll(\PDO::FETCH_CLASS, "lestore_style\app\model\Style");
            }
        }catch(\PDOException $e){
            echo 'DB Error: ' . $e->getMessage();
        }
		return $res;
    }

 	public function getStylesNls($styleIds) {
        $res = array();
        $selector = $this->selector($styleIds);
        if(null == $selector){
            return $res;
        }

        $sql = "/* ProductDao.getStylesNls */SELECT s.style_id id, sl.languages_id langId,
                IFNULL(sl.name, s.name) AS name,
                IFNULL(sl.value, s.value) AS value
            FROM ".parent::_T('style')." AS s
            LEFT JOIN ".parent::_T('style_languages')." AS sl ON sl.style_id = s.style_id
            WHERE s.style_id $selector";

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

}
