<?php
namespace app\index\controller;
Use think\Controller;
Use app\index\model\Ghs;
Use app\index\model\User;

class Index extends Controller
{
    public function index()
    {
    	// flush();
    	// ob_flush();
    	// ob_end_clean();
    	// return "df";
       return json_encode(array('name'=>'ddd') , JSON_UNESCAPED_UNICODE);
    }

   
}

?>
