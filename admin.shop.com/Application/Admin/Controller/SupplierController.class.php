<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/24
 * Time: 16:14
 */

namespace Admin\Controller;


use Think\Controller;

class SupplierController extends Controller
{
    //?建公用model

    //1.显示供应商管理页面
     public function index(){
         $model=D('Supplier');
         //写搜索功能
         $key=I('get.keyword');
         $cond['status']=['egt',0];
         if($key){
             $cond['name']=['like','%'.$key.'%'];
         }
         //分页显示数据status>=0的数据
         $rows=$model->getPageRe($cond);
         $this->assign($rows);//只能给1个$rows传二维数组过去
         $this->display('index');
     }

    //2.添加供应商
    public function add(){
        $model=D('Supplier');
        if(IS_POST){   //如果有传送数据，则添加
            if($model->create()===false){
                $this->error(getErr($model));
            }
            //保存数据,提示跳转
            if($model->add()===false){
                $this->error(getErr($model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{     //显示
            $this->display('add');
        }
    }
    //3.编辑供货商
    public function edit($id){
        $model=D('Supplier');
        if(IS_POST){
            if($model->create()===false){
                $this->error(getErr($model));
            }
            if($model->save()===false){
                $this->error(getErr($model));
            }else{
                $this->success('编辑成功',U('index'));
            }
        }else{
            $row=$model->find($id);
            $this->assign('row',$row);
            $this->display('edit');
        }
    }
    //4.删除供货商
    public function remove($id){
//        物理删除：直接删除表中数据
//        $model=D('Supplier');
//        if($model->delete($id)===false){
//            $this->error(getErr($model));
//        }else{
//            $this->success('删除成功',U('index'));
//        }
        //逻辑删除：库中数据不删，但隐藏数据
        $model=D('Supplier');
        //写删除条件
        $cond=['id'=>$id,'status'=>-1,'name'=>['exp','concat(name,"_del")']];//让状态为-1后就不会显示了，位置字段name加_del
        //执行删除
        if($model->setField($cond)===false){
            $this->error(getErr($model));
          }else{
            $this->success('删除成功',U('index'));
         }
    }
}