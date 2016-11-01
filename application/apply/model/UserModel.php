<?php
/**
 * Created by PhpStorm.
 * User: gsy
 * Date: 2016/10/28
 * Time: 16:01
 */

namespace app\apply\model;

use think\Model;

class UserModel extends Model
{
    public $table = "app_user";

    //自定义初始化
    protected function initialize()
    {
        parent::initialize();
    }

    //检测用户名是否已注册
    public function userName($name)
    {
        if (empty($name)) {
            return false;
        }
        $result = db('user')->field('fname')->where('fname', $name)->select();
        if (!empty($result)) {
            return true;
        }
    }

    //添加注册用户
    public function addUser($info)
    {
        if (empty($info)) {
            return false;
        }
        $result = db('user')->insert($info);
        return $result;
    }


}