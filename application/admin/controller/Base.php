<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/13
 * Time: 22:13
 */
namespace app\admin\controller;

use think\Controller;
use think\Request;
class Base extends Controller
{
    public function _initialize()
    {
        parent::_initialize();

        $user_id = session("user_id");
        if(!$user_id){
            $this->error("您好，请先登录!");
        }else {
            //用户信息搜索
            $where['u.id'] = $user_id;
            $user =Db("user u")
                ->join("role r",'u.role_id=r.id')
                ->where($where)
                ->field("u.*,r.role_name")
                ->find();
        }

        $this->assign('module',Request::instance()->module());
        $this->assign('controller',Request::instance()->controller());
        $this->assign('action',Request::instance()->action());
    }
}