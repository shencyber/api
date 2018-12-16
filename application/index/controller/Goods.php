<?php

namespace app\index\controller;
Use think\Controller;
Use think\Request;
Use think\Config;
Use think\Db;
Use app\index\model\GoodsMod;
Use app\index\model\PhotoMod;

class Goods extends Base
{
   
   
     /**
     * 添加本地商品
     * @param [int] $[name] [<商品名称>]
     * @param [int] $[desc] [<商品描述>]
     * @param [int] $[unitprice] [<单价>]
     * @param [int] $[unit] [<单位>]
     * @param [int] $[ghsid] [供货商id]
     * @param [int] $[shortUrls] [图片名称数组]
     * @param [int] $[freighttemplateid] [运费模板id>]<第一阶段先不加>
     * @return [type] [description]
     */
    // public function addLocal( $name , $desc="" ,$unitprice,$ghsid,$freighttemplateid=""   )
    public function addLocal(    )
    { 
        $req = Request::instance()->param();
        $modelObj  = new GoodsMod();

        $res = $modelObj->addLocal( $req['name'] , $req['desc'] , $req['unitprice'] ,$req['unit'], $req['ghsid'] );
        $res_arr = json_decode($res , true) ;
        // dump( $res_arr );die;
        if( $res_arr['status'] != 0 )
        {
            return $res;die;
        }

        
        //添加对应的图片
        $photo = controller('Photo');
        // $res_photo = $photo->addLocalImage( $res_arr['result'] , join( "," , $req['shorturls'] )  );
        $res_photo = $photo->addLocalImage( $res_arr['result'] ,  $req['shorturls']   );
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
     * 更新商品信息
     * @param [int] $[goodsid] [商品id]
     * @param [int] $[name] [<商品名称>]
     * @param [int] $[desc] [<商品描述>]
     * @param [int] $[unitprice] [<单价>]
     * @param [int] $[shortUrls] [图片名称数组]
     * @param [int] $[freighttemplateid] [运费模板id>]<第一阶段先不加>
     * @return [type] [description]
     */
    public function updateGoods(  )
    {
        $req = Request::instance()->param();
        
        $modelObj  = new GoodsMod();

        $res = $modelObj->updateGoods( $req['goodsid'] , $req['name'] , $req['desc'] , $req['unitprice']   );
        // return $res ;
        // die;
        $res_arr = json_decode($res , true) ;
        if( $res_arr['status'] != 0 )
        {
            return $res;die;
        }
         
        /**
         * 对于图片的更新需要做一下操作
         * 1、获取前台发过来的数组  记为new
         * 2、从数据库获取已有的图片地址数组 记为old
         * 2、将新的数组和数据库中查到的地址数组做交集运算 记为C
         * 3、old-C 得到需要delete的数据 new-C  得到需要insert的数据   
         * 
         */
        $new = $req['shorturls'];  //['a.jpg','b.jpg'] 
        // print_r( 'new' );
        // dump( $req['shorturls'] );
        // die;


        $photoObj = controller('photo');
        $photoJson = $photoObj->getImagesByGoodId( $req['goodsid'] );
        $photoArr = json_decode($photoJson , true);
        if( 0==$photoArr['status']   )
        {
            $old = $photoArr['result'] ;
        }
        else
        {
            $old = [] ;
        }
        // print_r( 'old' );
        // dump( $old );
        // die;

        $c = array_intersect( $new , $old );
        // print_r('交集');
        // dump( $c );die;

        $delete = array_diff( $old , $c );

        // print_r('delete');
        // print_r($delete);

        $insert = array_diff( $new , $c );
        // print_r('insert');
        // print_r($insert);
        // 
        if( $delete )
        {
            $res = Db::table('photo')->where( 'url' ,  'in' , $delete  )->delete();
        }

         if( $insert )
        {
            $modelObj  = new PhotoMod();
            $res = $modelObj->addLocalImage( $req['goodsid'] , $insert );
        }

        $resArr = json_decode($res , true);

        // print_r("photo");
        // print_r(  $resArr );die;
        if( 0 !=  $resArr['status'] ) 
        {
            return $res ;
            die;
        }

        $obj = Array(
            'status' => 0,
            'result' => null ,
            'desc' => "修改成功"
        );

        return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;
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
              $goodsArr['result']['shortUrls'] = [] ;
              $goodsArr['result']['longUrls'] = [] ;

              // dump( $goodsArr );
              // print_r("-------------------");
              foreach( $photoArr['result'] as $url )
              {
                array_push( $goodsArr['result']['shortUrls'] , $url );
                array_push( $goodsArr['result']['longUrls'] , Config::get('ImageBaseURL').$url );
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
    public function getGoodsListByGhsId()
    {
        $req = Request::instance()->param();

        $con = array("ghsid"=>$req['ghsid'] , "status"=>$req['type']) ;
        $count  = Db::table('goods')->where( $con )->count();

        if( 0 == $count )
        {
          $obj = Array(
            'status' => 0,
            'total'  => 0,
            'desc'   => '查询成功',
            'result' => []
          );

          return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        }

        $modelObj  = new GoodsMod();
        $res = $modelObj->getGoodsListByGhsId( $req['ghsid'] , $req['type'],$req['currentpage'],$req['pagesize'] ); 

        foreach( $res as $key=>$val )
        {
            foreach( $val['urls'] as $index=>$url )
            {

              $res[$key]['urls'][$index] = Config::get('ImageBaseURL').$url;
              
            }
        }

        $obj =Array(
          'status' => 0,
          'total'  => $count,
          'desc'   => '查询成功',
          'result' => $res

        );  

        return json_encode($obj , JSON_UNESCAPED_UNICODE);die;

    }



  

}

?>
