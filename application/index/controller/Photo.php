<?php

namespace app\index\controller;
Use think\Controller;
use think\File;
Use think\Config;
Use think\Request;
Use app\index\model\PhotoMod;

class Photo extends Base
{
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // dump(request());die;
        // return json_encode( ['aa'=>9]  , JSON_UNESCAPED_UNICODE );die;
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/uploads');
            // $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');

            if($info){
                // return json_encode(array('name'=>0) ,JSON_UNESCAPED_UNICODE );die;
                // 成功上传后 获取上传信息
                // 输出 jpg
                // echo $info->getExtension(); 
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getFilename();

                $obj['result'] = array
                    ( 
                        'shortUrl'=>$info->getFilename() , 
                        'longUrl'=>Config::get('ImageBaseURL').$info->getFilename() 
                    ) 
                 ;



            }else{
                // return json_encode(array('name'=>9) ,JSON_UNESCAPED_UNICODE );die;
                // 上传失败获取错误信息
                echo $file->getError();
            }

            $obj['status']  = 0 ;

            return json_encode( $obj , JSON_UNESCAPED_UNICODE) ;die;


            // if($info){
            //     // 成功上传后 获取上传信息
            //     // 输出 jpg
            //     echo $info->getExtension();
            //     // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //     echo $info->getSaveName();
            //     // 输出 42a79759f284b767dfcb2a0197904287.jpg
            //     echo $info->getFilename(); 
            // }else{
            //     // 上传失败获取错误信息
            //     echo $file->getError();
            // }
        }
        else
        {
            
            return json_encode( array('name'=>999) , JSON_UNESCAPED_UNICODE) ;die;
        }
    }
   
     public function uploadMulti()
     {
        //     echo "<pre>";
        //     var_dump( request()->file('image') );
        //     echo "</pre>";
        // die;
        // 获取表单上传文件
        $files = request()->file('image');
        dump(request());die;
        $obj = array(
            'result'=>[],
            'status'=>null
        );
        foreach($files as $file)
        {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                // echo $info->getExtension(); 
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getFilename();

                array_push( $obj['result'] , array
                    ( 
                        'shortUrl'=>$info->getFilename() , 
                        'longUrl'=>Config::get('ImageBaseURL').$info->getFilename() 
                    ) 
                ) ;



            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }

        }

        $obj['status']  = 0 ;

        return json_encode( $obj , JSON_UNESCAPED_UNICODE) ;die;     
    }

      /**
     * 本地图片地址加入数据库
     * @param [int] $[goodid] [<商品id>]
     * @param [array] $[imgurls] [<图片名字数组>]
     * @return [type] [description]
     */
    // public function addLocalImage( $goodid ,  $imgurls)
    public function addLocalImage( $goodid,$urls )
    {
        // $req = Request::instance()->param();

        $modelObj  = new PhotoMod();
        return $modelObj->addLocalImage( $goodid , $urls );
        // return $modelObj->addLocalImage( 1 , ['5c074307c4388.jpg']);
        // $res = $modelObj->run( Config::get('ImageServerURL').'5c074307c4388.jpg' , Config::get('ImageServerURL').'5c074307c4490.jpg' );
        
        // $res = $modelObj->run( Config::get('ImageServerURL').'5c074307c4388.jpg' , Config::get('ImageServerURL').'5c074307c4388.jpg' );
        // var_dump( !!$res );
    }

    /**
     * 本地图片地址加入数据库
     * @param [int] $[imgname] [<图片上传之后的名称>]
     * @param [int] $[ghsid] [<供货商id>]
     * @return [type] [description]
     */
    public function searchByImage( $file )
    {   

        $modelObj  = new PhotoMod();
        

        // 1、获取图片对象
        // $file = request()->file('image');
        

        //2、 保存图片，移动到框架应用根目录/public/uploads/tmp 目录下
        if($file)
        {
            $info = $file->rule('uniqid')->move(Config::get('ImageTmpURL'));
            if($info)
            {
                $imgName = $info->getFilename();
                $hash =  $modelObj->getHashValue( Config::get('ImageTmpURL').$imgName );

                //将临时图片删除，节省空间
                unlink ( Config::get('ImageTmpURL').$imgName );

                // print_r( $hash );
                //3、在数据库内查询相同的图片对应的商品id
                // return  $modelObj->getGoodsIdByImgHash('1111111111111111111111111111111111111111111111111111111111111111');die;
                $res = $modelObj->getGoodsIdByImgHash($hash);
                if( empty( $res ) )
                {
                    $obj = [ "status"=>1,'desc'=>'未找到','result'=>[] ];
                }
                else
                {
                    $obj = [ "status"=>0,'desc'=>'找到了','result'=>$res ];

                }
                return json_encode($obj , JSON_UNESCAPED_UNICODE);

                die;


                // $imagesHash = $modelObj->getImageByHash( $hash );
                // echo "hash".$imagesHash ;


                // 成功上传后 获取上传信息
                // 输出 jpg
                // echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getFilename(); 
            }
            else
            {
                // 上传失败获取错误信息
                return $file->getError();
            }
        }

         

         //4、调用Goods控制器，根据goodid查询商品信息
         // $goodObj = controller('Good');

         
    }

    /**fgetImagesByGoodId
    
     * 根据商品id查找对应的图片
     * @param  [int] $goodid [商品id数组]
     * @return [type]          [description]
     */
    public function getImagesByGoodId( $goodsid )
    {
        $modelObj  = new PhotoMod();
        $res = $modelObj->getImagesByGoodId( $goodsid );
        // print_r("photo");
        // dump($res);die;
        $resArr = json_decode($res , true);

        // print_r("photo");
        // print_r(  $resArr );die;
        // if( is_array($resArr['result']) )
        // {
        //     $resArr['result'] = array_map( [$this,'fillImageFullPath'] , $resArr['result'] );
        // }
        // else
        // {
        //     $resArr['result'] = [] ;
        // }
        // return $modelObj->getImagesByGoodId( $goodid );
        return json_encode( $resArr , JSON_UNESCAPED_UNICODE ) ; die;
    }

    /**
     * 将图片的名字加上外部访问路径
     */
    public function fillImageFullPath( $url )
    {
        // echo Config::get('ImageBaseURL').$url ;
        return Config::get('ImageBaseURL').$url ;
    }

    // public function test()
    // {
    //     $modelObj  = new PhotoMod();
    //     return $modelObj->getGoodsIdByImgHash('1111111111111111111111111111111111111111111111111111111111111111');
    //     die;
    // }
   
 
 

}

?>
