<?php
namespace esmeralda\base;

abstract class BaseDao{
    public $db;

    protected static function _T($table){
        return $table;
    }

    public function __construct($db){
        $this->db = $db;
    }

    public function getNl($nlId, $language){
	    $sql = "SELECT * FROM " . self::_T('multilanguage') . " WHERE code = :nlId";
        try{
            $pstmt = $this->db->prepare($sql);
            $pstmt->bindParam(':nlId',$nlId);
            if($pstmt->execute()){
                if($row = $pstmt->fetch()){
                    return $row[$language];
                }
            }
            return '';
        }catch(PDOException $e){
            echo 'DB Error ';// . $e->getMessage();
        }
    }

    public static function bruiser($object){
        $flattened = array();
        if(is_array($object) || is_object($object)){
            foreach($object as $key => $value){
                if($value == NULL){
                    continue;
                }
                if(is_object($value)){
                    $flattened = array_merge($flattened, self::bruiser($value));
                }else if(is_array($value)){
                    $flattened = array_merge($flattened, $value);
                }else{
                    $flattened[$key] = $value;
                }
            }
        }
        return $flattened;
    }
}
