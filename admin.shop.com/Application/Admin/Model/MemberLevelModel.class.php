<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/11
 * Time: 23:42
 */

namespace Admin\Model;


use Think\Model;

class MemberLevelModel extends Model
{
    //获取会员等级信息
    public function getList(){
        return $this->where(['status'=>1])->order('sort')->select();
    }
}