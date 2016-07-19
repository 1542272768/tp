<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/11
 * Time: 17:38
 */

namespace Home\Model;


use Think\Model;

class OrderInfoModel extends Model
{
    //设置订单状态
    public $statuses=[0=>'已取消',1=>'待支付',2=>'待发货',3=>'待收货',4=>'交易完成'];


    /* I函数接收的数据,根据这些ID值查具体数据
    array(5) {
    ["address_id"] => string(1) "1"
    ["delivery_id"] => string(1) "1"
    ["pay_type_id"] => string(1) "1"
    ["receipt_type"] => string(1) "1"
    ["receipt_content_type"] => string(1) "1"
    }
     */

    //1.添加订单
    public function addOrder(){
        $this->startTrans();
        //1.1 获取收获地址,后组合数据(操作本表)
        $amodel=D('Address');
        $aInfo=$amodel->getAddressInfo(I('post.address_id'), 'province_name,city_name,area_name,tel,name,detail_address,member_id');
        $this->data=array_merge($this->data,$aInfo);

        //1.2获取配送方式,后组合数据
        $dmodel=D('Delivery');
        $dInfo=$dmodel->getDeliveryInfo(I('post.delivery_id'), 'name as delivery_name,price as delivery_price');
        $this->data=array_merge($this->data,$dInfo);

        //1.3获取支付方式,后组合数据
        $pmodel=D('Payment');
        $pInfo=$pmodel->getPaymentInfo(I('post.pay_type_id'), 'name as pay_type_name');
        $this->data=array_merge($this->data,$pInfo);

        //1.4获取订单的金额
        $scmodel=D('ShoppingCar');
        $scInfo=$scmodel->getShopList();//取出购物车数据,这返回的是2个数据

        //1.5获取商品库存,如果订单数量>库存,不能创建订单
        //设置连接符与条件
        $cond['_logic']='OR';
        foreach($scInfo['goods_info_list'] as $key=>$val){
            $cond[]=['id'=>$key,'stock'=>['lt',$val['amount']]];  //拼接条件:查找商品表中id=$key与库存<订单数量
        }
        //执行条件
        $gmodel=M('Goods');
        $not_enough_stock_list=$gmodel->where($cond)->select();
        $error='';
        //库存不足,就找出哪个不足提示错误信息,不再执行后续流程.
        if($not_enough_stock_list){
            foreach($not_enough_stock_list as $que_name){
                $error.=$que_name['name'].'库存量为:'.$que_name['stock'];
            }
            $this->error=$error.',订单量过多,请重新下单';
            $this->rollback();
            return false;
        }
        //库存足够,减库存
        foreach($scInfo['goods_info_list'] as $goods){  //遍历购物车数据,改变库存
            if($gmodel->where(['id'=>$goods['id']])->setDec('stock',$goods['amount'])===false){
                $this->error='更新库存失败';
                $this->rollback();
                return false;
            };
        }

        //设置data中的订单数据
        $this->data['price']=$scInfo['total_price'];
        $this->data['status']=1;//订单创建状态为未支付
        $this->data['inputtime']=NOW_TIME;//订单创建时间

        //1.5添加操作
        if(($order_id=$this->add())===false){
            $this->rollback();
            return false;
        }

        //2.操作它表order_info_item,保存订单详情
        //2.1获取订单详情,已有购物车数据$scInfo
        $data=[];
        foreach($scInfo['goods_info_list'] as $goods){
            $data[]=[
                'order_info_id' => $order_id,  //添加成功时ID
                'goods_id' => $goods['id'],
                'goods_name' => $goods['name'],
                'logo' => $goods['logo'],
                'price' => $goods['shop_price'],
                'amount' => $goods['amount'],
                'total_price' => $goods['sub_price'],
            ];
        }
        //创建模型入库
        $oiimodel=D('OrderInfoItem');
        if($oiimodel->addAll($data)===false){
            $this->error = '保存订单详情失败';
            $this->rollback();
            return false;
        }

        //3保存发票信息
        //3.1获取抬头数据，个人还是公司
        $receipt_type=I('post.receipt_type');
        if($receipt_type==1){
            $receipt_name=$aInfo['name'];  //收获地址中姓名
        }else{
            $receipt_name=I('post.company_name');
        }
        //3.2获取发票内容数据(拼接)
        $receipt_content_type=I('post.receipt_content_type'); //1,2,3,4
        $receipt_content='';
        switch($receipt_content_type){
            case 1:  //发票内容为明细
                $tmp=[];
                     foreach($scInfo['goods_info_list'] as $goods){   //循环购物车中的数据,制作为发票明细
                         $tmp[]=$goods['name']."\t" . $goods['shop_price'] . '×' . $goods['amount'] . "\t" . $goods['sub_total'];
                     }

                $receipt_content=implode("\r\n",$tmp);//回车,连接内容
                break;
            case 2:
                $receipt_content.='办公用品';
                break;
            case 3:
                $receipt_content.='体育休闲';
                break;
            default:
                $receipt_content.='耗材';
                break;
        }
        //准备全部内容
        $content=$receipt_name."\r\n". $receipt_content . "\r\n总计：" . $scInfo['total_price'];
        //准备数据
        $data=['name'=>$receipt_name,'content' =>$content,'price'=>$scInfo['total_price'],'inputtime'=>NOW_TIME,'member_id'=>$aInfo['member_id'],'order_info_id' => $order_id,];
        //dump($data);exit;
        //创建模型入库
        $imodel=D('Invoice');
        if($imodel->add($data)===false){
            $this->error = '保存发票失败';
            $this->rollback();
            return false;
        }

        //4.操作完成后清空购物车(无需清cookie)
        if($scmodel->clearCar()===false){
            $this->error = '清空购物车失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    //查询订单表
    public function getList(){
        //先查找此会员有哪些订单
        $userinfo=Login();
        $cond=['member_id'=>$userinfo['id']];
        $re=$this->where($cond)->select();
        //取出所有订单详情
        $oiimodel=D('OrderInfoItem');
        foreach($re as $key=>$val){
            //goods_list用于显示图片等
            $re[$key]['goods_list']=$oiimodel->field('goods_id,goods_name,logo')->where(['order_info_id'=>$val['id']])->select();
        }
        return $re;
    }

    //阿里支付中:获取订单信息.
    public function getOrderInfoById($id) {
        return $this->find($id);
    }

}