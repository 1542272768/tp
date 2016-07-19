<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/11
 * Time: 12:46
 */

namespace Home\Model;


use Think\Model;

class DeliveryModel extends Model
{
    //查询送货方法
    public function getList(){
        return $this->where(['status'=>1])->order('sort')->select();
    }
    //查询配送方式信息
    public function getDeliveryInfo($id,$field='*'){
        $cond=['id'=>$id];
        return $this->field($field)->where($cond)->find();

    }
}