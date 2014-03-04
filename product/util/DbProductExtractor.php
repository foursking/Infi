<?php
namespace esmeralda\product\util;

define('IN_ECS', true);
require_once __DIR__ . '/../../../esmeralda\init.php';
#require_once __DIR__ . '/../../test/helper.php';

use PHPUnit_Framework_TestCase;
use esmeralda\product\service\JsonProductService;
use esmeralda\product\dao\ProductDao;

#$db = new \cls_mysql($db_host, $db_user, $db_pass, $db_name);
#$db->query("SET time_zone = '$dbtimezone'");
#$db->set_disable_cache_tables(array(
#    $ecs->table('sessions'), 
#    $ecs->table('cart')
#)); 
#$GLOBALS['db'] = $db;
#
$db_host = "192.168.1.50:3306";
$db_name = "jjshouse";
$db_user = "dbuser0114";
$db_pass = "dbpswd0114";

// $pdo = new \PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$prefix = "";
$ecs = new ECS($db_name, $prefix);
$pdo = new ECMysql($db_host, $db_user, $db_pass, $db_name);
$dao = new ProductDao($pdo);

$productS = new JsonProductService();
$productS->initDAO($dao);

file_put_contents($argv[1], $productS->base2Json());
file_put_contents($argv[2], $productS->detail2Json());

$languages = array('de','es','fr','se','no','da','fi','ru','nl','it','pt','en');
foreach($languages as $lang){
    file_put_contents($argv[1] . '.nl.' . $lang, $productS->base2NlJson($lang));
    file_put_contents($argv[2] . '.nl.' . $lang, $productS->detail2NlJson($lang));
}


