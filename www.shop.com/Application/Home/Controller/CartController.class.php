<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/8
 * Time: 19:13
 */

namespace Home\Controller;
use Think\Controller;

//购物车相关逻辑的控制器.
class CartController extends Controller
{
    //1.向购物车加入商品
    public function add2car($id, $amount){
        //1.获取用户登录状态
        $userinfo=Login();
        //1.1如果未登录
        if(!$userinfo){
            //自定义名字,拿给其他使用的
            $key      = 'USER_SHOPPING_CAR';
            //取出cookie中的购物车数据
            $car_list = cookie($key);
            if(isset($car_list[$id])){
                $car_list[$id]+=$amount;//如果购物车中商品存在,则数量累加
            }else{
                $car_list[$id] = $amount;//如果购物车中商品不存在,则数量新增
            }
            //然后把数据保存到cookie中一周cookie(变量名,变量值,过去时间)
            cookie($key,$car_list,604800);
        //1.2已登录
        }else{
             //获取当前商品购物车数量
                $shopping_car_model = D('ShoppingCar');
                $num=$shopping_car_model->getNumByGd($id);
                //dump($num);
            //判断数量
            if($num){  //如果数据库中有数据,就加数量
                $shopping_car_model->addNum($id,$amount);
            }else{     //如果数据库中没有有数据,就新增
                $shopping_car_model->add2car($id,$amount);
            }
        }
        //跳转到购物车列表页面
        $this->success('添加成功',U('flow1'));
     }


    //2.显示购物车列表1
    public function flow1() {
        //回显数据库购物车表数据
        $scmodel=D('ShoppingCar');
        $rows=$scmodel->getShopList();
        //dump($rows);
        $this->assign($rows);
        $this->display();
    }

    //购物车商品删除功能
    public function remove($id){
        //判断是否登录
        $userinfo=Login();
        if(!$userinfo){  //未登录则清空该商品的cookie
            cookie('USER_SHOPPING_CAR',null);
            $this->success('删除成功',U('flow1'));
        }else{   //已登录则删除数据库中文件
            $scmodel=D('ShoppingCar');
            $scmodel->deleteC($id);
            $this->success('删除成功',U('flow1'));
        }
    }

    //3.购物车跳转到结算页面:填写收获地址，发票信息，配送方式等
    public function flow2(){
        //必须登录才能看到
        $userInfo=Login();
        if(!$userInfo){
            cookie('this_html',__SELF__);//将当前页保存到cookie中便于登录后跳转
            $this->error('请先登录',U('Member/login'));
        }else{ //数据回显
                //1.收货人信息
                $amodel = D('Address');
                $this->assign('addresses',$amodel->getList());
                //2.送货方式
                $dmodel=D('Delivery');
                $this->assign('deliveries',$dmodel->getList());
                //3.支付方法
                $pmodel=D('Payment');
                $this->assign('payments',$pmodel->getList());
                //4.商品详细信息 return的compact
                $scmodel=D('ShoppingCar');
                $this->assign($scmodel->getShopList());
                $this->display();
            }

    }



}