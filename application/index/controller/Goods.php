<?php

namespace app\index\controller;
Use think\Controller;
Use app\index\model\GoodsMod;
Use app\index\model\PhotoMod;
Use think\Config;

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
    // public function addLocal( $name , $desc="" ,$unitprice,$ghsid,$freighttemplateid=""   )
    public function addLocal(    )
    { 
        $req = Request::instance()->param();
        
        $modelObj  = new GoodsMod();

        $res = $modelObj->addLocal( $req['name'] , $req['desc'] , $req['unitprice'] , $req['ghsid'] );
        $res_arr = json_decode($res , true) ;
        if( $res_arr['status'] != 0 )
        {
            return $res;die;
        }
        
        //添加对应的图片
        $photo = controller('Photo');
        $res_photo = $photo->addLocalImage( $res_arr['result'] , join( "," , $req['shorturls'] )  );
        $res_arr = json_decode($res_photo , true) ;
        if( $res_arr['status'] != 0 )
        {
            return $res_photo; die ; 
        }
        
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
        $req = Requeset::instance()->param();
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->shangjia( $res['goodsid'] );
    }

    /**
     * 商品下架
     * @param  [int] $goodsid [商品id]
     * @return [type]            [description]
     */
    // public function xiajia( $goodsid )
    public function xiajia(  )
    {
        $req = Requeset::instance()->param();
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->xiajia( $res['goodsid'] );
    }


     /**
    * 根据商品id获取对应的商品数据
    * @param  [array] $goodsids [商品id]
    * @return [type]          [description]
    */
    // public function getGoodsListById( $goodids )
    public function getGoodsByIds(  )
    {

        $req = Requeset::instance()->param();
        $goodsids = json_decode( $req['goodsids'] , true );

        print_r( $goodsids );
        if( !is_array( $goodsids ) ) 
        {
          $obj = Array(

                'result'=>null,
                "status" => -2,
                "desc"=>"参数格式错误" 
          );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;
        } 

        $modelObj  = new GoodsMod();
        //1、获取商品信息
        $goodsJson =   $modelObj->getGoodsById( $goodsids ) ;
        $goodsArr  =   json_decode( $goodsJson , true );

        if( 0 != $goodsArr['status'] || !$goodsArr['result'] )
        {
            return $goodsJson ;die; 
        }

        //2、 根据商品信息获取图片信息
        foreach( $goodsArr['result'] as $index=>$good )
        {
            //找对应的商品图片
            $photoObj = controller('photo');
            $photoJson = $photoObj->getImagesByGoodId( $good['id'] );
            $photoArr = json_decode($photoJson , true);
            // if( 0==$photoArr['status'] &&  $photoArr['result']  )
            // {
            // foreach( $photoArr['result'] as $subindex=>$url )
            // {
            //     $photoArr['result'][$subindex] =  Config::get('ImageBaseURL').$url ;
            // }
            $goodsArr['result'][$index]['urls'] = $photoArr['result'] ;
            // }
        }

        return json_encode( $goodsArr , JSON_UNESCAPED_UNICODE );die;

    }

    
  /**
     * 根据供货商id，获取商品列表
     * @param  [type] $ghsid [供货商id]
     * @return [type]        [description]
     */
    // public function getGoodsListByGhsId( $ghsid )
    public function getGoodsListByGhsId(  )
    {
        $req = Requeset::instance()->param();
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->getGoodsListByGhsId( $req['ghsid'] );   
    }

  

}

?>
