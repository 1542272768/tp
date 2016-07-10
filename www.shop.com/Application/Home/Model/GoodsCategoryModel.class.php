<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/7
 * Time: 17:31
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model
{
   //查找商品分类数据
    public function getList($field='*'){
        return $this->field($field)->where(['status'=>1])->select();
    }
}