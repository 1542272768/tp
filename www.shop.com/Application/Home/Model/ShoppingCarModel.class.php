<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/8
 * Time: 21:16
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model
{
  //1.获取购物车中指定商品的数量(用户已经登录了)
    public function getNumByGd($id){
        $userinfo=Login();
        $cond=['member_id'=>$userinfo['id'], 'goods_id'=>$id,];
        $this->where($cond)->getField('amount');
    }

  //2.更新购物车中商品数量
    public function addNum($id,$amount){
        $userinfo = Login();
        $cond = ['member_id'=>$userinfo['id'], 'goods_id'=>$id,];
        return $this->where($cond)->setInc('amount',$amount);//加
    }

  //3.新增购物车中商品数量
    public function add2car($id,$amount){
        $userinfo= Login();;
        $cond=['member_id'=>$userinfo['id'], 'goods_id'=>$id,'amount'=>$amount];
        return $this->add($cond);
    }
  //4.登陆后根据cookie中的购物车数量改变数据库shopping_car表中数量
    //(在购物车控制器中已将$key='USER_SHOPPING_CAR'存入了cookie)
    public function cookie2db(){
        $userInfo=Login(); //取出用户登录数据,下面需要这里的用户ID值
        $carInfo=cookie('USER_SHOPPING_CAR');//取出cookie中数据
        if(!$carInfo){ //如果未登录,cookie数据就不入表,下面操作不执行
             return true;
        }
        //用户已登录,入表操作(以cookie为准)
        //先删除(cookie中有的)原来的数据
        $cond=['member_id'=>$userInfo['id'],'goods_id'=>['in',array_keys($carInfo)]];
        if($this->where($cond)->delete()===false){
            return false;
        };
        //再添加所有新的
        $data=[];
        foreach($carInfo as $key=>$val){
            $data[]=['member_id'=>$userInfo['id'],'goods_id'=>$key,'amount'=>$val];
        }
        return $this->addAll($data);
     }

    //5.获取数据库购物车表中数据用于回显
    public function getShopList(){
        //5.1判断用户是否登录后获取商品id和数量
        $userInfo=Login();
        if($userInfo){  //5.11登录了从数据库中获取商品id和数量
            $car_list=$this->where(['member_id'=>$userInfo['id']])->getField('goods_id,amount');
        }else{          //5.12未登录则从cookie取商品id和数量
            $car_list=cookie('USER_SHOPPING_CAR');//取出cookie中数据
        }
        //如果购物车内无数据,返回一个默认值
        if(!$car_list){
            return ['total_price' => '0.00', 'goods_info_list'=>[],];
        }

        //5.2根据商品ID获取商品的name,logo,shop_price.
        $gmodel=D('Goods');
        $cond=['id'=>['in',array_keys($car_list)],'status'=>1,'is_on_sale'=>1];
        $goods_info_list=$gmodel->where($cond)->getField('id,name,logo,shop_price');//id用于删除,显示等用

        //组合数据返回给页面
        $total_price=0.00;   //设置总价初始值
        foreach($car_list as $goods_id=>$amount){    //设置html页面需要的值
            $goods_info_list[$goods_id]['amount']=$amount;
            $goods_info_list[$goods_id]['shop_price']=nf($goods_info_list[$goods_id]['shop_price']);
            $goods_info_list[$goods_id]['sub_price']=nf($goods_info_list[$goods_id]['shop_price']*$amount);
            $total_price+=$goods_info_list[$goods_id]['sub_price'];
        }
        $total_price = nf($total_price);
        return compact('goods_info_list','total_price');
    }

    //6.登陆时删除商品
    public function deleteC($id){
        $userinfo=Login();
        $cond=['goods_id'=>$id,'member_id'=>$userinfo['id']];
        $this->where($cond)->delete();
    }

}