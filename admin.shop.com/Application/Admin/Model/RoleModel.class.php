<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/1
 * Time: 21:59
 */

namespace Admin\Model;


use Think\Model;

class RoleModel extends Model
{
    //0-1.查询首页数据
    public function getPageRe(array $cond=[]) {
        //查询条件
        $cond = array_merge(['status'=>1],$cond);
        //总行数
        $count = $this->where($cond)->count();
        //获取配置
        $page_setting = C('PAGE_SETTING');
        //工具类对象
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        //设置主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        //获取分页代码
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact('rows', 'page_html');
    }
    //0-2.查询角色表所有数据回显到网页
    public function getPInfo($id){
        //查询本表数据回显
        $row=$this->where(['status'=>1])->find($id);
        //查询它表数据回显
        $role_permission_model = M('RolePermission');
        //直接在row中定义key为permission_ids的查询,返回一个一维数组
        $row['permission_ids']=json_encode($role_permission_model->where(['role_id'=>$id])->getField('permission_id',true));
        return $row;
    }

    //1.添加角色!
    public function addRole(){
        $this->startTrans();
        //添加本表信息:其中保存新建的的自增ID值
        if(($role_id=$this->add())===false){
            $this->rollback();
            return false;
        };
        //添加role-p表信息
       //先遍历出权限,因为可以给一个角色多个权限啊,最后在添加啊
        $permission_ids=I('post.permission_id');
        $data=[];
        foreach($permission_ids as $val){
            $data[]=['role_id'=>$role_id,'permission_id'=>$val];
        }
        if($data){
        $role_permission=D('RolePermission');
        if($role_permission->addall($data)===false) {
            $this->error = '保存权限失败';
            $this->rollback();
            return false;
            }
        };
        $this->commit();
        return true;
    }
    //2.保存角色!
    public function saveRole(){
        $this->startTrans();
        $role_id = $this->data['id'];//保存处理的ID值
        //1.保存基本信息
        if ($this->save() === false){
            $this->rollback();
            return false;
        }
        //2.删除原有的
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$role_id])->delete()===false){
            $this->error = '删除历史权限失败';
            $this->rollback();
            return false;

        };
        //3.保存关联的权限
        $permission_ids=I('post.permission_id');
        $data=[];
        foreach($permission_ids as $val){
            $data[]=['role_id'=>$role_id,'permission_id'=>$val];
        }
        if($data){
            if($role_permission_model->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    //4.删除角色,同时删除对应的权限关联.!
    public function deleteRole($id){
        //1.删除本表数据
        $this->startTrans();
        //删除角色记录
        if($this->delete($id) === false){
            $this->rollback();
            return false;
        }
        //2.删除role_p表的关联权限
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$id])->delete()===false){
            $this->error = '删除权限关联失败';
            $this->rollback();
            return false;
        }
        //3.删除admin_role表的管理员管理
        //删除a_r表数据
        $armodel=D('AdminRole');
        if($armodel->where(['role_id'=>$id])->delete()===false){
            $this->error='删除管理员关联失败';
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;
    }

    //.查找角色表信息
    public function getList(){
        return $this->select();
    }

}