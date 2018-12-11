<?php
namespace app\index\controller;
Use think\Controller;
Use app\index\model\Ghs;
Use app\index\model\User;

class Index extends Controller
{
    public function index()
    {

        // header('Access-Control-Allow-Origin:*');  
        // // 响应类型  
        // header('Access-Control-Allow-Methods:*');  
        // // 响应头设置  
        // header('Access-Control-Allow-Headers:x-requested-with,content-type');  

        // $date=date_create("2018-12-05T14:08:59.000Z");
        // echo date_format( date_create("2018-12-05T14:08:59.000Z") ,"Y-m-d H:i:s");
        // die;
        // 响应头设置  
        header('Access-Control-Allow-Origin:*');  
        // 响应类型  
        header('Access-Control-Allow-Methods:*');  
        // 响应头设置  
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        header("Content-type: text/html; charset=utf-8");         
        // print_r( $_POST['name'] );die;
     //    $data = file_get_contents('php://input');
     //    // dump( $data );die;
     //    $res  = json_decode($data , true);

    	// print_r( $res  );
     //    die();
    	// // return ( json_encode( $_POST ) );
     //    return 'ok';
    }

   
}

?>
