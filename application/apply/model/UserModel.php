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

    //匹配激活码，比对时间
    public function checkTime($token)
    {
        $result = db('user')->field('ftokenexptime')->where("ftoken", $token)->select();
        if (!empty($result)) {
            return $result[0]['ftokenexptime'];
        }
    }

    //激活账号
    public function activation($token)
    {
        $result = db('user')->where("ftoken", $token)->setField('Fstatus', 1);
        return $result;
    }

    //匹配用户
    public function login($name, $pwd)
    {
        $result = db('user')->field('fid')->where("fname", $name)->where("fpassword", $pwd)->select();
        return $result[0]['fid'];
    }

    //检测邮箱是否激活
    public function email($id)
    {
        $result = db('user')->field('fstatus')->where("fid", $id)->select();
        if (empty($result[0]['fstatus'])) {
            return true;
        }
    }
}

