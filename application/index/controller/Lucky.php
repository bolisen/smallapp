<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4
 * Time: 21:49
 */

namespace app\index\controller;


class Lucky extends Index
{
    public function index()
    {
        return view();
    }

    /**
     * 方案管理
     */
    public function design()
    {

        $key = I("get.key");
        $page = I("get.page","","number_int");
        $prizeModels = M("Schemeprize")->field("scheme_id")->group("scheme_id")->select();
        $count =M("Scheme as s")
            ->join("LEFT JOIN __USER__ as u on u.id=s.user_id")
            ->field("s.id as id ,s.name,s.draw_time,s.draw_num,s.state,s.order,s.winning_ways,s.pc_img,s.mob_img,u.user_nickname")
            ->where(array("s.name"=>array("like","%".$key."%")))
            ->order("state asc,draw_time desc")
            ->count();
        $pagesize = C("PAGESIZE");
        $page = getPage($count, $pagesize, $page);
        $res = M("Scheme as s")
            ->join("LEFT JOIN __USER__ as u on u.id=s.user_id")
            ->field("s.id as id ,s.name,s.draw_time,s.draw_num,s.state,s.order,s.winning_ways,s.pc_img,s.mob_img,u.user_nickname")
            ->where(array("s.name"=>array("like","%".$key."%")))
            ->page($page['p'])
            ->limit($page['s'])
            ->order("state asc,draw_time desc")->select();
        foreach ($res as $k =>$v){
            $res[$k]['url_id'] = $this->encode($v['id']);
        }
        $schemeArr = array();
        foreach ($prizeModels as $k=>$v){
            array_push($schemeArr,$v['scheme_id']);
        }
        foreach($res as $k=>$v){
            if(in_array($v['id'],$schemeArr)){
                $res[$k]['delState'] = 0;
            }else{
                $res[$k]['delState'] = 1;
            }
        }
        $this->assign("list", $res);
        $this->assign("page", $page);
        $this->display();
    }

    public function getDesign()
    {
        $id = I("get.id", 0, "number_int");
        $res = M("Scheme")->where(array("id" => $id))->find();
        $this->assign("list", $res);
        $this->display("getDesign");
    }

    public function setDesign()
    {
        $scheme = M("Scheme");
        $id = I("post.id", 0, "");
        $data['name'] = I("post.name");
        $data['user_id'] = I("post.user_id");
        $data['draw_time'] = I("post.draw_time");
        $data['draw_num'] = I("post.draw_num");
        $data['scheme_type'] = I("post.scheme_type");
        $data['winning_ways'] = I("post.winning_ways");
        $data['order'] = I("post.order");
        $data['state'] = I("post.state");
        $data['pc_id'] = I("post.pc_id");
        $data['pc_img'] = I("post.pc_img");
        $data['mob_img'] = I("post.mob_img");
        $data['mob_id'] = I("post.mob_id");
        if ($id) {//编辑
            if (!$data['name'] || !$data['draw_time'] || !$data['draw_num']) $this->error("信息缺失，修改失败");
            if ($scheme->where(array("id" => $id))->save($data) === false) $this->error("修改失败,数据异常");
        } else {//添加
            if (!$scheme->add($data)) $this->error("添加失败！");
        }
        $this->success("操作成功");
    }

    public function delDesign()
    {
        $id = I("id", 0, "number_int");
        $ids = I("ids");
        if ($ids) {
            $where['id'] = array('in', $ids);
        } else {
            $where['id'] = $id;
            if (!$id) {
                $this->error("id异常");
            }
        }
        $scheme = M("Scheme");
        $res = $scheme->where($where)->delete();
        if ($res) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    /**
     * 奖品管理
     */
    public function prize()
    {
        $sid = I("sid","","number_int");
        $key = I("get.key");
        if($key){
            $where['prize_name'] = array("like","%".$key."%");
        }
        $where['scheme_id'] = $sid;
        $count = M("Schemeprize as p")->where($where) ->order("top desc","id desc")->count();
        $pagesize = C("PAGESIZE");
        $page = I("get.page","","number_int");
        $page = getPage($count, $pagesize, $page);
        $res = M("Schemeprize as p")
            ->where($where)
            ->order("top desc","id desc")
            ->page($page['p'])
            ->limit($page['s'])
            ->select();
        $this->assign("list", $res);
        $this->assign("sid",$sid);
        $this->assign("page",$page);
        $this->display();
    }

    public function getPrize()
    {
        $sid = I("sid");
        $id = I("get.id", 0, "number_int");
        $res = $res = M("Schemeprize as p")
            ->join("LEFT JOIN __SCHEME__ as s ON p.scheme_id=s.id")
            ->field("p.id,p.name,p.num,p.img_url,s.name as scheme_name,s.id as scheme_id,p.prize_name")
            ->where(array("p.id" => $id))
            ->find();
        $this->assign("list", $res);
        $this->assign("sid",$sid);
        $this->display("getPrize");
    }

    public function setPrize()
    {
        $scheme = M("Schemeprize");
        $id = I("post.id", 0, "");
        $sid = I("post.sid", 0, "");
        $data['name'] = I("post.name");
        $data['num'] = I("post.num");
        $data['img_url'] = I("post.img_url");
        $data['scheme_id'] = $sid;
        $data['prize_name'] = I("post.prize_name");
        if (!$data['name'] || !$data['num'] || !$data['img_url']) $this->error("信息缺失，修改失败");
        if ($id) {//编辑
            if (!$scheme->where(array("id" => $id))->save($data)) $this->error("修改失败");
        } else {//添加
            if(!$data['scheme_id']) $this->error("信息缺失，添加失败");
            if (!$scheme->add($data)) $this->error("添加失败！");
        }
        $this->success("操作成功");
    }

    public function delPrize()
    {
        $id = I("id", 0, "number_int");
        $ids = I("ids");
        if ($ids) {
            $where['id'] = array('in', $ids);
        } else {
            $where['id'] = $id;
            if (!$id) {
                $this->error("id异常");
            }
        }
        $scheme = M("Schemeprize");
        $res = $scheme->where($where)->delete();
        if ($res) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    /** 改变奖品状态 */
    public function changePrize(){
        $id = I("id");
        $state =I("state");
        if($state == 1){
            $data['state'] = 3;
        }
        if($state == 3){
            $data['state'] = 1;
        }
        if(!M("Schemeprize")->where(array("id"=>$id))->save($data)) $this->error("锁定状态不可修改");
        $this->success("修改成功");
    }

    /** 查询方案名称 */
    public function getName()
    {
        $key = I("key");
        $where['name'] = array("like", "%" . $key . "%");
        $res = M("Scheme")->where($where)->field("id,name")->select();
        if ($res) {
            $this->ajaxReturn(json_encode($res));
        }
    }

    /**
     * 素材管理
     */
    public function getSource()
    {
        $id = I("id", "", "number_int");
        $this->assign("id", $id);
        $this->display();
    }

    public function setSource()
    {
        $id = I("id", "", "number_int");
        $exp = M("Schemexp");
        //手机图片
        $mob[] = I("mob_0");
        $mob[] = I("mob_1");
        $mob[] = I("mob_2");
        for ($i = 0; $i < count($mob); $i++) {
            if (empty($mob[$i])) {
                continue;
            } else {
                $data[$i]['id'] = $id;
                $data[$i]['exp_key'] = "mob_" . $i;
                $data[$i]['exp_value'] = $mob[$i];
                $where['exp_key'] = "mob_" . $i;
                $where['id'] = $id;
                if ($model = $exp->where($where)->find()) {//存在则修改
                    if (!$exp->where($where)->save($data)) $this->error("手机端第" . ($i + 1) . "张图片修改失败");
                } else {
                    if (!$exp->add($data[$i])) $this->error("手机端第" . ($i + 1) . "张图片添加失败");
                }
            }
        }
        //PC图片
        $pc[] = I("pc_0");
        $pc[] = I("pc_1");
        $pc[] = I("pc_2");
        for ($j = 0; $j < count($pc); $j++) {
            if (empty($pc[$j])) {
                continue;
            } else {
                $data[$j]['id'] = $id;
                $data[$j]['exp_key'] = "pc_" . $j;
                $data[$j]['exp_value'] = $pc[$j];
                $where['exp_key'] = "pc_" . $i;
                $where['id'] = $id;
                if ($model = $exp->where($where)->find()) {
                    if (!$exp->where($where)->save($data)) $this->error("PC端第" . ($j + 1) . "张图片修改失败");
                } else {
                    if (!$exp->add($data[$j])) $this->error("PC端第" . ($j + 1) . "张图片添加失败");
                }
            }
        }
        $this->success("添加成功");
    }


    public function luck()
    {
        $prizeId = I("pid");
        $sid = M("Schemeprize")->where(array("id"=>$prizeId))->getField("scheme_id");
        $key = I("get.key");
        $where1['prize_id'] = $prizeId;
        $where1['nickname'] = array("like","%".$key."%");
        $count =M("Schemeluck")
            ->where($where1)
            ->order("luck_time desc")
            ->count();
        $pagesize = C("PAGESIZE");
        $page = I("get.page","","number_int");
        $res = M("Schemeluck")
            ->where($where1)
            ->order("luck_time desc")
            ->select();
        foreach ($res as $k=>$v){
            $v['delState'] = 0;
        }
        $where['t.prize_id']=$prizeId;
        $where['t.state'] = array("neq",0);
        $trueLuck = M("Schemetrueluck as t")
            ->join("LEFT JOIN __SCHEMEPRIZE__ as p on p.id=t.prize_id")
            ->where($where)
            ->field("t.openid,t.id as id ,t.nickname ,t.headurl,p.name,t.prize_id as pid,t.state,t.luck_time")
            ->select();
        if($trueLuck){
            foreach ($trueLuck as $k=>$v){
                $v['delState'] = 1;
                array_unshift($res,$v);
            }
        }
        $count = $count+count($trueLuck);
        $page = getPage($count, $pagesize, $page);
        $this->assign("list",$res);
        $this->assign("pid",$prizeId);
        $this->assign("sid",$sid);
        $this->assign("page",$page);
        $this->display();
    }

    public function getLuck(){
        $pid = I("pid");
        $url=code("http://wx.".APP_DOMAINNAME."/luck/index/pid/$pid",0);
        $this->assign("url",$url);
        $this->assign("pid",$pid);
        $this->display("getLuck");
    }

    public function delLuck(){
        $id = I("id", 0, "number_int");
        if (!$id) {
            $this->error("id异常");
        }
        $model = M("Schemetrueluck");
        $res = $model->where(array("id" => $id))->delete();
        if ($res) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    public function changeLuck(){
        $id = I("id");
        $state =I("state");
        if($state == 3){
            $data['state'] = 4;
        }
        if($state == 4){
            $data['state'] = 3;
        }
        if(!M("Schemetrueluck")->where(array("id"=>$id))->save($data)) $this->error("状态修改失败");
        $this->success("修改成功");
    }

    public function delAll(){
        $schemeluck = M("Schemeluck");
        $schemeluck->where('1')->delete();
        $res = M("Schemeprize")->where(array('state'=>2))->select();
        foreach ($res as $k=>$v){
            $data['state'] = 1;
            M("Schemeprize")->where(array("id"=>$v['id']))->save($data);
        }
        $this->success("删除成功！");
    }

    /** 活动id加密 */
    public function encode($id){
        $id = "lbdc47586411323103".$id* 371 ."asdfdasfsafdsadfdasfsadfsad";
        return $id;
    }

}