<?php

namespace app\index\controller;
Use think\Controller;
Use think\Request;
Use think\Db;
// Use app\index\model\DlsMod;
// Use app\index\model\GuanZhuMod;


class Category extends Controller
{
   protected $table = "category";
   
    /**
     * 添加分类
     */
    public function addCategory(   )
    {
        $param = Request::instance()->param();
        $res = Db::table('category')->where(['ghsid'=>$param['ghsid'] , 'cate' => $param['cate']] )->find();

        if( $res != NULL )
        {
            return json_encode(array( 'status'=>1 , 'desc'=>'该分类已存在' ,'result'=>null ) , JSON_UNESCAPED_UNICODE) ;
        }
        else
        {
            $id = Db::table($this->table)->insertGetId([ 'ghsid'=>$param['ghsid'] ,'cate'=>$param['cate'] , 'create_time'=>date("Y-m-d H:i:s")  ]);
            return json_encode(array( 'status'=>0 , 'desc'=>'添加成功' ,'result'=> $id) , JSON_UNESCAPED_UNICODE) ;
        }
        

    }

    /**
     * [editCate 修改分类]
     * @return [type] [description]
     */
    public function editCategory()
    {
        $param = Request::instance()->param();
        $cateId = $param['cateId']; //分类id
        $cate = $param['cate']; //分类名
        $effectedRows = Db::table($this->table)->where('id' , $cateId)->update([ 'cate'=>$cate , 'create_time'=>date("Y-m-d H:i:s")  ]);
        if( $effectedRows > 0 )
        {
            return json_encode(Array('status'=>0,'desc'=>'修改成功') , JSON_UNESCAPED_UNICODE);
        }
        else
        {
            return json_encode(Array('status'=>-1,'desc'=>'修改失败') , JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 根据供货商id获取所有分类
     */
    public function getCateList(  )
    {
        $param = Request::instance()->param();
        $list = Db::table($this->table)->where(['ghsid'=>$param['ghsid']])->order('create_time','desc')->select();
        return json_encode(Array('status'=>0,'desc'=>'查询成功','result'=>$list) , JSON_UNESCAPED_UNICODE);
    }

  

}

?>
