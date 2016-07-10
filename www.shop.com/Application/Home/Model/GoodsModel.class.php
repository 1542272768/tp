<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/7
 * Time: 21:52
 */

namespace Home\Model;


use Think\Model;

class GoodsModel extends Model
{
    //1.根据促销状态显示首页中间的商品列表.
    public function getListByGoodsStatus($goods_status){
        $cond=[
            'status'=>1,
            'is_on_sale'=>1,
            'goods_status & '.$goods_status   //数据库的值和传过来的124做二进制的与运算.
        ];
        return $this->where($cond)->select();
    }

    //2.根据商品ID查找商品表,商品介绍表,品牌表,商品相册表
    public function getGoodsInfo($id){
        $row=$this->field('g.*,b.name as bname,gi.content')->alias('g')->where(['is_on_sale'=>1,'g.status'=>1,'g.id'=>$id])->join('__BRAND__ as b ON g.brand_id=b.id')->join('__GOODS_INTRO__ as gi ON gi.goods_id=g.id')->find();
        $row['galleries'] = M('GoodsGallery')->where(['goods_id'=>$id])->getField('path',true);
        return $row;
    }
}