<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/5
 * Time: 10:32
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller
{
    /**
     * @var \Home\Model\MemberModel
     */
    //0.建本表类
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Member');

        //准备收货地址左上商品分类
        $mete_titles = [
            'reg'=>'用户注册',
            'login'=>'用户登陆',
        ];
        $meta_title = (isset($mete_titles[ACTION_NAME])?$mete_titles[ACTION_NAME]:'用户登陆');
        $this->assign('meta_title',$meta_title);

        //判断是否需要展示商品分类,首页展示,其它页面折叠
        $this->assign('show_category', false);

        //由于分类数据和帮助文章列表数据,不会频繁发生变化,但是请求又较为频繁,所以我们进行缓存
        if (!$goods_categories = S('goods_categories')) {
            //准备商品分类数据
            $goods_category_model = D('GoodsCategory');
            $goods_categories = $goods_category_model->getList('id,name,parent_id');
            S('goods_categories', $goods_categories,3600);
        }
        $this->assign('goods_categories', $goods_categories);

        if (!$help_article_list = S('help_article_list')) {
            //准备商品分类数据
            $article_category_model = D('Article');
            $help_article_list = $article_category_model->getHelpList();
            S('help_article_list', $help_article_list,3600);
        }
        //帮助文章分类
        $this->assign('help_article_list',$help_article_list);

        //获取用户登陆信息
        $this->assign('userinfo',login());
    }
    //1.注册页显示
    public function reg(){
        if(IS_POST){
            if($this->_model->create('','reg') === false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->addMember() === false){
                $this->error(getErr($this->_model));
            }
            $this->success('注册成功,请到邮箱中激活您的邮件',U('index'),5);

        }else{
            $this->display();
        }
    }
    //2.发送给别人的邮件中的激活账户操作
    public function active($email,$register_token){
        //写条件,查询表中是否有一个记录的email和token和传过来的一致的,'status'=>0未激活状态
        $cond=['email'=>$email,'register_token'=>$register_token,'status'=>0];
        //进行查询
        if($this->_model->where($cond)->count()){  //查询成功
            //设置状态为1
            $this->_model->where($cond)->setField('status',1);
            $this->success('激活成功',U('Index/index'));
        }else{
            $this->error('验证失败',U('Index/index'));
        }

    }
    //3.前台会员登陆
    public function login() {
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->login() === false){
                $this->error(get_error($this->_model));
            }
            //如果有url地址,则跳转到该页面
            $url1=cookie('this_html');
            $url2=cookie('this_address');
            if($url1){  //结算时的跳转
                cookie('this_html',null);
                $this->success('登陆成功',$url1);
            }elseif($url2){  //未登录去收获地址时的跳转
                cookie('this_address',null);
                $this->success('登陆成功',$url2);
            }else{
                //这样写就是直接跳,不提示:U('Index/index');
                $this->success('登陆成功', U('Index/index'));
            }
        }else{
            $this->display();
        }
    }

    //4.注册页面实时验证:检查用户名,邮箱,手机号码.
    public function checkByParam() {
        $cond = I('get.');
        if($this->_model->where($cond)->count()){  //如果查询到了
            $this->ajaxReturn(false);
        }else{
            $this->ajaxReturn(true);
        }
    }

    //5.会员退出界面
    public function logout() {
        session(null);
        cookie(null);
        $this->success('退出成功',U('Index/index'));
    }

    //6.登录后获取用户信息:姓名,返回
    public function getUser(){
        $userinfo=Login();
        if($userinfo){
            $this->ajaxReturn($userinfo['username']);
        }else{
            $this->ajaxReturn(false);
        }
    }
    //7.展示收货地址页面
    public function locations(){
        //判断登录了没
        $userInfo=Login();
        if(!$userInfo){
            //保存url,用于登录后跳转
            cookie('this_address',__SELF__);
           $this->error('请先登录!',U('Member/login'));
        }
        //获取省份列表
        $lmodel=D('Locations');
        $provinces=$lmodel->getListByPid();
        $this->assign('provinces',$provinces);

        //取得所有的收货地址回显
        $amodel = D('Address');
        $this->assign('addresses',$amodel->getList());

        $this->display();
    }

    //7.1 获取选中省份下的城市列表,ajax返回
    public function getCityByPid($parent_id){
        $lmodel=D('Locations');
        $cities=$lmodel->getListByPid($parent_id);
        $this->ajaxReturn($cities);
    }

    //8.添加收获地址入库
    public function addL(){
        $amodel = D('Address');
        if($amodel->create()===false){
            $this->error(get_error($amodel));
        }
        if($amodel->addL()===false){
            $this->error(get_error($amodel));
        }
        $this->success('添加完成',U('locations'));
    }

    //回显修改收获地址的页面
    public function modifyL($id){
        //回显当前地址的详细信息
        $address_model = D('Address');
        $row = $address_model->getAddressInfo($id);
        $this->assign('row',$row);

        //回显三级联动下拉框
        $location_model = D('Locations');
        $provices = $location_model->getListByPid();
        $this->assign('provinces',$provices);

        $this->display();
    }




}