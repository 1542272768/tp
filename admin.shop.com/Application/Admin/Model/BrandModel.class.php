<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/25
 * Time: 9:09
 */

namespace Admin\Model;


use Think\Model;

class BrandModel extends Model
{
    //1.开启批量验证
    protected $patchValidate=true;
    //2.自动验证
    protected $_validate=[
        //品牌不能为空且不能重名
        ['name','require','供货商名称不能为空'],
        ['name','','名称已存在',self::EXISTS_VALIDATE,'unique'],
        //介绍不能为空
        ['intro','require','供货商介绍不能为空'],
        //排序只能为数字
        ['sort','number','排序必须为数字'],
    ];
    //3.写分页数据和分页代码
    public function getPageRe(array $cond=[]){
        //先拿配置，获取行数，创分页类，更改样式，制造分页，传回
         $pz=C('PAGE_SETTING');
         $count=$this->where($cond)->count();
         $page=new \Think\Page($count,$pz['PAGE_SIZE']);
         $page->setConfig('theme',$pz['PAGE_THEME']);
         $page_html=$page->show();

         $rows=$this->where($cond)->page(I('get.p',1),$pz['PAGE_SIZE'])->select();
         return compact(['rows','page_html']);
    }

    //查找本表数据
    public function getList(){
        return $this->where(array('status'=>['gt',0]))->select();
    }
}