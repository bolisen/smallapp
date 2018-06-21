<?php
namespace app\common\controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/3
 * Time: 19:10
 */

class Fydb
{
    static private $instance;
    private $config;

    private function __construct($config)
    {
        $this->config = $config;
        echo "我被实例化了";
    }

    /**
     * 防止克隆
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    static public function getInstance($config)
    {
        if(!self::$instance instanceof self){
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getName()
    {
        echo "say hello!";
    }
}