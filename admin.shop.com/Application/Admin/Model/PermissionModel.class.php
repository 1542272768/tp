<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/1
 * Time: 16:46
 */

namespace Admin\Model;


use Think\Model;

class PermissionModel extends Model
{
    //0.自动验证
    protected $_validate=[
      ['name','require','权限名不能为空']
    ];
    //查找数据
    public function getList(){
        return $this->where(['status'=>1])->order('lft')->select();
    }

    //1.添加方法
    public function addP(){
        //删除主键,才可添加数据进入
        unset($this->data[$this->getPk()]);
        //添加嵌套集合进数据表,所以需要创建orm类
        $orm=D('MySQL','Logic');
        //创建nestedsets对象(数据库操作的类,操作表名,左节点,右节点,父节点,主键,层级)
        $nestedsets=new \Admin\Logic\NestedSets($orm,$this->getTableName(),'lft', 'rght', 'parent_id', 'id', 'level');
        //执行insert操作(父节点,完整的数据,固定bottom)
        if($nestedsets->insert($this->data['parent_id'],$this->data,'bottom')===false){
            $this->error='添加失败';
            return false;
        };
        return true;

    }
    //2.编辑方法
    public function saveP(){
        //先是判断ztree的数据更改与否,如果更改了就要用nestedsets去更改数据
        $parent_id=$this->getFieldById($this->data['id'],'parent_id');
        if($parent_id!=$this->data['parent_id']){
            //添加嵌套集合进数据表,所以需要创建orm类
            $orm=D('MySQL','Logic');
            //创建nestedsets对象(数据库操作的类,操作表名,左节点,右节点,父节点,主键,层级)
            $nestedsets=new \Admin\Logic\NestedSets($orm,$this->getTableName(),'lft', 'rght', 'parent_id', 'id', 'level');
            //执行insert操作(父节点,完整的数据,固定bottom)
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'],'bottom')===false){
                $this->error = '不能将分类移动到自身或后代分类中';
                return false;
            };
        }
        //保存基本数据
        return $this->save();
    }
   //3.删除方法:删除权限时同时删除后代权限
    public function deleteP($id){
        $this->startTrans();
        //1.找到后代权限
        $permission_info=$this->field('lft,rght')->find($id);
        //找到在父权限里面的子权限
        $cond=['lft'=>['egt', $permission_info['lft']],//后代的左>=父的左
               'rght'=>['elt', $permission_info['rght']] ]; //后代的右<=父的右
        //查找到符合条件节点,用一维数组展示出来
        $permission_ids=$this->where($cond)->getField('id',true);

        //2.删除时,还要把role-permission表的关联数据一起删除
        //先创类,然后把表中permission_id在上面的要删除总和所包含的数据删除了
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['permission_id'=>['in',$permission_ids]])->delete()===false){
            $this->error = '删除角色-权限关联失败';
            $this->rollback();
            return false;
        }
        //3.删除权限和菜单的管理
        $menu_permission_model = M('MenuPermission');
        if ($menu_permission_model->where(['permission_id' =>$id])->delete() === false) {
            $this->error = '删除菜单-权限关联失败';
            $this->rollback();
            return false;
        }
        //4.执行嵌套删除(创类,newN,)
        $orm=D('MySQL','Logic');
        $nestedsets=new \Admin\Logic\NestedSets($orm,$this->getTableName(),'lft', 'rght', 'parent_id', 'id', 'level');
        if($nestedsets->delete($id)===false){
            $this->error = '删除失败';
            $this->rollback();
            return false;
            }else{
            $this->commit();
            return true;
          }
        }






}






