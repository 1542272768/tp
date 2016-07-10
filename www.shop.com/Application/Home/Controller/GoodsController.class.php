<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/8
 * Time: 16:27
 */

namespace Home\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    //1.从数据库获得商品点击次数返回给html页面(为了减轻数据库压力,不用此方法)
    public function ClickTimes($id){
        //从表中获取点击次数
        $gcmodel=M('GoodsClick');
        $num=$gcmodel->getFieldByGoodsId($id,'click_times');
        if(!$num){  //如果没有浏览次数
            $num=1;
            //设置数据,加入数据库
            $data = ['goods_id'=>$id, 'click_times'=>$num,];
            $gcmodel->add($data);
        }else{     //有则自加浏览次数
            ++$num;
            $data = ['goods_id'=>$id, 'click_times'=>$num,];
            $gcmodel->save($data);
        }
        $this->ajaxReturn($num);
    }

    //2.1做优化,我们从redis获得商品浏览次数返回给html页面,使用此方法
   public function getClickTimes($id){
       //function中启用redis
       $redis = get_redis();
       $key='goods_clicks'; //数据库表中字段
       //返回浏览器次数,zIncrBy(字段,增加次数,传过来的商品ID)
       $this->ajaxReturn($redis->zIncrBy($key,1,$id));
   }

    //2.2将redis中的点击次数保存到数据库中.(此方法手动调用配合2.1,用于任务计划中,来保存数据)
    public function syncGoodsClicks(){
        $redis=get_redis();
        $key='goods_clicks'; //数据库表中字段
        //1.获取到所有商品的点击次数(字段,0到-1就是所有,-2代表倒数第二个,true代表取出值val,不加true只能拿到key)
        //取出来的是一维数组,gongs_id与click_times对
        $goods_clicks=$redis->zRange($key,0,-1,true);
        //优化方法:当一次插入500-1000条时可以进行分段
        //$tmp = array_chunk($goods_clicks,1000,true);
        //遍历里面的第一维,然后重复使用下面的代码

        //2.如果在redis中没有浏览次数,后面的操作无需执行
        if(empty($goods_clicks)){
            return true;
        }

        //3.将redis中所有浏览次数保存到数据表中
        $gcmodel=M('GoodsClick');
        //先删除所有的,再加入全部的
         $goods_ids=array_keys($goods_clicks); //拿出所有商品ID
         $gcmodel->where(['goods_id'=>['in',$goods_ids]])->delete();

        $data=[];
        foreach($goods_clicks as $key=>$val){
            $data[]=['goods_id'=>$key, 'click_times'=>$val,];
        }
        echo '<script type="text/javascript">window.close();</script>';
        return $gcmodel->addAll($data);
    }
}