<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/25
 * Time: 9:08
 */

namespace Admin\Controller;


use Think\Controller;

class BrandController extends Controller
{
    //0.建公用model
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Brand');
    }
    //1.显示界面
    public function index(){
        //搜索功能
        $key=I('get.keyword');
        $cond['status']=['egt',0];//显示的数据
        if($key){
            $cond['name']=['like','%'.$key.'%'];
        }
        $rows=$this->_model->getPageRe($cond);
        $this->assign($rows);
        $this->display('index');
    }
    //2.添加品牌
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                  $this->error(getErr($this->_model));
            }
            if($this->_model->add()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            $this->display('add');
        }
    }
    //3.编辑品牌
    public function edit($id){
        //1.检测post两面 2.判断接收一面 3.判断操作两面
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error($this->_model);
            }
            if($this->_model->save()===false){
                $this->error($this->_model);
            }else{
                $this->success('编辑成功',U('index'));
            }
        }else{
            $row=$this->_model->find($id);
            $this->assign('row',$row);
            $this->display('edit');
        }
    }
    //4.品牌删除(逻辑)
    public function remove($id){
        //定义条件
        $cond=['id'=>$id,'status'=>-1,'name'=>['exp','concat(name,"_del")']];
        if($this->_model->setField($cond)===false){
            $this->error($this->_model);
        }else{
            $this->success('删除成功',U('index'));
        }
    }

}