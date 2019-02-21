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

        $res = $modelObj->addLocal( $req['name'] , $req['desc'] , $req['unitprice'] ,$req['unit'], $req['ghsid'] , $req['cateId'] );
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

        $res = $modelObj->updateGoods( $req['goodsid'] , $req['name'] , $req['desc'] , $req['unitprice'],$req['unit'] , $req['cateId']  );
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
      * API  
    * 根据商品id获取对应的商品数据
    * @param  [array] $goodsids [商品id]
    * @return [type]          [description]
    */
    // public function getGoodsListById( $goodids )
    public function getGoodsById(  )
    {

        $req = Request::instance()->param();

        $modelObj  = new GoodsMod();
        //1、获取商品信息
        $list =   $modelObj->getGoodsById( $req['goodsid'] ) ;
        // dump( $list );die;
        foreach( $list as $key=>$value)
        {
          if(  $value['id'] == null)
          {
            unset( $list[$key] );
          }
          else
          {
            $tmp_urls = explode( "," , $value['urls'] ) ;
            $list[$key]['shortUrls'] = $tmp_urls ;
            foreach( $tmp_urls as $index=>$val )
            {
              if( 1 == $value['source'] )
                $tmp_urls[$index] = Config::get('ImageBaseURL').$val; 
              else if( 2 == $value['source'] )  
                $tmp_urls[$index] = Config::get('YPImageBaseUrl').$val; 
            }
            $list[$key]['longUrls'] = $tmp_urls ;
          }
        }
       

        if(  empty($list) )
        {
          $obj = [ 'status'=>0 , 'desc'=>'无数据' , 'result'=>[] ];

        }
        else
        {
          $obj = [ 'status'=>0 , 'desc'=>'查询成功' , 'result'=>$list[0] ];
        }

       
        return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;

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
        // dump( $res );die;

        foreach( $res as $key=>$val )
        {
            
            foreach( $val['urls'] as $index=>$url )
            {

              if( 1 == $val['source'] )
                $res[$key]['urls'][$index] = Config::get('ImageBaseURL').$url;
              else if( 2 == $val['source'] )
                $res[$key]['urls'][$index] = Config::get('YPImageBaseUrl').$url;
            }

            // if( 2 == $val['source'] )
            // {

              // echo "<pre>";
              // print_r( $res[$key]['name'] );
              // print_r( base64_decode($val['name']) );
              // echo "</pre>";
            

                // $res[$key]['name'] = base64_decode($val['name']);
                // print_r( $res[$key]['name'] );
              // $res[$key]['name'] = urldecode("P100++%E5%86%A0%E5%86%9B%F0%9F%8F%86%E7%A7%8B%E5%86%AC%E4%B8%93%E6%9F%9C%E6%96%B0%E6%AC%BE%EF%BC%8Cchampion+%E9%BA%92%E9%BA%9F%E8%8A%B1%E8%87%82+%E5%8F%8C%E8%87%82%E5%8D%B0%E8%8A%B1+USA%E5%8A%A0%E7%BB%92%E8%BF%9E%E5%B8%BD%E5%8D%AB%E8%A1%A3%EF%BC%8C%E5%8A%");

            // }
        }
       
        $obj =array(
          'status' => 0,
          'total'  => $count,
          'desc'   => '查询成功',
          'result' => $res

        ); 

      

        return json_encode($obj , JSON_UNESCAPED_UNICODE  );die;

    }

    /**GET DONE
     * 根据分类id，获取商品列表
     * @param  [type] $cateId [分类id]
     * @param  [type] $type [1-已上架  2-已下架]
     * @param  [type] $currentpage [当前页数]
     * @param  [type] $pagesize [每页显示数量]
     * @return [type]        [description]
     */
    public function getGoodsListByCateId()
    {
        $req = Request::instance()->param();

        $con = array("cateid"=>$req['cateId'] , "status"=>$req['type']) ;
        $res  = Db::table('goods')->where( $con )->page($req['currentpage'],$req['pagesize'])->select();

        if( !$res )
        {
          $obj = Array(
            'status' => 0,
            'total'  => 0,
            'desc'   => '查询成功',
            'result' => []
          );

          return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        }
        

        foreach ($res as $key => $value) {
          
          $res[$key]['urls'] = Array() ;
          $urls = Db::table('photo')->where('goodid',$value['id'])->field('url')->find();
          $res[$key]['urls'][0] = $urls['url'] ;

        }

        foreach( $res as $key=>$val )
        {
            
            foreach( $val['urls'] as $index=>$url )
            {

              if( 1 == $val['source'] )
                $res[$key]['urls'][$index] = Config::get('ImageBaseURL').$url;
              else if( 2 == $val['source'] )
                $res[$key]['urls'][$index] = Config::get('YPImageBaseUrl').$url;
            }

        }
       
        $obj =array(
          'status' => 0,
          'total'  => count($res) ,
          'desc'   => '查询成功',
          'result' => $res

        ); 

        return json_encode($obj , JSON_UNESCAPED_UNICODE  );die;

    }





    /**
     * [searchGoodsByImage 根据用户上传的图片查找对应的商品及其供货商信息]
     * @return [array] [status 0 - 找到  1-未找到]
     */
    public function searchGoodsByImage()
    {      
        $req = Request::instance();
        // print_r($req);die;
        $file = $req->file('image');
        $param = $req->param();
        

        $photoCon = controller( 'photo' );
        $res = $photoCon->searchByImage( $file );
        // print_r( $res );die;
        $res_arr = json_decode($res , true);
        if( 0 != $res_arr['status'] ) 
        {
            return $res;die;
        }
        else
        {
            $modObj = model('GoodsMod');
            // $goodsids = $res_arr['result'];
            // dump( $goodsids );
            //1、根据商品id找到商品详情和对应的供货商信息
            // $res = $modObj->getGoodsByIds( [ 222] );
            $res = $modObj->getGoodsByIds( $res_arr['result'] );
            // dump( $res );die;

            //如果前台传过来供货商id，则需要匹配该供货商的照片
            if( !empty($param['ghsid'] ))
            {
              foreach( $res as$key=>$val )
              {
                if( $val['ghsid'] != $param['ghsid'] )
                {
                  unset( $res[$key] );
                }
              }
            }

            if( empty( $res ) )
            {
                return json_encode(Array( 'status'=>1 , 'desc'=>'未找到对应的商品信息','result'=>[] ) , JSON_UNESCAPED_UNICODE);die;
            }
            else
            {
               foreach( $res as $key=>$val )
                {
                    $res[$key]['goods'] = array( 'id'=>$val['id'],'name'=>$val['name'],'unitprice'=>$val['unitprice'],'desc'=>$val['desc']  );
                    unset( $res[$key]['id'] );
                    unset( $res[$key]['name'] );
                    unset( $res[$key]['unitprice'] );
                    unset( $res[$key]['desc'] );
                    
                }

                // print_r( $res );die;

                $ghsids =array();
                foreach( $res as $key=>$val ){ array_push($ghsids , $val['ghsid']); }
                $ghsids = array_unique($ghsids );
                $ghsids_obj = array();
                foreach( $ghsids as $val)
                {
                    array_push( $ghsids_obj , array( 'ghsid'=>$val , 'ghsname'=>'','goods'=>array() ) );    
                }
                // print_r( $ghsids_obj );die;
                foreach( $res as $key=>$val )
                {
                    foreach( $ghsids_obj as $subkey=>$subval )
                    {
                        if( $subval['ghsid'] == $val['ghsid'] )
                        {
                            $ghsids_obj[$subkey]['ghsname'] = $val['ghsname'];
                            array_push($ghsids_obj[$subkey]['goods'] , $val['goods'] ); 
                        }    
                    }
                    
                }

                // dump( $ghsids_obj );die;
                
                return json_encode( Array('status'=>0 , 'desc'=>'找到对应的商品信息','result'=>$ghsids_obj ) , JSON_UNESCAPED_UNICODE);die;
                
            }

        }

    }



    /**
     * [getGoodsByKeyWordsApi 根据关键字查询商品信息] DONE
     * @param  [type] $keyword [关键字]
     * @return [type]          [description]
     */
    // public function getGoodsByKeyWordsApi($keyword)
    public function getGoodsByKeyWordsApi()
    {
      $req = Request::instance();
      $param = $req->param() ; 
      
      $keyword = explode(" " , $param['keyword'] ) ;
      //去重
      $keyword = array_unique($keyword) ; 
      //过滤空字符串
      $searchWord =array();
      foreach ($keyword as $key => $val) 
      {
        if (  empty($val)  ) {  continue;  }
        $searchWord[] = $val;
      }
      foreach($searchWord as $key=>$val)
      {
        $searchWord[$key] = "%".$val."%"; 
      }
      
      // 1、根据关键词在goods表内根据name搜索对应的商品
      if( empty($param['ghsid']) )
      {
        $goods = Db::table('goods')->where('name' , 'like' , $searchWord)->select();
      }
      else
      {
        $goods = Db::table('goods')->where('name' , 'like' , $searchWord)->where('ghsid' ,$param['ghsid'] )->select();
      }

      foreach( $goods as $key=>$val )
      {
        $url = Db::table('photo')->where('goodid' , $val['id'])->field('url')->find();
        if( 1 == $val['source'] )
        {
          $url = array( Config::get('ImageBaseURL').$url['url'] ) ;
        }
        else if( 2 == $val['source'] )
        {
          
          $url = array( Config::get('YPImageBaseUrl').$url['url'] ) ;
        }
        // print_r($url);
        $goods[$key]['urls'] = $url ;
      }

      if( !empty($param['ghsid']) )
      {
          return json_encode(Array('status'=> 0,'desc' => "查询成功",'result' => $goods) ,JSON_UNESCAPED_UNICODE);
      }

      // 2、如果没有ghsid，则获取商品对应的供货商name和id
      else
      {

        //获取商品对应的供货商id
        $ghsids = array();
        foreach( $goods as $key=>$val )
        {
          array_push( $ghsids , $val['ghsid'] );
        }
        $ghsids = array_unique($ghsids) ;
        $ghs = Db::table('gonghuoshang')->where('id' , 'in' ,  $ghsids)->field('id,name')->select();
        
        $res =array(); 
        foreach( $ghs as $key => $val )
        {
          $res[$key]['ghsid'] = $val['id'] ;
          $res[$key]['ghsname'] = $val['name'] ;
          $res[$key]['goods'] = array() ;
          foreach( $goods as $index=>$good )
          {
            if( $good['ghsid'] == $val['id'] )
            {
              array_push( $res[$key]['goods'] , $good );
              
            }
          }
        }

        // print_r( $res );die;
         return json_encode(Array('status' => 0,'desc' => "查询成功",'result'  =>  $res ),JSON_UNESCAPED_UNICODE);

      }
     

     
    }

    




  

}

?>
