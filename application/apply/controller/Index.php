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
use think\Request;

class Index extends Base
{

    public function __construct(Request $request)
    {
        session_start();
        parent::__construct($request);

    }

    public function index()
    {
        if (isset($_SESSION['user'])) {
            $data = Db::table("app_addon")->paginate(5);/*dump($data);*/
            $this->assign('data', $data);
            return $this->view->fetch('index');
        } else {
            return $this->view->fetch('/user/login');
        }
    }
}