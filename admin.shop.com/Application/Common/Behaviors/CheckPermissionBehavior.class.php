<?php
namespace Common\Behaviors;

use Think\Behavior;

class CheckPermissionBehavior extends Behavior
{

    public function run(&$params)
    {
        //1.获取操作时用到的当前模块名,控制器,方法
        $url=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        //2.获取忽略列表(对这些页面不用验证其权限就可直接登录).A:所有人都能登录也就没必要做其他的记录,所以放到最前面
         $ignore_setting=C('ACCESS_IGNORE');
        //3.配置所有人都可访问的页面   //IGNORE:所有人都可见的页面
         $allallow=$ignore_setting['IGNORE'];
        if(in_array($url,$allallow)){  //如果你执行的操作在后面的$allallow找到了,那么就允许操作
            return true;
        }

        //4.利用令牌完成自动登录  B:判断是否可以自动登录
        $userinfo=login();//获取用户登录信息.
        if(!$userinfo){                    //如果没有用户信息,说明就没登录
            $userinfo= D('Admin')->autoLogin();//就调用自动登录的方法
        }

        //5.判断登录帐号是否是超级管理员,如果是就什么都不验证
        if(isset($userinfo['username'])&&$userinfo['username']=='admin111'){
            return true;   //就可以直接操作了
        }

        //6.获取登录者有权限访问的路径,并把它加入允许登录中
        $pathes=permission_pathes();

        //登陆用户都可见的页面,配置中的USER_IGNORE:指登陆后都可见的页面
        $userallow = $ignore_setting['USER_IGNORE'];

        //设置每个用户所有允许访问的页面:这个角色自有的权限+每人都允许的权限
        $urls = $pathes;
        if($userinfo){  //如果用户登录成功(这里不是超级管理员)
            $urls = array_merge($urls,$userallow);  //合并所有可见页面
        }
        //7.如果用户的操作没在允许的范围内,就让它跳到无权限访问页面
        if(!in_array($url,$urls)){
            header('Content_Type:text/html;charset=utf-8');
            //redirect(U('Admin/Admin/login'),3,'您没有权限访问这里');
            echo '<script type="text/javascript">top.location.href="'.U('Admin/Admin/login').'"</script>';
            exit;
        }

    }
}