<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/13
 * Time: 23:26
 */

namespace Admin\Model;


use Think\Model;

class OrderInfoModel extends Model
{
    public $statuses = [
        0=>'已取消',
        1=>'待支付',
        2=>'待发货',
        3=>'待收货',
        4=>'交易完成',
    ];
    //获取表信息
    public function getList() {
        return $this->order('inputtime desc')->select();
    }

}