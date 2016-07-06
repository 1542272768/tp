<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/2
 * Time: 22:00
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends Controller
{
    //利用权限设置登录者可以看到的菜单,这里做的的是设置权限可以看到的的菜单
    /**
     * @var \Admin\Model\MenuModel
     */
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Menu');
    }
    //展示首页
    public function index(){
        //查询本表显示数据
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }

    //添加权限对应的菜单
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->addMenu()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            //查出菜单和权限返回
            $this->bView();
            $this->display();
        }

    }

    //编辑
    public function edit($id){
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(getErr($this->_model));
            }
            if ($this->_model->saveMenu() === false) {
                $this->error(getErr($this->_model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //查询本表中文数据进行回显
            $this->bView();
            //查询它表ID数据进行回显
            $row = $this->_model->getMenuInfo($id);
            $this->assign('row', $row);
            $this->display('add');
        }
    }

    //删除菜单
    public function remove($id){
        if ($this->_model->deleteMenu($id) === false) {
            $this->error(getErr($this->_model));
        } else {
            $this->success('删除成功', U('index'));
        }

    }

    //查出菜单和权限
    public function bView(){
        $menus=$this->_model->getList();
        //加入顶级权限
        array_unshift($menus,['id'=>0,'name'=>'顶级菜单','parent_id' =>0]);
        $this->assign('menus',json_encode($menus));

        $pmodel=D('Permission');
        $permissions=$pmodel->getList();
        $this->assign('permissions',json_encode($permissions));
    }

}