<?php
namespace esmeralda\base;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
#use Monolog\Handler\FirePHPHandler;
use esmeralda\category\RawCacheCategoryFeeder;
use esmeralda\category\RawCategoryService;
use esmeralda\shipping\ShippingDao;
use esmeralda\shipping\ShippingService;
use esmeralda\coupon\CouponDao;
use esmeralda\coupon\CouponService;
use esmeralda\style\StyleDao;
use esmeralda\style\StyleService;
use esmeralda\currency\CurrencyDao;
use esmeralda\currency\CurrencyService;
use esmeralda\search\CacheSearchService;
use \PDO;

class Initializer{

    public static function tplPath(){
        return realpath(dirname(__DIR__).'/view');
    }

    public function initConf($container){
        include_once $container['APP_FS_ROOT'].'etc/env_config.php';
        $container['siteConf'] = $siteConf;
        return $container;
    }

    public function initBase($container){
        $container['log_handlers'] = $container->share(function($c){
            $siteConf = $c['siteConf'];
            if(!isset($siteConf['log_dir'])){
                $siteConf['log_dir'] = '/var/log/esmeralda';
            }
            if(!is_dir($siteConf['log_dir'])){
                @mkdir($siteConf['log_dir'], 0777, true);
            }
            return array(
                new StreamHandler(
                    $siteConf['log_dir'].'/esmeralda-'.date('Y-m-d').'.log', $siteConf['log_level']),
                //new FirePHPHandler(),
            );
        });

        $container['db'] = $container->share(function($c){
            $siteConf = $c['siteConf'];
            try {
                $dbh = new PDO("mysql:host={$siteConf['db_host']};dbname={$siteConf['db_name']}", 
                    $siteConf['db_user'], $siteConf['db_pass'],
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $dbh;
            } catch (PDOException $e) {
                $logger = LogFactory::get('esmeralda.init');
                $logger->error('Create database connection failed: ' . $e->getMessage());
                echo 'Sorry, our site is under maintenance. Please come back later!';
                die;
            }
        });

        $container['cache'] = $container->share(function($c){
            $siteConf = $c['siteConf'];
            $memcache = new \Memcache();
            $memcache->connect($siteConf['cache_host'], $siteConf['cache_port']);

            $cacheDriver = new \Doctrine\Common\Cache\MemcacheCache();
            $cacheDriver->setMemcache($memcache);
            $cacheDriver->setNamespace($siteConf['domain']);

            $cacheDriver->timeout = function($base = -1) use ($siteConf) {
                if($base === -1){
                    $base = intval($siteConf['cache_timeout']);
                }
                return $base + rand(0, $base);
            };

            return $cacheDriver;
        });

        $container['lock'] = function($c){
            $siteConf = $c['siteConf'];
            $memcache = new \Memcache();
            $memcache->connect($siteConf['cache_host'], $siteConf['cache_port']);

            return new Lock($memcache, $siteConf['domain']);
        };
        
        return $container;
    }

    public function initServices($container){
        $container['category'] = function($c){
            $feeder = new RawCacheCategoryFeeder($c);
            return new RawCategoryService($feeder);
        };

        $container['search'] = $container->share(function($c){
            $siteConf = $c['siteConf'];
            return new CacheSearchService($siteConf['search_endpoint'], $siteConf['domain'], $c['cache']);
        });

        #$container['shipping'] = $container->share(function($c){
        #    $dao = new ShippingDao($c['db']);
        #    return new ShippingService($dao);
        #});
        #
        #$container['coupon'] = $container->share(function($c){
        #    $dao = new CouponDao($c['db']);
        #    return $dao;
        #    //return new ShippingService($);
        #});
        #
        #$container['style'] = $container->share(function($c){
        #    $dao = new StyleDao($c['db']);
        #    return new StyleService($dao);
        #});
        #
        $container['currency'] = $container->share(function($c){
            $dao = new CurrencyDao($c['db']);
            return new CurrencyService($dao);
        });
        return $container;
    }
}
