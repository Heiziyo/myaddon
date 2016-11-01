<?php
/**
 * Created by PhpStorm.
 * User: gsy
 * Date: 2016/10/28
 * Time: 09:11
 */

namespace app\apply\controller;

use app\apply\model\UserModel;
use app\common\controller\Base;
use app\common\helper\VerifyHelper;
use think\Request;

class User extends Base
{


    public function register()
    {
        return $this->view->fetch();
    }

    public function login()
    {
        return $this->view->fetch();
    }

    public function forget()
    {
        return $this->view->fetch();
    }

    /**
     * 显示验证码图片
     */
    public function verify()
    {
        VerifyHelper::verify();
    }

    /**
     * 注册页逻辑
     */
    public function doRegister()
    {
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $post['Fname'] = isset($_POST['name']) ? stripslashes(trim($_POST['name'])) : '';
        $post['Femail'] = isset($_POST['email']) ? trim($_POST['email']) : '';
        //验证密码
        if (strlen($_POST['password']) < 6) {
            $this->error('登录密码最少6个字符哟~');
        }
        $post['Fpassword'] = isset($_POST['password']) ? md5(trim($_POST['password'])) : '';
        //注册时间
        $post['Fregtime'] = time();
        //创建激活码
        $post['Ftoken'] = md5($post['Fname'].$post['Fpassword'].$post['Fregtime']);
        //激活码过期时间限制
        $post['FtokenExptime'] = time() + 3600*24;
        $user_model = new UserModel();
        //验证用户名
        if (strlen($post['Fname']) < 3) {
            $this->error('用户名最少3个字符哟~');
        }
        $check_name = $user_model->userName($post['Fname']);
        if ($check_name) {
            $this->error('该用户名已被注册，换一个吧~');
        }
        //验证验证码
        $check_code = VerifyHelper::check($code);
        if(!$check_code) {
            $this->error('验证码错误');
        }
        //创建注册信息
        $add_user = $user_model->addUser($post);
        if ($add_user) {
            require_once 'class.phpmailer.php';
            include 'class.smtp.php';
            $mail = new \PHPMailer();
            $mail->IsSMTP();
            //smtp需要鉴权 这个必须是true
            $mail->SMTPAuth = true;
            // 设置邮件的字符编码，若不指定，则为'UTF-8'
            $mail->CharSet = 'UTF-8';
            $mail->IsHTML(true);
            // 设置邮件正文
            $mail->Body = "亲爱的".$post['Fname'].":<br/>欢迎您加入 LvyeCMS! 开放平台，您的账号需要邮箱认证，点击下面链接进行认证：<br/> <a href='http://".$_SERVER["HTTP_HOST"]."/apply/user/active/verify/".$post['Ftoken']."' target='_blank'>http://".$_SERVER["HTTP_HOST"]."/apply/user/active/verify/".$post['Ftoken']."</a><br/>如果链接无法点击，请完整拷贝到浏览器地址栏里直接访问，该链接24小时内有效。
                                                    邮件服务器自动发送邮件请勿回信!";
            // 设置发件人邮箱
            $mail->From = "gsy@yougou-shop.com";
            // 设置发件人名字
            $mail->FromName = 'LvyeCMS';
            // 设置邮件标题
            $mail->Subject = '用户账号激活';
            // 设置SMTP服务器。
            $mail->Host = 'smtp.exmail.qq.com';
            //设置使用ssl加密方式登录鉴权
            $mail->SMTPSecure = 'ssl';
            // SMTP服务器的端口号
            $mail->Port = 465;
            // 设置用户名和密码。
            $mail->Username = 'gsy@yougou-shop.com';
            $mail->Password = '123456';
            //收件人地址
            $mail->AddAddress($post['Femail']);
            if (!$mail->Send()) {
                echo "发送失败：" . $mail->ErrorInfo;
            } else {
                $this->error("注册成功，快去邮箱验证吧~","/apply/user/login");
            }
        }
    }

    /**
     * 邮箱验证
     */
    public function active() {
        $verify = Request::instance()->param("verify");
        echo $verify;
    }

}