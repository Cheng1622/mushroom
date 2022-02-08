<?php

namespace app\index\controller;

use app\index\model\Grade as GradeModel;
use think\Request;

class Grade extends Base
{
    //蘑菇列表
    public function  gradeList()
    {
        $this->view->assign('title', '蘑菇列表');
        $count = 0;
        $gradeList = [];
        //获取所有蘑菇表数据
        $grade = GradeModel::table('grade')->order('id desc')->select();
        //获取记录数量"

        $count = GradeModel::count();
        //遍历grade表


        foreach ($grade as $value) {
            $data = [
                'id' => $value->id,
                'number' => $value->number,
                'mushroom' => $value->mushroom,
                'coordinate' => $value->coordinate,
                'size' => $value->size,
                'canque' => $value->canque,
                'hebian' => $value->hebian,
                'grade' => $value->grade,
                'status' => $value->status,
                'create_time' => $value->create_time,
                'update_time' => $value->update_time,
                'delete_time' => $value->delete_time,
                'is_delete' => $value->is_delete,
            ];
            //每次关联查询结果,保存到数组$gradeList中
            $gradeList[] = $data;
        }

        $this->view->assign('gradeList', $gradeList);
        $this->view->assign('count', $count);

        return $this->view->fetch('grade_list');
    }

    //蘑菇状态变更
    public function setStatus(Request $request)
    {
        //获取当前的蘑菇ID
        $grade_id = $request->param('id');

        //查询
        $result = GradeModel::get($grade_id);

        //合格和不合格处理
        if ($result->getData('status') == 1) {
            GradeModel::update(['status' => 0], ['id' => $grade_id]);
        } else {
            GradeModel::update(['status' => 1], ['id' => $grade_id]);
        }
    }

    //渲染蘑菇编辑界面
    public function gradeEdit(Request $request)
    {
        //获取到要编辑的蘑菇ID

        //获取当前的蘑菇ID
        $grade_id = $request->param('id');

        //查询
        $result = GradeModel::get($grade_id);

        //根据ID进行查询

        //关联查询,获取与当前蘑菇对应的教师编号
        // $result -> mushroom = $result -> mushroom->name;

        //给当前页面seo变量赋值
        $this->view->assign('title', '编辑蘑菇');
        $this->view->assign('keywords', '');
        $this->view->assign('desc', 'mushroom');

        //给当前编辑模板赋值
        $this->view->assign('grade_info', $result);

        //渲染编辑模板
        return $this->view->fetch('grade_edit');
    }

    //蘑菇更新
    public function doEdit(Request $request)
    {
        //从提交表单中排除关联字段mushroom字段
        $data = $request->except('mushroom');
        //        $data = $request -> param();  如果全部获取,提交会提示缺少字段mushroom

        //设置更新条件
        $condition = ['id' => $data['id']];

        //更新当前记录
        $result = GradeModel::update($data, $condition);

        //设置返回数据
        $status = 0;
        $message = '更新失败,请检查';

        //检测更新结果,将结果返回给grade_edit模板中的ajax提交回调处理
        if (true == $result) {
            $status = 1;
            $message = '恭喜, 更新成功~~';
        }
        return ['status' => $status, 'message' => $message];
    }

    //渲染蘑菇添加界面
    public function gradeAdd()
    {
        //给模板赋值seo变量
        $this->view->assign('title', '编辑蘑菇');
        $this->view->assign('keywords', 'mushroom');
        $this->view->assign('desc', 'mushroom');

        //渲染添加模板
        return $this->view->fetch('grade_add');
    }

    //添加蘑菇
    public function doAdd(Request $request)
    {
        //从提交表单中获取数据
        $data = $request->param();
        // $file = request()->file('image');
        $file =
            request()
            ->file('mushroom');
        // request()->file('image');
        $name = $file->getInfo();
        dump($name);
        $mushroom = file_get_contents($name['tmp_name']);
        // if ($fp = fopen($name['tmp_name'], "rb", 0)) {
            //     fclose($fp);
            $gammer = base64_encode($mushroom);
            // }
            // dump( filesize($mushroom));
        $data['mushroom'] = $gammer;
        //更新当前记录
        $result = GradeModel::create($data);

        //设置返回数据的初始值
        $status = 0;
        $message = '添加失败,请检查';

        //检测更新结果,将结果返回给grade_edit模板中的ajax提交回调处理
        if (true == $result) {
            $status = 1;
            $message = '恭喜, 添加成功~~';
        }

        //自动转为json格式返回
        return ['status' => $status, 'message' => $message];
    }

    //软删除操作
    public function deleteGrade(Request $request)
    {
        $user_id = $request->param('id');
        GradeModel::update(['is_delete' => 1], ['id' => $user_id]);
        GradeModel::destroy($user_id);
    }

    //恢复删除操作
    public function unDelete()
    {
        GradeModel::update(['delete_time' => NULL], ['is_delete' => 1]);
    }
}
