<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/1
 * Time: 21:58
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller
{
    /**
     * @var \Admin\Model\RoleModel
     */
    //本表类
    private $_model=null;
    protected function _initialize(){
           $this->_model=D('Role');
    }
    //首页
    public function index(){
        //搜索条件
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = [
                'like', '%' . $name . '%'
            ];
        }
        $this->assign($this->_model->getPageRe($cond));
        $this->display();
    }
    //添加
    public function add(){
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->addRole() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功', U('index'));
        }else{
            $this->bView();
            $this->display();
        }
    }
    //编辑
    public function edit($id){
        if(IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->saveRole() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功', U('index'));
        }else{
            //数据回显
            $row = $this->_model->getPInfo($id);
            $this->assign('row',$row);
            $this->bView();
            $this->display('add');
        }

    }
    //删除
    public function remove($id){
        if($this->_model->deleteRole($id) === false){
            $this->error(get_error($this->_model));
        }
        $this->success('删除成功', U('index'));
    }

    //显示所有权限
    private function bView() {
        //获取所有权限
        $permission_model = D('Permission');
        $permissions      = $permission_model->getList();
        //传递
        $this->assign('permissions', json_encode($permissions));
    }

}