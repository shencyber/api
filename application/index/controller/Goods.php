<?php

namespace app\index\controller;
Use think\Controller;
Use think\Request;
Use app\index\model\GoodsMod;
Use app\index\model\PhotoMod;
Use think\Config;

class Goods extends Base
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
        $req = Request::instance()->param();
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->shangjia( $req['goodsid'] );
    }

    /**
     * 商品下架
     * @param  [int] $goodsid [商品id]
     * @return [type]            [description]
     */
    // public function xiajia( $goodsid )
    public function xiajia(  )
    {
        $req = Request::instance()->param();
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->xiajia( $req['goodsid'] );
    }


     /**
    * 根据商品id获取对应的商品数据
    * @param  [array] $goodsids [商品id]
    * @return [type]          [description]
    */
    // public function getGoodsListById( $goodids )
    public function getGoodsById(  )
    {

        $req = Request::instance()->param();
        // $goodsids = json_decode( $req['goodsids'] , true );

        // print_r( $goodsids );
        // if( !is_array( $goodsids ) ) 
        // {
        //   $obj = Array(

        //         'result'=>null,
        //         "status" => -2,
        //         "desc"=>"参数格式错误" 
        //   );
        //     return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;
        // } 

        $modelObj  = new GoodsMod();
        //1、获取商品信息
        $goodsJson =   $modelObj->getGoodsById( $req['goodsid'] ) ;
        $goodsArr  =   json_decode( $goodsJson , true );
        if( 0 != $goodsArr['status'] || !$goodsArr['result'] )
        {
          
            return $goodsJson ;die; 
        }

        //2、 根据商品信息获取图片信息
        // foreach( $goodsArr['result'] as $index=>$good )
        // {
            //找对应的商品图片
            $photoObj = controller('photo');
            $photoJson = $photoObj->getImagesByGoodId( $req['goodsid'] );
            $photoArr = json_decode($photoJson , true);
            // print_r("输出照片 ");dump( $photoArr );die;
        // dump($goodsArr);
        // die;
        // print_r("-------------------");
        // dump( $photoJson );  //['' , '']
            if( 0==$photoArr['status'] &&  $photoArr['result']  )
            {
              // foreach( $photoArr['result'] as $subindex=>$url )
              $goodsArr['result']['urls'] = [] ;

              // dump( $goodsArr );
              // print_r("-------------------");
              foreach( $photoArr['result'] as $url )
              {
                array_push( $goodsArr['result']['urls'] , Config::get('ImageBaseURL').$url );
                  // $photoArr['result'][$subindex] =  Config::get('ImageBaseURL').$url ;
              }
              // $goodsArr['result']['urls'] = $photoArr['result'] ;
            }
            
            // $goodsArr['result']['urls'] = $photoArr['result'] ;
            // die;
        // }
            // dump( $goodsArr );die;
        return json_encode( $goodsArr , JSON_UNESCAPED_UNICODE );die;

    }

    
  /**
     * 根据供货商id，获取商品列表
     * @param  [type] $ghsid [供货商id]
     * @param  [type] $type [1-已上架  2-已下架]
     * @param  [type] $currentpage [当前页数]
     * @param  [type] $pagesize [每页显示数量]
     * @return [type]        [description]
     */
    // public function getGoodsListByGhsId( $ghsid )
    public function getGoodsListByGhsId(  )
    {
        $req = Request::instance()->param();
        $modelObj  = new GoodsMod();
        // return $modelObj->shangjia( $goodsid );
        return $modelObj->getGoodsListByGhsId( $req['ghsid'] , $req['type'],$req['currentpage'],$req['pagesize'] );   
    }

  

}

?>
