<?php
namespace esmeralda\category\attribute;

use esmeralda\base\BaseDao;

class AttributeDao extends BaseDao{
    CONST filter_table = 'tmp_c_attr_goods_filter_62';
    CONST BITSET_BASE = 62;

    public function __construct($db, $projName){
        $this->projName = $projName;
        parent::__construct($db);
    }

    private function getFilterTable(){
        return self::filter_table.'_'.strtolower($this->projName);
        //switch($this->projName){
        //case 'JenJenHouse':
        //case 'DressFirst':
        //    return self::filter_table.'_'.strtolower($this->projName);
        //default:
        //    return self::filter_table;
        //}
    }

    public function getAttributes($catId,$langId,$isFilter,$callback){
        $sql = "SELECT c.cat_id,c.attr_name AS attr_name_en,c.attr_values AS attr_values_en, al.* ";
        if($isFilter){
            $sql .= ", 1 AS is_show, c.goods_nos, c.attr_pid AS parent_id FROM ".$this->getFilterTable()." c ";
        }else{
            $sql .= ", c.is_show as is_show, c.parent_id AS parent_id FROM attribute c ";
        }
        $sql .= " LEFT JOIN attribute_languages al ON al.attr_id = c.attr_id
            WHERE ";
        if($isFilter){
            $sql .= " c.project_name = '" . $this->projName . "' AND ";
        }else{
            $sql .= " c.parent_id > 0 AND ";
        }
        if(null != $catId){
            $sql .= " c.cat_id = :catId AND ";
        }
        $sql .= "al.languages_id = :langId 
            AND c.is_delete = 0
            ORDER BY c.attr_id";
        try{
            $pstmt = $this->db->prepare($sql);
            if(null != $catId){
                $pstmt->bindParam(':catId',$catId);
            }
            $pstmt->bindParam(':langId',$langId);
            if($pstmt->execute()){
                while($row = $pstmt->fetch()){
                    $callback($row);
                }
            }
        }catch(PDOException $e){
            echo 'DB Error ';// . $e->getMessage();
        }
    }
}


