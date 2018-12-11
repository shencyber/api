<?php

namespace app\index\controller;
Use think\Controller;
use \think\File;
Use think\Config;
Use app\index\model\PhotoMod;

class Photo extends Controller
{
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename(); 
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
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
     * @param [int] $[dlsid] [<代理商id>]
     * @param [int] $[ghsid] [<供货商id>]
     * @return [type] [description]
     */
    // public function addLocalImage( $goodis ,  $imgurls)
    public function addLocalImage( )
    {
        $modelObj  = new PhotoMod();
        // return $modelObj->addLocalImage( 1 , ['5c074307c4490.jpg'] );
        // return $modelObj->addLocalImage( 1 , ['5c074307c4388.jpg']);
        $res = $modelObj->run( Config::get('ImageServerURL').'5c074307c4388.jpg' , Config::get('ImageServerURL').'5c074307c4490.jpg' );
        
        var_dump( !!$res );
        // return $modelObj->run( Config::get('ImageServerURL').'5c074307c4388.jpg' , Config::get('ImageServerURL').'5c074307c4388.jpg' );
    }

    /**
     * 本地图片地址加入数据库
     * @param [int] $[dlsid] [<代理商id>]
     * @param [int] $[ghsid] [<供货商id>]
     * @return [type] [description]
     */
    // public function addLocalImage( $goodis ,  $imgurls)
    // public function addLocalImage( )

   
 
 

}

?>
