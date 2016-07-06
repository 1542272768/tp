<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/27
 * Time: 22:33
 */

namespace Admin\Model;


use Admin\Logic\MYSQLLogic;
use Think\Model;

class GoodsCategoryModel extends Model
{
    //批量验证开启
     protected $patchValidate=true;
    //自动验证
     protected $_validate=[
         ['name', 'require', '商品分类名称不能为空'],
     ];
    //查找本表数据
    public function getList(){
       return $this->where(array('status'=>['egt',0]))->order('lft')->select();
    }

    //写分类的添加数据，计算左右节点和层级的方法，使用NestedSets实现
    //$this->trueTableName==$this->getTableName()
    public function addCategory(){
        //删除传过来的数据中的主键,避免和自动增长矛盾
        unset($this->data[$this->getPk()]);
        //创建orm对象让nestedsets调用,D方法:Logic下面找MySQL方法($name 资源地址必须一样,$layer 模型层名称)
        $orm=D('MySQL','Logic');
        //创建nestedsets对象,里面传入(数据库操作的类,操作表名,左节点,右节点,父节点,主键,层级)
        $nestedsets=new \Admin\Logic\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //执行insert操作(父节点,完整的数据,固定bottom)
        return $nestedsets->insert($this->data['parent_id'],$this->data,'bottom');

    }

    //写分类的编辑数据
    public function saveCategory(){
        //获取原来的父级ID,使用getFieldBy,不会改变$this->data中的值(根据的字段,要获取的值)
         $parent_id=$this->getFieldById($this->data['id'],'parent_id');
        //判断是否修改了父级分类(如果没修改,就不要创建nestedset),
        if($this->data['parent_id']!=$parent_id){
        //创建orm对象让nestedsets调用,D方法:Logic下面找MySQL方法($name 资源地址必须一样,$layer 模型层名称)
             $orm=D('MySQL','Logic');
        //创建nestedsets对象,里面传入(数据库操作的类,操作表名,左节点,右节点,父节点,主键,层级)
             $nestedsets=new \Admin\Logic\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //使用moveUnder保存左右节点和层级(移动的哪一个分类id,设置它的新父级ID,固定的bottom)
            if($nestedsets->moveUnder($this->data['id'],$this->data['parent_id'],'bottom')===false){
                $this->error='不能将分类移动到后代分类下';
                return false;
            }
        }
        //返回数据保存其他的数据
        return $this->save();
    }

    //写分类的删除数据
    public function deleteCategory($id){
        //创建orm对象让nestedsets调用,D方法:Logic下面找MySQL方法($name 资源地址必须一样,$layer 模型层名称)
        $orm=D('MySQL','Logic');
        //创建nestedsets对象,里面传入(数据库操作的类,操作表名,左节点,右节点,父节点,主键,层级)
        $nestedsets=new \Admin\Logic\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //调用删除方法
        return $nestedsets->delete($id);

    }



}