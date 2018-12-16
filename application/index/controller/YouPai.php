<?php

namespace app\index\controller;
Use think\Controller;
Use think\Config;
Use \think\Request;
Use app\index\model\GhsMod;

class Youpai extends Base
{

    /**
     * 获取又拍相册所有分类
     * @param  [type] $ghsid [供货商id]
     * @return [type]        [description]
     */
    public function getALLCat()
    {
        $req = Request::instance()->param();
        $ghs  = new GhsMod();
        $res  =$ghs->getGhsInfo( $req['ghsid'] );
        $res_arr = json_decode($res , true);
        // print_r($res_arr['result']);
        if( 0 != $res_arr['status'] ) 
        {
            exit( $res );
        }
        else
        {
            $useridYP = $res_arr['result']['youpaiuserid'];
            $openId = $res_arr['result']['youpaiopenid'];
            $token  = $res_arr['result']['youpaitoken'];
            $token ='b63ba5b0582ca04fae182941daaa3b40c81de918' ;
            
            $arr = array( $token , '/category/openId=' , $openId , Config::get('YPAppKey') );
            $sign = md5( join("" , $arr) ) ;
            $requestUrl = Config::get('YPApi').'category?token='.$token.'&sign='.$sign.'&openId='.$openId ;
            $ch = curl_init();

            curl_setopt( $ch , CURLOPT_URL ,  $requestUrl );
            curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
            curl_setopt( $ch , CURLOPT_HEADER , 0);
            $output = json_decode( curl_exec( $ch ) , true   );
            curl_close($ch);
            $catarr = $output['data']; 
            /**
             * 示例{
            "id": 515599,
            "name": "2018年短袖系列",
            "userId": 34004,
            "albumNumber": 0
             */
            
            $obj = Array(
                'status' => 0,
                'desc'   => '获取所有分类',
                'result' => $catarr 
            );

            return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
          // 根据分类id获取对应的相册，返还给前台
            

        }


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
