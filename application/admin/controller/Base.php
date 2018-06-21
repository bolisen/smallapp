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
        //后台静态信息
        $html['img'] = '../../static/admin/img';
        $html['css']= '../../static/admin/css';
        $html['js'] = '../../static/admin/js';


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

        $this->assign("user",$user);
        $this->assign("html",$html);
        $this->assign('module',Request::instance()->module());
        $this->assign('controller',Request::instance()->controller());
        $this->assign('action',Request::instance()->action());
    }
}