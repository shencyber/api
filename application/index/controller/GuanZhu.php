<?php

namespace app\index\controller;
Use think\Controller;
Use app\index\model\GuanZhuMod;


class Guanzhu extends Controller
{
   
   
    /**
     * 添加关注关系
     * @param [int] $[dlsid] [<代理商id>]
     * @param [int] $[ghsid] [<供货商id>]
     * @return [type] [description]
     */
    // public function guanZhu(  $dlsid , $ghsid  )
    public function guanZhu(    )
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->add( 1 , 4  );
        return $res ;

    }

    /**
     * 取消代理商和供货商的关注关系
     * @param  [type] $guanzhuid [description]
     * @return [type]            [description]
     */
    // public function cancel( $guanzhuid )
    public function cancel(  )
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->cancel( 2  );
        return $res ;
    }

     /**
     * 获取素有关注信息
     * @return [type]        [description]
     */
    public function getAllGuanZhuInfo()
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->allList(  );
        return $res ;   
    }

    /**
     * 根据代理商id，获取关注列表
     * @param  [type] $dlsid [代理商id]
     * @return [type]        [description]
     */
    // public function getListByDlsId( $dlsid )
    public function getListByDlsId(  )
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->getListByDlsId( 1 );
        return $res ;      
    }

    /**
     * 根据供货商id，获取关注列表
     * @param  [type] $dlsid [供货商id]
     * @return [type]        [description]
     */
    // public function getListByGhsId( $dlsid )
    public function getListByGhsId(  )
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->getListByGhsId( 4 );
        return $res ;      
    }

  

}

?>
