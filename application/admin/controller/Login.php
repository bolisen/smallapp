<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/13
 * Time: 23:22
 */

namespace app\admin\controller;


use think\Controller;

class Login extends Controller
{

    /**
     * 入口文件
     * @return \think\response\View
     */
    public function index()
    {
        return view();
    }

    /**
     * 登录验证
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setLogin()
    {
        $user_name= input("user_name");
        $user_pass= input("user_pass");
        if(!$user_name || !$user_pass)$this->error("用户名或密码不能为空！");


        $where['user_name'] = $user_name;
        $where['disabled'] = 0;
        $user = Db("User")->where($where)->find();

        if(!$user) {
            $this->error("用户不存在!");
        }else{
            //判断密码
            if($user['user_pass'] !== password($user_pass)) $this->error("用户名或密码错误！");
        }

        //将登录信息放入session
        session("user_id",$user['id']);
       $this->redirect('index/index');
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        //清除登录数据
        session("user",null);
        $this->redirect("index");
    }
}