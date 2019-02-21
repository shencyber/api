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
     * [deleteCateById 删除分类 ,如果该分类下有商品则无法删除]
     * @return [type] [description]
     */
    public function deleteCateById()
    {
        $param = Request::instance()->param();
        $cateId = $param['cateId'] ;

        $obj = Array("status"=>0 , "desc"=>"");
        //1、判断该分类是否存在
        $exists = Db::table( $this->table )->where( "id" , $cateId )->find() ;
        if( $exists == NULL ) 
        {

            return json_encode(Array("status"=>1,"desc"=>"分类不存在") , JSON_UNESCAPED_UNICODE) ;
            die;
        }

        //2、查询该分类下是否有商品
        $exists = DB::table('goods')->where('cateid' , $cateId )->find();
        if(  $exists == NULL )
        {
             $res =  Db::table( $this->table )->delete( $cateId ); 

            if( $res > 0 ) 
                return json_encode(Array("status"=>0,"desc"=>"删除成功") , JSON_UNESCAPED_UNICODE) ;
            else 
                return json_encode(Array("status"=>-1,"desc"=>"删除失败") , JSON_UNESCAPED_UNICODE) ;   

        } 
        else
        {
            return json_encode(Array("status"=>2,"desc"=>"该分类下有商品,无法删除分类") , JSON_UNESCAPED_UNICODE) ;
            die;
        }
        
    }

    /**
     * 根据供货商id获取所有分类
     */
    public function getCateList()
    {
        $param = Request::instance()->param();
        
        //1、查询已有的分类
        $list = Db::table($this->table)->where(['ghsid'=>$param['ghsid']])->order('create_time','desc')->select();

        // foreach( $list as $item=>$val )
        // {

        //     $_count = Db::table("goods")->where('cateid' , $val['id'] )->count();
        //     $list[$item]['count'] = $_count ;   
        // }

        return json_encode(Array('status'=>0,'desc'=>'查询成功','result'=>$list) , JSON_UNESCAPED_UNICODE);

    }

    /**
     * GET done
     * [getCateListWithGoodsCount 查询所有的分类及其下面的商品数量,包括未分类的商品]
     * @return [JSON] {
                        "status": 0,
                        "desc": "查询成功",
                        "result": [
                            {
                                "id": 0,
                                "cate": "未分类",
                                "count": 5
                            },
                            {
                                "id": "13",
                                "cate": "ddf",
                                "ghsid": "1",
                                "create_time": "2019-02-19 17:42:31",
                                "count": 1
                            }
                        ]
                    }
     */
    public  function getCateListWithGoodsCount()
    {
        $param = Request::instance()->param();
        
        //1、查询已有的分类
        $list = Db::table($this->table)->where(['ghsid'=>$param['ghsid']])->order('create_time','desc')->select();

        foreach( $list as $item=>$val )
        {

            $_count = Db::table("goods")->where('cateid' , $val['id'] )->count();
            $list[$item]['count'] = $_count ;   
        }

        //查询未分类的数据
        $_nocate_count = Db::table("goods")->where(['cateid'=>0])->count();

        array_unshift($list, Array('id'=>0,'cate'=>'未分类','count'=>$_nocate_count));
        
        return json_encode(Array('status'=>0,'desc'=>'查询成功','result'=>$list) , JSON_UNESCAPED_UNICODE);

    }

  

}

?>
