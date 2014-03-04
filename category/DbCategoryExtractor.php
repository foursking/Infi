<?php
namespace lestore_category\app\service;

require_once __DIR__ . '/../../../lestore_init.php';
require_once __DIR__ . '/../../../lestore_common.php';
#require_once __DIR__ . '/../../test/helper.php';

use lestore_category\app\service\ManualCategoryService;
use lestore_category\app\dao\CategoryDao;
use lestore_base\app\service\G11N;

$dao = new CategoryDao($container['db']);
$categoryS = new ManualCategoryService($dao);

file_put_contents($argv[1], $categoryS->toJson());

foreach(G11n::$langId2Code as $lang){
    file_put_contents($argv[1] . '.nl.' . $lang, $categoryS->toNlJson($lang));
}


