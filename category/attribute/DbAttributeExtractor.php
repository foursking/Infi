<?php
namespace lestore_attribute\app\service;

require_once __DIR__ . '/../../../lestore_init.php';
require_once __DIR__ . '/../../../lestore_common.php';
//require_once __DIR__ . '/../../test/helper.php';

use lestore_category\app\service\ManualCategoryService;
use lestore_attribute\app\service\DbAttributeServiceService;
use lestore_category\app\dao\CategoryDao;
use lestore_base\app\service\G11N;

//$db = new \cls_mysql($db_host, $db_user, $db_pass, $db_name);
//$db->query("SET time_zone = '$dbtimezone'");
//$db->set_disable_cache_tables(array(
//    $ecs->table('sessions'), 
//    $ecs->table('cart')
//)); 
//$GLOBALS['db'] = $db;
$db_host = "192.168.1.50";
$db_name = "jjshouse";
$db_user = "dbuser0114";
$db_pass = "dbpswd0114";
$pdo = new \PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$dao = new CategoryDao($pdo);

$categoryS = new ManualCategoryService($dao);

foreach($categoryS->getAllNodes() as $cid => $category){
    $attributeS = new DbAttributeService($container['db'], $cid, true);
    if(0 != count($attributeS->getAllNodes())){
        file_put_contents($argv[1] . '.filter.' . $cid, $attributeS->toJson());
    }
}


foreach($categoryS->getAllNodes() as $cid => $category){
    $attributeS = new DbAttributeService($container['db'], $cid, false);
    if(0 != count($attributeS->getAllNodes())){
        file_put_contents($argv[1] . '.cate.' . $cid, $attributeS->toJson());
    }
}
foreach(G11N::$langId2Code as $lang){
    file_put_contents($argv[1] . '.nl.' . $lang, $attributeS->toNlJson($lang));
}



