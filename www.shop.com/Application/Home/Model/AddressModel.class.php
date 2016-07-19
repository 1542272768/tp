<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/10
 * Time: 11:40
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model
{
    //0.开启批量验证
    protected $patchValidate=true;
    //开启自动验证地址信息
    protected $_validate=[
        ['name','require','收货人姓名不能为空'],
        ['province_id','require','省份不能为空'],
        ['city_id','require','市级城市不能为空'],
        ['area_id','require','区县不能为空'],
        ['detail_address','require','详细地址不能为空'],
        ['tel','require','手机不能为空'],
    ];

    //1.添加地址到数据库
    public function addL(){
        $userInfo=Login();
        //判断是否更改默认地址
        if(isset($this->data['is_default'])){
            //删除所有默认地址
            $this->where(['member_id'=>$userInfo['id']])->setField('is_default',0);
        }
        //添加新数据进入
        $this->data['member_id'] = $userInfo['id'];
        return $this->add();
    }

    //2.取出所有收获地址
    public function getList(){
        $userInfo=Login();
        return $this->where(['member_id'=>$userInfo['id']])->select();
    }

    //3.获取某人收获地址进行回显
    public function getAddressInfo($id,$field='*'){
        $userinfo = login();
        $cond = [
            'member_id'=>$userinfo['id'],
            'id'=>$id,
        ];
        return $this->field($field)->where($cond)->find();
    }
    //4.编辑收货地址
    public function saveL(){
        return $this->save();
    }


}