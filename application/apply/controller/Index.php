<?php
/**
 * Created by PhpStorm.
 * User: 黑子
 * Date: 2016/10/20
 * Time: 16:25
 */
namespace app\apply\controller;
use app\common\controller\Base;
use think\Db;
use OSS\OssClient;
class Index extends Base{


    public function index(){
        $data = Db::table("app_addon")->paginate(5);/*dump($data);*/
        $this->assign('data',$data);

        return $this->view->fetch('index');
    }
}