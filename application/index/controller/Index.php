<?php
namespace app\index\controller;

use app\index\model\Grade;

class Index extends Base
{
    public function index()
    {
        $this -> isLogin();  //判断用户是否登录
        $this -> view -> assign('title', 'mushroom');
        return $this -> view -> fetch();  //渲染首页模板
    }


}