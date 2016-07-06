<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/27
 * Time: 22:32
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends Controller
{
    //建这张表的model
    /**
     * @var \Admin\Model\GoodsCategoryModel
     */
    private $_model=null;
    protected function _initialize(){
        $this->_model = D('GoodsCategory');
    }
     //1.显示goods分类界面
    public function index(){
        $rows=$this->_model->getList();
        $this->assign('rows',$rows);
        $this->display('index');
    }
    //2.添加
    public function add(){
        if(IS_POST){//添加数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addCategory() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功',U('index'));

        }else{//显示页面
            //ztree接收的是json数据，所以要转化下
            $this->dingView();
            $this->display();
        }
    }
    //编辑分类
    public function edit($id) {
        if (IS_POST) {
            //收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->saveCategory() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('编辑成功',U('index'));
        } else {
            //展示数据
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            //获取所有的分类
            $this->dingView();
            $this->display('edit');
        }
    }
    //删除分类
    public function remove($id) {
        if($this->_model->deleteCategory($id)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }
    //给添加分类中的下拉框加个顶级分类,供添加进数据库
    private function dingView(){
        //得到所有的查询数据
        $goods_categories =$this->_model->getList();
        //添加顶级分类进入数据中
        array_unshift($goods_categories,['id'=>0,'name'=>'顶级分类','parent_id'=>0]);
        //在把值转化为json后传回去
        $goods_categories=json_encode($goods_categories);
        $this->assign('goods_categories',$goods_categories);

    }



}