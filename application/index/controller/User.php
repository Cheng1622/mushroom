<?php

namespace app\index\controller;

use think\Request;
use app\index\model\User as UserModel;
use think\Session;
use think\Controller;

class User extends Controller
{
    //登陆界面
    public function login()
    {
        $this->alreadyLogin();
        $this->view->assign('title', '登陆界面');
        // $this -> view -> assign('keywords', 'mushroom');
        // $this -> view -> assign('desc', 'mushroom');
        $this->view->assign('copyRight', '©2021-2022 mushroom');
        return $this->view->fetch();
    }

    public function md5(Request $request)
    {
        $data = $request->param();
        return  md5($data['pwd']);
    }

    //登录验证
    public function checkLogin(Request $request)
    {
        $status = 0; //验证失败标志
        $result = '验证失败'; //失败提示信息
        $data = $request->param();

        //验证规则
        $rule = [
            'name|用户名' => 'require',
            'password|密码' => 'require',
            'captcha|验证码' => 'require|captcha'
        ];

        //验证数据 $this->validate($data, $rule, $msg)
        $result = $this->validate($data, $rule);

        //通过验证后,进行数据表查询
        //此处必须全等===才可以,因为验证不通过,$result保存错误信息字符串,返回非零
        if (true === $result) {

            //查询条件
            $map = [
                'name' => $data['name'],
                'password' => md5($data['password'])
            ];
            $user = UserModel::get($map);
            $result = $user;
            if (null === $user) {
                $result = '没有该用户,请检查';
            } else {
                $status = 1;
                $result = '验证通过，请稍后';

                //创建2个session,用来检测用户登陆状态和防止重复登陆
                Session::set('user_id', $user->id);
                Session::set('user_info', $user->getData());

                //更新用户登录次数:自增1
                $user->setInc('login_count');
            }
        }
        return ['status' => $status, 'message' => $result, 'data' => $data];
    }

    //退出登陆
    public function logout()
    {
        //退出前先更新登录时间字段,下次登录时就知道上次登录时间了
        UserModel::update(['login_time' => time()], ['id' => Session::get('user_id')]);
        Session::delete('user_id');
        Session::delete('user_info');

        $this->success('注销登陆,正在返回', url('user/login'));
    }

    // public function anydoor()
    // {
    //     Session::set('user_id', 99999);
    //     Session::set('user_info', "");
    // }



    protected function _initialize()
    {
        parent::_initialize();
        define('USER_ID',  Session::get('user_id') ?? null);
    }

    //判断用户是否登陆,放在系统后台入口前面: index/index
    protected function isLogin()
    {
        if (is_null(USER_ID)) {
            $this->error('用户未登陆,无权访问', url('user/login'));
        }
    }
    protected function alreadyLogin()
    {
        if (USER_ID) {
            $this->error('用户已经登陆,请勿重复登陆', url('index/index'));
        }
    }
}
