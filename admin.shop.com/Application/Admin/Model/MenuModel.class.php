<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/2
 * Time: 22:00
 */

namespace Admin\Model;


use Think\Model;

class MenuModel extends Model
{
    //0.自动验证
    protected $_validate = [
        ['name', 'require', '菜单名称不能为空'],
    ];

    //查询菜单
    public function getlist(){
        return $this->where(['status'=>['egt', 0]])->order('lft')->select();
    }

    //1.添加菜单
    public function addMenu(){
        //添加本表,使用net
        //1.删除主键
        unset($this->data[$this->getPk()]);
        $orm=D('MySQL','Logic');
        $net=new \Admin\Logic\NestedSets($orm,$this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if(($menus_id   = $net->insert($this->data['parent_id'], $this->data, 'bottom')) === false){
            $this->error = '添加菜单失败';
            return false;
        }
        //添加菜单_权限表,用上面的menus_id,和循环获得$this->data['permission_id']
        $menu_permission_model = M('MenuPermission');
        $pids=I('post.permission_id');
        $data=[];
        foreach($pids as $pid){
            $data[]=['menu_id'=>$menus_id,'permission_id'=>$pid];
        }
        if($data){
            if ($menu_permission_model->addAll($data) === false) {
                $this->error = '保存权限关联失败';
                return false;
             }
        }
        return true;
    }

    //2.编辑的回显
    public function getMenuInfo($id){
        //查找本表数据
        $row=$this->find($id);
        //它表数据放入row中让其回显
        $mpmodel=D('MenuPermission');
        $row['permission_ids']=json_encode($mpmodel->where(['menu_id'=>$id])->getField('permission_id', true));
        return $row;
    }

    //编辑菜单
    public function saveMenu(){
        $id=$this->data['id']; //保存操作的当前行ID,它表使用

        //1.保存本表数据(先判断更改父级菜单没,即parent_id变了没)
        $parent_id=$this->data['parent_id'];
        $form_pid=$this->getFieldById($id,'parent_id');
        if($parent_id != $form_pid){
            //就要用嵌套更新新数据了
            $orm=D('MySQL','Logic');
            $net=new \Admin\Logic\NestedSets($orm,$this->getTableName(),'lft', 'rght', 'parent_id', 'id', 'level');
            //moveUnder只计算左右节点和层级，不保存其它数据
            if($net->moveUnder($this->data['id'], $this->data['parent_id'],'bottom')===false){
                $this->error = '不能将菜单移动到后代菜单下';
                return false;
            };
        }
        //保存本表其他数据
         if($this->save()===false){
             return false;
         }
        //2.保存它表数据(先删除,后循环存值入表)
        $mpmodel=D('MenuPermission');
        if($mpmodel->where(['menu_id'=>$id])->delete()===false){
            $this->error = '删除历史关联失败';
            return false;
        }

        $data           = [];
        $permission_ids = I('post.permission_id');
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'       => $id,
                'permission_id' => $permission_id,
            ];
        }
        if ($data) {
            if ($mpmodel->addAll($data) === false) {
                $this->error = '保存权限关联失败';
                return false;
            }
        }
        return true;
    }

    //3.删除菜单表
    public function deleteMenu($id){
        //使用save操作后$this->data里面的数据清空
        //先删除关联数据,在删除本表数据.(因为删除本表数据时会清空ID,所以要留到最后做)

        //3.删除menu_p表的关联数据,需要计算出在左右节点之间的节点一并删除
        $mpmodel=D('MenuPermission');
        $info = $this->field('lft,rght')->find($id);
        $cond=['lft'=>['egt',$info['lft']],'rgnt'=>['elt',$info['rgnt']]];
        //找出menu表中符合这些节点的id来删除
        $mids=$this->where($cond)->getField('id',true);
        if($mpmodel->where(['menu_id'=>['in',$mids]])->delete()===false){
            $this->error = '删除历史关联失败';
            return false;
        }

        //2.删除本表的数据,可能有后代,所以要用net删除
        $orm=D('MySQL','Logic');
        $net=new \Admin\Logic\NestedSets($orm,$this->getTableName(),'lft', 'rght', 'parent_id', 'id', 'level');
        //delete会将所有的后代菜单一并删除,并且重新计算相关节点的左右节点
         if($net->delete($id)===false){
             return false;
         }
       return true;
    }

    //4.查询用户可看的菜单数据
    public function getMenuList(){
         //判断登录用户是不是超管
        $userinfo=Login();
        if($userinfo['username']=='admin111'){//如果是超级管理员,可看到左侧全部菜单
            //查出所有的菜单ID返回给html页面
            $menus=$this->distinct(true)->field('id,parent_id,name,path')->alias('m')->join('__MENU_PERMISSION__ as mp ON mp.menu_id=m.id')->select();
        }else{   //如果不是超管,就显示自身权限能看到的菜单
            //先获取用户权限id
            $pids = permission_pids();
            //根据用户权限ID获取用户菜单的id
            if($pids){
                $menus = $this->distinct(true)->field('id,parent_id,name,path')->alias('m')->join('__MENU_PERMISSION__ as mp ON mp.menu_id=m.id')->where(['permission_id'=>['in',$pids]])->select();
            }else{
                $menus = [];
            }
        }
        //返回菜单信息
        return $menus;
    }

}