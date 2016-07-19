<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/13
 * Time: 23:24
 */

namespace Admin\Controller;


use Think\Controller;

class OrderInfoController extends Controller
{
    //1.发货首页展示
    public function index(){
        $order_info_model = D('OrderInfo');
        $rows             = $order_info_model->getList();
        $this->assign('rows', $rows);
        $this->assign('statuses', $order_info_model->statuses);
        $this->display();
    }
    //2.发货操作
    public function send($id){
        $order_info_model = D('OrderInfo');
        if ($order_info_model->where(['id' => $id])->setField('status', 3) === false) {
            $this->error(getErr($order_info_model));
        } else {
            $this->success('发货成功', U('index'));
        }
    }

    //3.超时订单关闭后加库存(此方法手工调用或外部任务计划调用)http://admin.shop.com/OrderInfo/clearTimeOutOrder
    public function clearTimeOutOrder(){
        M()->startTrans();

        //1.查找出所有已经超时的订单id
        $order_info_model = D('OrderInfo');
        //  添加时间+900<now即超时: 添加时间<now-900
        $order_ids        = $order_info_model->where(['inputtime' => ['lt', NOW_TIME - 900], 'status' => 1])->getField('id', true);

        if (!$order_ids) {  //如果没有超时的,无需执行后续逻辑
            return true;
        }

        //2.修改这些订单的状态为0
        $order_info_model->where(['id' => ['in', $order_ids]])->setField('status', 0);

        //3.查看这些订单占用了哪些商品及其库存数量
        $order_info_item_model = D('OrderInfoItem');
        $goods_list            = $order_info_item_model->where(['order_info_id' => ['in', $order_ids]])->getField('id,goods_id,amount');

        //4.遍历每个商品,将库存加回去
        $goods_model           = M('Goods');
        foreach($goods_list as $goods){
                    $goods_model->where(['id'=>$goods['goods_id']])->setInc('stock',$goods['amount']);
          }
        //dump($goods_list);exit;
        M()->commit();
    }

}