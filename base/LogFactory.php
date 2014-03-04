<?php

namespace esmeralda\base;

use Monolog\Logger;
use Monolog\Handler\NullHandler;

class LogFactory{
    public static function get($name){
        global $container;
        return self::getWithHandlers($name, $container['log_handlers']);
    }

    public static function getWithHandlers($name, $handlers){
        $logger = new Logger($name);
        if(isset($handlers) && is_array($handlers) && !empty($handlers)){
            foreach($handlers as $handler){
                $logger->pushHandler($handler);
            }
        }else{
            $logger->pushHandler(new NullHandler());
        }
        return $logger;
    }
}
