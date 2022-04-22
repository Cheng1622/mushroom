<?php

namespace app\index\controller;
use app\index\model\Grade as GradeModel;
use think\Request;

class Mushroom extends Base
{
    //蘑菇列表
    public function  index()
    {
        $this -> view -> assign('title', '蘑菇分析');
        $this -> view -> assign('keywords', 'mushroom');
        $this -> view -> assign('desc', 'mushroom');    

 $count = 0;
        $gradeList = [];
        //获取所有蘑菇表数据
        $grade = GradeModel::table('grade')->order('id desc') ->limit(3)->select();
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

        return $this -> view -> fetch('index');
    // 
}

}
