<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/2
 * Time: 10:57
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends Controller
{
    /**
     * @var \Admin\Model\AdminModel
     */
    //0.创本表类
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Admin');
    }
    //1.首页展示管理员
    public function index(){
        //模糊查询
        $name=I('get.name');
        $cond=[];
        if($name){   //如果查询存在
            $cond['username']=['like','%'.$name.'%'];
        }
        //调用getPageRe方法查询本表数据并以分页展示
        $rows=$this->_model->getPageRe($cond);
        $this->assign($rows);  ///!!!!????
        $this->display();
    }

    //2.添加管理员
    public function add(){
        if(IS_POST){
            if($this->_model->create('','register')===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->addAdmin()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            $this->bView();
            $this->display();
        }

    }

    //3.编辑管理员
    public function edit($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->saveAdmin($id)===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('编辑成功',U('index'));
            }
        }else{
            //查找admin表与admin_role表信息返回
            $row=$this->_model->getAdminInfo($id);
            $this->assign('row',$row);
            $this->bView();
            $this->display('add');
        }
    }

    //4.删除管理员
    public function remove($id){
        if($this->_model->deleteAdmin($id)===false){
            $this->error('删除失败',U('index'));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    //获取角色表信息,以json返回,让html页面显示
    public function bView(){
        $rmodel=D('Role');
        $roles=$rmodel->getList();
        $this->assign('roles',json_encode($roles));
    }

    //密码重置
    public function reset($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $rp=$this->_model->repwd($id);
                $this->success('新密码为'.$rp.',请牢记!',U('index'),10);
            }else{
                $cp=$this->_model->cpwd($id);
                $this->success('新密码为'.$cp.',请牢记!',U('index'),10);
            }
        }else{
            //展示页面
            $row=$this->_model->find($id);
            $this->assign('row',$row);
            $this->display('re');
        }
    }

    //5.登录界面
    public function login(){
        if(IS_POST){
            if($this->_model->create()===false){   //登陆时验证
                $this->error(getErr($this->_model));
            }
            if($this->_model->adminLogin()===false){
                $this->error(getErr($this->_model));
            }else{
                //登录成功后跳转到Index控制器下的index页面
                $this->success('登录成功',U('Index/index'));
            }
        }else{
            $this->display();
        }
    }

    //写退出功能:清空session和cookies
    public function logout(){
        session(null);
        cookie(null);
        $this->success('退出成功',U('login'));
    }

}