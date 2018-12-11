<?php

namespace app\index\controller;
Use think\Controller;
Use app\index\model\GoodsMod;


class Goods extends Controller
{
   
   
     /**
     * 添加本地商品
     * @param [int] $[name] [<商品名称>]
     * @param [int] $[desc] [<商品描述>]
     * @param [int] $[unitprice] [<单价>]
     * @param [int] $[ghsid] [供货商id]
     * @param [int] $[freighttemplateid] [运费模板id>]<第一阶段先不加>
     * @return [type] [description]
     */
    // public function addLocal(  $name , $desc="" ,$unitprice,$ghsid,$freighttemplateid=""  )
    public function addLocal(  )
    {
        $modelObj  = new GoodsMod();
        // $res = $modelObj->addLocal( $name , $desc="" ,$unitprice,$ghsid,$freighttemplateid=""  );
        $res = $modelObj->addLocal( '这是第一个商品','' ,30 , 2 , '' );
        $res_arr = json_decode($res , true) ;
        if( $res_arr['status'] != 0 )
        {
            return $res;die;
        }
        
        //添加对应的图片
        $photo = controller('Photo');
        $res_photo = $photo->addLocalImage( $res_arr['result'] , 'dnfkgdg.jpg,dfggfg.jpg'  );
        $res_arr = json_decode($res_photo , true) ;
        if( $res_arr['status'] != 0 )
        {
            return $res_photo; die ; 
        }
        // echo "<pre>";
        // var_dump( $res_photo );
        // echo "</pre>";
        return $res ; die;

    }

    /**
     * 商品上架
     * @param  [int] $goodsid [商品id]
     * @return [type]            [description]
     */
    
    // public function shangjia( $goodsid )
    public function shangjia(  )
    {
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->shangjia( 4 );
    }

    /**
     * 商品下架
     * @param  [int] $goodsid [商品id]
     * @return [type]            [description]
     */
    // public function xiajia( $goodsid )
    public function xiajia(  )
    {
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->xiajia( 1 );
    }

    
  /**
     * 根据供货商id，获取商品列表
     * @param  [type] $ghsid [供货商id]
     * @return [type]        [description]
     */
    // public function getGoodsListByGhsId( $ghsid )
    public function getGoodsListByGhsId(  )
    {
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->getGoodsListByGhsId( 2 );   
    }

  

}

?>
