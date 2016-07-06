<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/2
 * Time: 10:58
 */

namespace Admin\Model;


use Think\Model;

class AdminModel extends Model
{
    //0.批量验证
    protected $patchValidate=true;
    //自动验证
    /**
     * 1.username必填 唯一
     * 2.password必填 长度6-16位
     * 3.repassword 和password一致
     * 4.email 必填 唯一
     * @var type
     */
    protected $_validate=[
        ['username','require','用户名不能为空'],
        //注册时验证,场景写到对应的create('','register')中
        ['username','','用户名已被使用',self::EXISTS_VALIDATE,'unique','register'],
        ['password','require','密码不能为空'],
        ['password','6,16','密码长度非法',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','密码两次输入不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','email','邮箱格式不正确',self::EXISTS_VALIDATE],
        ['email','require','邮箱不能为空'],
        ['email','','邮箱已被使用',self::EXISTS_VALIDATE,'unique'],
        //自动验证验证码,callback写在这个model中
        ['captcha','checkC','验证码不正确',self::EXISTS_VALIDATE,'callback'],

    ];
    //自动完成:add_time和自动制造盐
    protected $_auto=[
        ['add_time',NOW_TIME,'register'],
        ['salt','\Org\Util\String::randString','register','function']
    ];
    //验证验证码的方法
    protected function checkC($code){
        $verify=new \Think\Verify();
        return $verify->check($code);
    }
    //1.1查询本表数据并以分页展示
    public function getPageRe(array $cond=[]){
        //1.如果模糊查询了,就把模糊查询条件放进去
        $cond = array_merge(['status'=>1],$cond);
        //2.查询总行数
        $count=$this->where($cond)->count();
        //3.获取配置
        $setting=C('PAGE_SETTING');
        //4.new出类
        $page=new \Think\Page($count,$setting['PAGE_SIZE']);
        //5.放入设置
        $page->setConfig('theme',$setting['PAGE_THEME']);
        //6.show出来
        $page_html=$page->show();
        //7.获取数据
        $rows=$this->where($cond)->page(I('get.p',1),$setting['PAGE_SIZE'])->select();
        return compact('rows','page_html');
    }

    //1.2查找admin表与admin_role表信息
    public function getAdminInfo($id){
        //根据ID找到本表数据
         $row=$this->find($id);
        //查找admin_role表信息
        $armodel=D('AdminRole');
        $row['role_ids']=json_encode($armodel->where(['admin_id'=>$id])->getField('role_id',true));
        return $row;
    }


    //2.添加管理员
    public function addAdmin(){
        //0.把密码加盐加密
         $this->data['password']=salt_mcrypt($this->data['password'],$this->data['salt']);
         //1.添加本表数据,并保存添加的ID.
        if(($admin_id=$this->add())===false){
            $this->error='添加管理员失败';
        }
        //2.添加角色信息到admin_role表中.
        $admin_role=D('AdminRole');
        $role_ids=I('post.role_id');
        $data=[];
        //因为可能是多组数据,所以需要遍历
        foreach($role_ids as $role_id){
            $data[]=['admin_id'=>$admin_id,'role_id'=>$role_id];
        }
        if($data){
            if($admin_role->addall($data)===false){
                $this->error='添加a_r表失败';
            }
        }
        return true;
    }

    //自动更换密码
    public function repwd($id){
        $str = substr(md5(time()), 0, 6);
        $salt=$this->where(['id'=>$id])->getField('salt');
        $str1=salt_mcrypt($str,$salt);
        $this->where(['id'=>$id])->setField('password',$str1);
        return $str;
    }
    //得到密码更换
    public function cpwd($id){
        $pwd=$this->data['password'];
        $salt=$this->where(['id'=>$id])->getField('salt');
        $str1=salt_mcrypt($pwd,$salt);
        $this->where(['id'=>$id])->setField('password',$str1);
        return $pwd;
    }

    //3.编辑管理员
    public function saveAdmin($id){
        $armodel=D('AdminRole');
        //1.先删除原有的
        if($armodel->where(['admin_id'=>$id])->delete()===false){
           $this->error='删除原有数据失败';
            return false;
        };
        //2.添加新数据存入表(遍历)
        $role_ids=I('post.role_id');
        $data=[];
        foreach($role_ids as $role_id){
            $data[]=['role_id'=>$role_id,'admin_id'=>$id];
        }
        if($data){
            if($armodel->addAll($data)===false){
                $this->error='新增数据失败';
            }
        }
        return true;
    }

    //4.删除管理员
    public function deleteAdmin($id){
        //删除本表数据
        if($this->delete($id)===false){
            $this->error='删除本表数据失败';
            return false;
        }
        //删除a_r表数据
        $armodel=D('AdminRole');
        if($armodel->where(['admin_id'=>$id])->delete()===false){
            $this->error='删除角色关联失败';
            return false;
        }
        return true;
    }

    //5.登录验证
    public function adminLogin(){
        //1.获取传来的用户名和密码
        $username=$this->data['username'];
        $password=$this->data['password'];
        //根据Username获取数据库表中对应所有数据
        $userinfo=$this->getByUsername($username);
        //2.如果根据传来的username在数据库中未获取到相同的数据,就报错
        if(!$userinfo){
            $this->error = '用户名或密码不匹配';
            return false;
        }
        //3.验证密码正确与否
        $spwd=salt_mcrypt($password,$userinfo['salt']);
        if($spwd!=$userinfo['password']){
            $this->error = '用户名或密码不匹配';
            return false;
        }
        //4.保存最后登陆时间与IP
        $data=[
            'last_login_time'=>NOW_TIME,
            'last_login_ip'=> get_client_ip(1), //得到IP,1代表返回IPV4地址数字形式
            'id'=>$userinfo['id']     //保存的ID是什么
        ];
        $this->save($data);

        //5.将用户数据$userInfo保存到session中,直接调用function中的Login方法
        Login($userinfo);

        //6.根据登录的管理员的ID去找权限的ID(a_r表到r_p表到permission)
        $this->getPermissions($userinfo['id']); //获取用户的权限

        //7.利用令牌完成自动登录功能(就是勾了记住密码,关闭浏览器直接输网址还可以进的效果),配合行为使用!
        //7.1 用户每登录一次,就删除之前的token记录,才能插入成功(因为表中id有唯一键)
        $atmodel=D('AdminToken');
        $atmodel->delete($userinfo['id']);

        //7.2自动登录,如果登录界面勾选了记住密码
        if(I('post.remember')){  //就生成cookie(设置中记得加前缀)和数据存入at表
            //入表
            $data=['admin_id'=>$userinfo['id'],'token'=>\Org\Util\String::randString(40)];
            //cookie放值,存7天
            cookie('USER_AUTO_LOGIN_TOKEN',$data,604800);
            $atmodel->add($data);
        }
        //返回用户信息
        return $userinfo;
    }

    //5.1获取用户的权限的方法
    private function getPermissions($admin_id){
        //path<>'':path字段不为空
        //$sql='SELECT DISTINCT path FROM admin_role AS ar JOIN role_permission AS rp ON ar.`role_id`=rp.`role_id` JOIN permission AS p ON p.`id`=rp.`permission_id` WHERE path<>'' AND admin_id=1';
        $cond=[
           'path'=>['neq', ''],
           'admin_id'=> $admin_id
        ];
        //找到所有权限ID(显示左手菜单列表)和权限路径(行为验证登录),将两者保存到数组中
        $permissions=M()->distinct(true)->field('permission_id,path')->table('admin_role')->alias('ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON p.`id`=rp.`permission_id`')->where($cond)->select();
        $pids=[];
        $pathes=[];
        foreach($permissions as $permission){
            $pids[]=$permission['permission_id'];
            $pathes[]=$permission['path'];
        }
        //从function中调用方法,在session中存入$pids和$pathes
        permission_ids($pids);
        permission_pathes($pathes);
        return true;
    }

    //5.2为行为写自动登录的方法
    public function autoLogin(){
        //1.获取cookie中的数据
        $data=cookie('USER_AUTO_LOGIN_TOKEN');
        if(!$data){
            return false;
        }
        //2.用$data值和数据库中的token做对比
        $atmodel = M('AdminToken');
        if (!$atmodel->where($data)->count()) {   //这行数据未找到
            return false;
        }
        //3.为了避免token被窃取,自动登陆一次就重置token
        $atmodel->delete($data['admin_id']);
        //添加新的cookie和表中数据
        $data=['admin_id'=>$data['admin_id'], 'token'=>\Org\Util\String::randString(40)];
        cookie('USER_AUTO_LOGIN_TOKEN',$data,604800);
        $atmodel->add($data);
        //4.如果cookie匹配,保存用户信息到session中
        $userinfo=$this->find($data['admin_id']);
        Login($userinfo);//调用Login方法session存入
        //5.获取并保存用户的权限,用于登录时查看左边的菜单
        $this->getPermissions($userinfo['id']);
        //6.返回用户信息
        return $userinfo;
    }




}