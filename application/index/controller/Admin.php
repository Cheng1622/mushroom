<?php

namespace app\index\controller;

use think\Request;
use app\index\model\User as AdminModel;
use think\Session;
// use think\Controller;

class Admin extends Base
{
    //管理员列表
    public function  adminList()
    {
        $this->view->assign('title', '管理员列表');
        $this->view->assign('keywords', 'mushroom');
        $this->view->assign('desc', 'mushroom');

        $this->view->count = AdminModel::count();

        //判断当前是不是admin用户
        //先通过session获取到用户登陆名
        $userName = Session::get('user_info.name');
        if ($userName == 'admin') {
            $list = AdminModel::all();  //admin用户可以查看所有记录,数据要经过模型获取器处理
        } else {
            //为了共用列表模板,使用了all(),其实这里用get()符合逻辑,但有时也要变通
            //非admin只能看自己信息,数据要经过模型获取器处理
            $list = AdminModel::all(['name' => $userName]);
        }


        $this->view->assign('list', $list);
        //渲染管理员列表模板
        return $this->view->fetch('admin_list');
    }

    //管理员状态变更
    public function setStatus(Request $request)
    {
        $user_id = $request->param('id');
        $result = AdminModel::get($user_id);
        if ($result->getData('status') == 1) {
            AdminModel::update(['status' => 0], ['id' => $user_id]);
        } else {
            AdminModel::update(['status' => 1], ['id' => $user_id]);
        }
    }

    //渲染编辑管理员界面
    public function adminEdit(Request $request)
    {
        $user_id = $request->param('id');
        $result = AdminModel::get($user_id);
        $this->view->assign('title', '编辑管理员信息');
        $this->view->assign('keywords', 'mushroom');
        $this->view->assign('desc', 'mushroom');
        $this->view->assign('user_info', $result->getData());
        return $this->view->fetch('admin_edit');
    }

    //更新数据操作
    public function editUser(Request $request)
    {
        //获取表单返回的数据
        //        $data = $request -> param();
        $param = $request->param();

        //去掉表单中为空的数据,即没有修改的内容
        foreach ($param as $key => $value) {
            if (!empty($value)) {
                $data[$key] = $value;
            }
        }

        $condition = ['id' => $data['id']];
        $result = AdminModel::update($data, $condition);

        //如果是admin用户,更新当前session中用户信息user_info中的角色role,供页面调用
        if (Session::get('user_info.name') == 'admin') {
            Session::set('user_info.role', $data['role']);
        }


        if (true == $result) {
            return ['status' => 1, 'message' => '更新成功'];
        } else {
            return ['status' => 0, 'message' => '更新失败,请检查'];
        }
    }

    //删除操作
    public function deleteUser(Request $request)
    {
        $user_id = $request->param('id');
        AdminModel::update(['is_delete' => 1], ['id' => $user_id]);
        AdminModel::destroy($user_id);
    }

    //恢复删除操作
    public function unDelete()
    {
        AdminModel::update(['delete_time' => NULL], ['is_delete' => 1]);
    }

    //添加操作的界面
    public function  adminAdd()
    {
        $this->view->assign('title', '添加管理员');
        $this->view->assign('keywords', 'mushroom');
        $this->view->assign('desc', 'mushroom');
        return $this->view->fetch('admin_add');
    }

    //检测用户名是否可用
    public function checkUserName(Request $request)
    {
        $userName = trim($request->param('name'));
        $status = 1;
        $message = '用户名可用';
        if (AdminModel::get(['name' => $userName])) {
            //如果在表中查询到该用户名
            $status = 0;
            $message = '用户名重复,请重新输入~~';
        }
        return ['status' => $status, 'message' => $message];
    }

    //检测用户邮箱是否可用
    public function checkUserEmail(Request $request)
    {
        $userEmail = trim($request->param('email'));
        $status = 1;
        $message = '邮箱可用';
        if (AdminModel::get(['email' => $userEmail])) {
            //查询表中找到了该邮箱,修改返回值
            $status = 0;
            $message = '邮箱重复,请重新输入~~';
        }
        return ['status' => $status, 'message' => $message];
    }

    //添加操作
    public function addUser(Request $request)
    {
        $data = $request->param();
        $status = 1;
        $message = '添加成功';

        $rule = [
            'name|用户名' => "require|min:3|max:10",
            'password|密码' => "require|min:3|max:10",
            'email|邮箱' => 'require|email'
        ];

        $result = $this->validate($data, $rule);

        if ($result === true) {
            $user = AdminModel::create($request->param());
            if ($user === null) {
                $status = 0;
                $message = '添加失败~~';
            }
        }


        return ['status' => $status, 'message' => $message];
    }
}
