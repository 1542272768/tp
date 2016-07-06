<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    //显示框架集
    public function index(){
        $this->display('index');
    }
    //显示top上面
    public function top(){
        //从Loign获取session数据,返回给页面
        $userinfo=Login();
        $this->assign('userinfo',$userinfo);
        $this->display();
    }
    //显示menu左边菜单
    public function menu(){
        //从menu表中查询这个登录者可以看到的菜单信息
        $mmodel=D('menu');
        $menus=$mmodel->getMenuList();
        $this->assign('menus',$menus);

        //为修改密码从session中传管理员的ID
        $userinfo=Login();
        $this->assign('userinfo',$userinfo);

        $this->display();

    }
    //显示main右边主部
    public function main(){
        $this->display();
    }
}