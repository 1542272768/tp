<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/9
 * Time: 23:44
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model
{
    //获取指定地区的下级城市
    public function getListByPid($pid=0){//默认为0查省份
        return $this->where(['parent_id'=>$pid])->select();
    }
}