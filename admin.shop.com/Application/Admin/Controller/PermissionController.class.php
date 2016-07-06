<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/1
 * Time: 16:45
 */

namespace Admin\Controller;
use Think\Controller;

class PermissionController extends Controller
{
    /**
     * @var \Admin\Model\PermissionModel
     */
    //0.建本表类
    private $_model=null;
    protected function _initialize(){
       $this->_model=D('Permission');
     }
    //1.权限显示首页
    public function index(){
        //回显数据:获取所有的权限列表
        $rows = $this->_model->getList();
        $this->assign('rows', $rows);
        $this->display();

    }
    //2.添加权限
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->addP()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            //准备父级权限,回显
            $this->bView();
            $this->display();
        }

    }
    //3.编辑权限
    public function edit($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->saveP()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('编辑成功',U('index'));
            }
        }else{
            //获取本表数据,传递给页面
            $row = $this->_model->find($id);
            $this->assign('row',$row);
            //给ztree使用的数据
            $this->bView();
            $this->display('add');
        }
    }
    //4.删除权限
   public function remove($id){
       if($this->_model->deleteP($id)===false){
           $this->error(getErr($this->_model));
       }else{
           $this->success('删除成功',U('index'));
       }

    }


   //查询ztree数据,以json返回
   public function bView(){
       //查询permission表中数据
       $row=$this->_model->getList();
       //加入顶级权限
       array_unshift($row,['id'=>0,'name'=>'顶级权限','parent_id'=>null]);
       //用json返回到html页面
       $this->assign('permissions',json_encode($row));
   }





}