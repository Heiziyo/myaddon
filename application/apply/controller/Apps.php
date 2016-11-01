<?php
/**
 * Created by PhpStorm.
 * User: 黑子
 * Date: 2016/10/24
 * Time: 9:49
 */

namespace app\apply\controller;

use app\common\controller\Base;
use think\Request;
use think\Db;
use app\apply\model\MyappModel;
use app\apply\model\VersionModel;
class Apps extends Base{

    protected function initialize()
    {

        parent::initialize();

    }
    //添加
    public function addAddon(){
        $type = Request::instance()->param("typeid");
        if (!isset($type)){
            $this->error("非法访问");
            return false;
        }
        $typearr = array(0,1,2);
        if (!in_array($type,$typearr)){
            $this->error('对不起，您无权创建此类型的应用');
            return false;
        }
        if (Request::instance()->isPost()){
            $myappmodel = new MyappModel();
            $data = Request::instance()->post();

            if (!empty(Request::instance()->file())){
                $pre_thumb = $this->upload('pre_thumb');
                $thumb = $this->upload('thumbs');
            }else{
                $this->error("请添加上传文件");
            }
            $data['pre_thumb'] = $pre_thumb;
            $data['thumbs'] = serialize($thumb);
            $data['dateline'] = time();
            $data['type'] = $data['types'];
            unset($data['types']);
            $result = $myappmodel->addData($data);
            if ($result){

            }else{
                $this->error("添加失败");
            }
        }else{
            $this->assign("typeid",$type);
            return $this->view->fetch('addAddon');
        }
    }

    /*
     * 版本管理
     */
    public function versionManagement(){
        $id = Request::instance()->param("id");
        if(empty($id)){
            $this->error("不存在");
        }
        //查询当前包信息
        $info = Db::table("app_addon")->where('id',$id)->find();
        $data = Db::table("app_versionmanagement")->where('pid',$id)->select();

        $this->assign('info',$info);
        $this->assign("data",$data);
        return $this->view->fetch("versionManagement");
    }

    /*
         *
         * 创建版本
         */
    public function createVersion(){
        if (Request::instance()->isPost()){
            if (Request::instance()->post()){
                if (Request::instance()->file()){
                    $data = Request::instance()->post();
                    $filepath = $this->fileUp('filenamenew');
                    $data['filenamenew'] = $filepath;
                    $data['charsetnew'] = serialize($data['charsetnew']);
                    if (!empty($data)){
                        $version = new VersionModel();
                        $result = $version->addData($data);
                        if ($result){
                            $this->success("添加成功");
                        }
                    }
                }else{
                    $this->error("请上传安装包");
                }
            }
        }else{
            $id = Request::instance()->param("id");
            if(empty($id)){
                $this->error("不存在");
            }
            //查询当前包信息
            $info = Db::table("app_addon")->where('id',$id)->find();
            //查询程序版本信息
            $version = Db::table("app_version")->select();
            $this->assign('info',$info);
            $this->assign('version',$version);
            $this->assign('pid',$id);
            return $this->view->fetch("createVersion");
        }
    }





    //图片上传
    /*
     * parm  string $image_t  图片标识
     *
     * */
    private function upload($image_t){
        $files = request()->file($image_t);
        $filepath = ROOT_PATH . 'public' . DS . 'uploads';
        if (is_array($files)){
            $thumb = "";
            foreach($files as $k=>$file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move($filepath);
                if($info){
                    $thumb[$k] =  $info->getSaveName();
                }else{
                    return $file->getError();
                }
            }
            return $thumb;
        }else{
            $info = $files->move($filepath);
            if($info){
                return $info->getSaveName();
            }else{
                return $files->getError();
            }
        }
    }
    /*
    * 文件上传
    */
    public function fileUp($filename){
        $files = request()->file($filename);
        $filepath = ROOT_PATH . 'public' . DS . 'filepubic';
        $info = $files->move($filepath);
        if($info){
            return $info->getSaveName();
        }else{
            return $files->getError();
        }
    }
}
