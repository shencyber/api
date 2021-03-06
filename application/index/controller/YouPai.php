<?php

namespace app\index\controller;
Use think\Controller;
Use think\Config;
Use \think\Request;
Use \think\Db;
Use app\index\model\GhsMod;
Use app\index\model\PhotoMod;

class Youpai extends Base
{

    /**
     * getALLCatAndGoods 获取又拍相册所有分类 20181221
     * @param  [int] $ghsid [供货商id]
     * @return [json]        { 
     * status:0 ,    0-成功 1-账号不存在、或token已过期  
     * desc:'获取商品信息' , 
     * result:[  
     *     {"albumid":860656,"goods":[{"id":20084029,"name":"扫一扫加微信","cover":"\/2855775930_v\/815cc4bc\/da747216.jpg","type":"photo"},{"id":20084015,"name":"微商相册二维码，转图神器","cover":"\/2855775930_v\/85d16ac9\/bca48564.png","type":"photo"}]}  ,   
     * 
     * {"albumid":860656,"goods":[{"id":20084029,"name":"扫一扫加微信","cover":"\/2855775930_v\/815cc4bc\/da747216.jpg","type":"photo"},{"id":20084015,"name":"微商相册二维码，转图神器","cover":"\/2855775930_v\/85d16ac9\/bca48564.png","type":"photo"}]}
     * 
     * 
     * ] }
     */
    public function getALLCatAndGoods()
    {
        $req = Request::instance()->param();

        // 1\判断是否已授权或者token是否过期
        $ghs  = new GhsMod();
        $res = $ghs->isExpireTokenYP( $req['ghsid'] );
        // print_r( "dkfgld" );
        // dump($res);

        if( !$res )
        {
            $obj = Array(
                'status'=>1,
                'desc'=>"账号不存在、或token已过期",
                'result'=>$res
            );   
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;         
        }

        // $token = Db::table('gonghuoshang')->where(['id'=>$req['ghsid']])->field('youpaitoken')->select();
        // $token = $token[0]['youpaitoken'] );
        $ghs  = new GhsMod();
        $res  =$ghs->getGhsInfo( $req['ghsid'] );
        // dump( $res );
        $res_arr = $res[0];
        // dump($res_arr);die;
        // $res_arr = json_decode($res , true);
        // print_r($res_arr['result']);
       
            $useridYP = $res_arr['youpaiuserid'];
            $openId = $res_arr['youpaiopenid'];
            $token  = $res_arr['youpaitoken'];
            
            $arr = array( $token , '/category/openId=' , $openId , Config::get('YPAppKey') );
            $sign = md5( join("" , $arr) ) ;
            $requestUrl = Config::get('YPApi').'category?token='.$token.'&sign='.$sign.'&openId='.$openId ;
            
            $ch = curl_init();

            curl_setopt( $ch , CURLOPT_URL ,  $requestUrl );
            curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
            curl_setopt( $ch , CURLOPT_HEADER , 0);
            $output = json_decode( curl_exec( $ch ) , true   );
            curl_close($ch);
            // dump( $output );
            if( !empty($output['code']) && 40003 ==  $output['code'] )
            {
                $obj = Array(
                        'status'=>1,
                        'desc'=>"禁止访问",
                        // 'result'=>[]
                        'result'=>$output['code']
                );   
                return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;
            }
            $catarr = $output['data']; 
            // dump($catarr);die;
            /**
             * 示例{
            "id": 515599,
            "name": "2018年短袖系列",
            "userId": 34004,
            "albumNumber": 0
             */
            
            $obj = Array(
                'status' => 0,
                'desc'   => '获取商品信息成功',
                'result' => array()
            );
            // print_r("fenlei");
            // dump( $catarr );
            $totalLength = 6 ;
            // $alb = $this->getAlbumByCatId( $catarr[2]['id'] ,  $token , $openId  );die;
            // die;
            foreach( $catarr as $key => $val )
            {
                // print_r( "a:" );print_r($key);
                if( $val['albumNumber'] > 0 )
                {

                    $alb = $this->getAlbumByCatId( $val['id'] ,  $token , $openId  );
                    // dump( $alb );
                    // if( $key == 1 ) die;
                
                    if( sizeof( $alb ) > $totalLength ) 
                        array_push( $obj['result'] , array('cateid'=>$val['id'] , 'catename'=>$val['name'], 'goods'=>array_slice($alb , 0 , $totalLength) ) );
                    else
                        array_push( $obj['result'] , array('cateid'=>$val['id'] , 'catename'=>$val['name'], 'goods'=>$alb) );
                }

            // dump( $obj );
            }
            return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
          // 根据分类id获取对应的相册，返还给前台
            



    }

    /**
     * API
     * [getAlbumByCatId 根据分类id 获取该分类下的相册]
     * @param  [int] $catId [description]
     * @return [array]        [[{"id":20084029,"name":"扫一扫加微信","cover":"\/2855775930_v\/815cc4bc\/da747216.jpg","type":"photo"] , [{"id":20084029,"name":"扫一扫加微信","cover":"\/2855775930_v\/815cc4bc\/da747216.jpg","type":"photo"]]
     */
    public function getAlbumByCatIdApi(  )
    {
       
         $req = Request::instance()->param();
         $catId = $req['catid'] ;  //分类id
         $ghsid = $req['ghsid'] ;  //供货商id
        // 1\判断是否已授权或者token是否过期
        $ghs  = new GhsMod();
        $res = $ghs->isExpireTokenYP( $req['ghsid'] );
       

        if( !$res )
        {
            $obj = Array(
                'status'=>1,
                'desc'=>"账号不存在、或token已过期",
                'result'=>$res
            );   
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;         
        }

        $ghs  = new GhsMod();
        $res  =$ghs->getGhsInfo( $ghsid );
        // $res_arr = $res[0];
       
        $useridYP = $res[0]['youpaiuserid'];
        $openId = $res[0]['youpaiopenid'];
        $token  = $res[0]['youpaitoken'];


        $data = $this->getAlbumByCatId( $catId , $token , $openId  );
        if( $data )
        {
            $obj = array(
                'status' => 0 ,
                'desc'   => '查询成功',
                'result' => $data
            );
        }
        else
        {
            $obj = array(
                'status' => 0 ,
                'desc'   => '查询成功',
                'result' => $data
            );
        }

        return  json_encode( $obj , JSON_UNESCAPED_UNICODE) ;
    }

    /**
     * [getAlbumByCatId 根据分类id 获取该分类下的相册]
     * @param  [int] $catId [description]
     * @return [array]        [[{"id":20084029,"name":"扫一扫加微信","cover":"\/2855775930_v\/815cc4bc\/da747216.jpg","type":"photo"] , [{"id":20084029,"name":"扫一扫加微信","cover":"\/2855775930_v\/815cc4bc\/da747216.jpg","type":"photo"]]
     */
    public function getAlbumByCatId( $catId , $token , $openId )
    {

        $arr = array( $token , '/category/'.$catId.'/all/openId=' , $openId , Config::get('YPAppKey') );
        // dump( $arr );
        $sign = md5( join("" , $arr) ) ;
        $requestUrl = Config::get('YPApi').'category/'.$catId.'/all?token='.$token.'&sign='.$sign.'&openId='.$openId ;
        
        // print_r( $requestUrl ); 
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_URL ,  $requestUrl );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
        curl_setopt( $ch , CURLOPT_HEADER , 0);
        // $output = curl_exec( $ch ) ;
        $output = json_decode( curl_exec( $ch ) , true   );
        curl_close($ch);
        // print_r( "--------------------" );
        // print_r("album".$catId);
        // dump( $output );
        // print_r( "--------------------" );
        // die;
        // print_r("fenli");
        // $data = $output['data'];
        if( array_key_exists('list',$output['data'] )  )
        {
            if( sizeof( $output['data']['list'] ) > 0 )
            {
                $data = $output['data']['list'];
            }
            else
            {
                return ;
            }
        }
        else
        {
            $data = $output['data'];
        } 
        // {
        // } 
        // else
        // {
        //     $data = $output['data']['list'];
        // }

        // $data = $output['data']['list'];
        // dump( $data );die;
        foreach( $data as $key=>$val )
        {
            // print_r( $val['cover'] );
            // print_r( $data[$key]['cover'] );
            $data[$key]['cover'] = Config::get('YPImageBaseUrl').$val['cover'];
            // $data[$key]['cover'] = join("" ,  array(Config::get('YPImageBaseUrl') , $data[$key]['cover']) );
            // print_r( $data[$key]['cover'] );
            //根据相册id判断相册是否在goods数据库内 
            $exists = Db::table('goods')->where('youpaialbumid',$val['id'])->select();
            // print_r("exists");
            // print_r($exists);
            if( $exists )
            {
                $data[$key]['exists'] = true;
            }
            else
            {
                $data[$key]['exists'] = false;
            }
        }
        // print_r("xiangc");
        // dump( $data );



        return $data;
    }


    /**
     * [tongBuYP 同步又拍相册]
     * @return [type] [description]
     */
    public function tongBuYP()
    {

        $req = Request::instance()->param();

        // 1、判断是否已授权或者token是否过期
        $ghs  = new GhsMod();
        $res = $ghs->isExpireTokenYP( $req['ghsid'] );
        // dump($res);

        if( !$res )
        {
            $obj = Array(
                'status'=>1,
                'desc'=>"账号不存在、或token已过期",
                'result'=>$res
            );   
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;         
        }

        //根据相册id判断相册是否在goods数据库内 
        $exists = Db::table('goods')->where( 'youpaialbumid' , $req['youpaialbumid'] )->select();

            // 2、根据相册id获取相册图片 
           $photos = $this->getPhotosByAlbumId( $req['ghsid'] , $req['youpaialbumid'] );

           // 3、创建商品
          $modObj = model('GoodsMod');

          // $insertedId = $modObj->tongBuYP( utf8_encode(bin2hex( $req['name'] )) ,""  , $req['ghsid'] , $req['youpaialbumid'] );
          // $insertedId = $modObj->tongBuYP( base64_encode($req['name']) ,""  , $req['ghsid'] , $req['youpaialbumid'] );
          $insertedId = $modObj->tongBuYP( $req['name'] ,""  , $req['ghsid'] , $req['youpaialbumid'] );
          // print_r( $insertedId );

          // 4、为商品添加图片
          $modObj = model('PhotoMod'); 
          $res = $modObj->addYPImage( $insertedId ,$photos , $req['youpaialbumid']  );
          $obj = array( 
            'status' => 0 ,
            'desc'   => '同步成功',
            'result' => null
          );
          return json_encode($obj , JSON_UNESCAPED_UNICODE);die;

    }

    /**
     * [updateYP 更新相册]
     * @param  [type] $youpaialbumid [相册id]
     * @return [type]                [description]
     */
    public function updateYP(   )
    {
        $req = Request::instance()->param();
        // $goodsid = $req['goodsid'] ;
        $ghsid = $req['ghsid'] ;
        $name = $req['name'] ;
        $desc = $req['desc'] ;
        $youpaialbumid = $req['youpaialbumid'] ;

        $goodsidArr = Db::table('goods')->where('youpaialbumid' , $youpaialbumid)->field('id')->select();
        $goodsid = $goodsidArr[0]['id'] ;

        //1、 根据供货商id和相册id获取又拍的相册信息
        $newphotos = $this->getPhotosByAlbumId( $ghsid , $youpaialbumid )  ;


        //2、查询已有的相册，并且删除 //
        $oldphotosArr = Db::table('photo')->where('youpaialbumid',$youpaialbumid)->select();
        // print_r( $oldphotosArr );
        $oldphotos = array();
        foreach( $oldphotosArr as $key=>$val )
        {
            array_push( $oldphotos , $val['url'] );
        }


        $c = array_intersect( $newphotos , $oldphotos );
        // print_r('交集');
        // dump( $c );die;

        $delete = array_diff( $oldphotos , $c );

        // print_r('delete');
        // print_r($delete);

        $insert = array_diff( $newphotos , $c );
        // print_r('insert');
        // print_r($insert);
        // 
        if( $delete )
        {
            $res = Db::table('photo')->where( 'url' ,  'in' , $delete  )->delete();

        }

        if( $insert )
        {

           $modObj = model('PhotoMod'); 
            $res = $modObj->addYPImage( $goodsid ,$insert , $req['youpaialbumid']  );
            
        }

        //更新商品的name、desc
        $res = Db::table('goods')->where(['id'=>$goodsid])->update(["name"=>$name,"desc"=>$desc]);
        $obj = array( 
                'status' => 0 ,
                'desc'   => '更新成功',
                'result' => null
             );
        return json_encode($obj , JSON_UNESCAPED_UNICODE);die;

    }



    /**
     * API
     * [getPhotosByAlbumId 根据相册id获取相册内的照片信息 ]
     * @param  [int] $ghsid   [供货商id]
     * @param  [int] $albumid [相册id]
     * @return [array] [1.jpg,2.jpg]
     */
    public function getPhotosByAlbumIdApi(  )
    {
      
        $req = Request::instance()->param();
        // dump( $req );die;
        $ghsid = $req['ghsid'];
        $albumid = $req['albumid'];
        
        //1、判断该相册是否已经存在
        $exists = false ;
        $res = Db::table('goods')->where('youpaialbumid' , $albumid)->select();
        // dump( $res );die;
        if( $res ) $exists = true;

        // $ghs  = new GhsMod();
        // $res  =$ghs->getGhsInfo( $ghsid );

        // $res_arr = $res[0];
        // $useridYP = $res_arr['youpaiuserid'];
        // $openId = $res_arr['youpaiopenid'];
        // $token  = $res_arr['youpaitoken'];

        // $arr = array( $token , '/albums/'.$albumid.'/detail/openId=' , $openId , Config::get('YPAppKey') );
        // dump( $arr );
        // $sign = md5( join("" , $arr) ) ;
        // $requestUrl = Config::get('YPApi').'albums/'.$albumid.'/detail?token='.$token.'&sign='.$sign.'&openId='.$openId ;
            
        // $ch = curl_init();
        // curl_setopt( $ch , CURLOPT_URL ,  $requestUrl );
        // curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
        // curl_setopt( $ch , CURLOPT_HEADER , 0);
        // // $output = curl_exec( $ch ) ;
        // $output = json_decode( curl_exec( $ch ) , true   );
        // curl_close($ch);
        // // print_r("album info");
        // dump( $output );
       
     
        // 2、根据相册id获取图片
        $res = $this->getPhotosByAlbumId( $req['ghsid'] , $req['albumid'] );
        // dump( $res );die;

        if( $res )
        {
            foreach( $res as $key=>$val )
            {
                $res[$key] = Config::get('YPImageBaseUrl').$val ;
            }
            $obj = array( 
                'status' => 0 ,
                'desc'   => '查询成功',
                'result' => array('exists'=>$exists , 'photos'=>$res)
             );
        }
        else
        {
             $obj = array( 
                'status' => 1 ,
                'desc'   => ' 查询失败',
                'result' => null
             );
        }

        // dump( $res );
        return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;
       

    }

    /**
     * [getPhotosByAlbumId 根据相册id获取又拍相册内的照片信息 ]
     * @param  [int] $ghsid   [供货商id]
     * @param  [int] $albumid [相册id]
     * @return [array] [1.jpg,2.jpg]
     */
    public function getPhotosByAlbumId( $ghsid , $albumid )
    {
      
        $ghs  = new GhsMod();
        $res  =$ghs->getGhsInfo( $ghsid );

        $res_arr = $res[0];
        $useridYP = $res_arr['youpaiuserid'];
        $openId = $res_arr['youpaiopenid'];
        $token  = $res_arr['youpaitoken'];

        $arr = array( $token , '/albums/'.$albumid.'/photos/all/openId=' , $openId , Config::get('YPAppKey') );
        // dump( $arr );
        $sign = md5( join("" , $arr) ) ;
        $requestUrl = Config::get('YPApi').'albums/'.$albumid.'/photos/all?token='.$token.'&sign='.$sign.'&openId='.$openId ;
        
        // print_r( $requestUrl ); 
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_URL ,  $requestUrl );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
        curl_setopt( $ch , CURLOPT_HEADER , 0);
        // $output = curl_exec( $ch ) ;
        $output = json_decode( curl_exec( $ch ) , true   );
        curl_close($ch);
        // dump( $output );
        // die;

        $photos = array();
        foreach( $output['data']['list'] as $key=>$item )
        {
            array_push($photos, $item['path']);
        }

        return $photos;

    }

    
 

    /**
     * 同步供货商照片
     * @param  [type] $ghsid [供货商id]
     * @return [type]        [description]
     */
    // public  function tongBuYP( $ghsid )
    // public  function tongBuYP(  )
    // {
        // 同步又拍相册
        // 1、获取用户在数据库中的所有相册,存储在数组里
        // $goods = $this->getAllAlbumByUserid();
        // 2、查询用户的又拍相册，根据相册id判断是否是新的相册
        // $modObj = model( 'GoodsMod' );
        // $ypalbum = $modObj->getGoodsListByGhsId( 2  , 2 );
        // return $res ;
        // 3、如果是新的相册，则直接作为新的商品录入数据库
        // 3.1  找出哪些相册id是新的
            //(a)、初始化两个数组。$YPAlbumIds -  又拍商品的相册id组成的数组 、 $GoodsModIds -  数据库内的商品id组成的数组
            // foreach( $ypalbum as $key=>$value )
            // {
            //     array_push( $YPAlbumIds , $value['某一个值'] );
            // }

            // foreach( $goods as $key=>$value )
            // {
            //     array_push( $GoodsModIds , $value['某一个值'] );
            // }

            //(b)又拍数组减去数据库数组
            // $newAlbumIds = array_diff( $YPAlbumIds , $goods ) ;
            
            //(c)根据这些id去获取对应的照片数组
            
            //(d)将这些新的相册数据导入商品数据库

        // 4、如果该相册已存在，则判断updatetime是否一致，如果不一致，说明该相册需要更新
    // }



    
    /**
     * [获取又拍用户的token和opendi]
     * @param  [string] $token  [description]
     * @param  [string] $openid [description]
     * @return [type]         [description]
     */
    // public function getUserIdYP( $token , $openid  )
    // public function getUserIdYP( )
    // {   
    //     $token = 'dfdfdfs';
    //     $openid = '121514512';
    //     $arr = array( $token , '/account/general/openId=' , $openid , Config::get('YPAppKey') );
    //     print_r($arr);
    //     $sign = md5( join($arr) ) ;
    //     echo $sign ;

    //     // 将userid，openid，token写入数据库
    //     $GhsCon = controller( 'Ghs' );
    //     $res = $GhsCon->updateYouPAi();
    //     return $res ;

    // }

    /**
     * 根据用户的又拍userid，获取用户的相册 , 每页显示120条
     * @param  [type] $useridYP [description]
     * @return [type]           [description]
     */
    // public function getAllAlbumByUserid( $useridYP )
    // public function getAlbumByUserid( )
    // {
    //     $useridYP = '34004';
    //     $ch = curl_init();
    //     curl_setopt( $ch , CURLOPT_URL , Config::get('YPApi').'web/users/'.$useridYP.'/albums?page=1' );
    //     curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
    //     curl_setopt( $ch , CURLOPT_HEADER , 0);
    //     $output = json_decode( curl_exec( $ch ) , true   );
    //     curl_close($ch);
    //     echo "<pre>";
    //     print_r($output)   ;
    //     echo "</pre>";
    //     die;

    // }


    // public function getAllAlbumByUserid()
    // {
    //     $useridYP = '34004';
    //     $token = 'dfdfdfs';
    //     $openId = '121514512';
    //     $arr = array( $token , '/albums/'.$useridYP.'/all' , $openId , Config::get('YPAppKey') );
    //     print_r($arr);
    //     $sign = md5( join($arr) ) ;
    //     // echo $sign ;
    //     $requestUrl = Config::get('YPApi').'/albums/'.$useridYP.'/all'.'?token='.$token.'&sign='.$sign.'&openId='.$openId ;

    //     echo $requestUrl ;die;
    //     $ch = curl_init();


    //     curl_setopt( $ch , CURLOPT_URL ,  $requestUrl );
    //     curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
    //     curl_setopt( $ch , CURLOPT_HEADER , 0);
    //     $output = json_decode( curl_exec( $ch ) , true   );
    //     curl_close($ch);
    //     echo "<pre>";
    //     print_r($output)   ;
    //     echo "</pre>";
    //     die;
    // }

    /**
     * 根据相册id获取对应的图片
     * @param  [type] $albumIds [description]
     * @return [type]           [description]
     */
    // public function getPhotosByAlbumId( $albumIds )
    // {

    // }
    


    

}

?>
