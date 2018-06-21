<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 21:33
 */
namespace app\index\controller;

use think\Controller;
use think\Request;

class Base  extends Controller
{

    public function _initialize()
    {
        $this->assign('module',Request::instance()->module());
        $this->assign('controller',Request::instance()->controller());
        $this->assign('action',Request::instance()->action());
    }
}