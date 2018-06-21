<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/13
 * Time: 22:13
 */
namespace app\admin\controller;


class Index extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 入口页面
     * @return \think\response\View
     */
    public function index()
    {
        return view();
    }

}