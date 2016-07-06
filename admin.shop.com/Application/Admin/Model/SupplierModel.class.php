<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/24
 * Time: 17:06
 */

namespace Admin\Model;


use Think\Model;

class SupplierModel extends Model
{
    //1.开启批量验证
    protected $patchValidate=true;
    //2.自动验证
    protected $_validate=[
        //供货商不能为空且不能重名
        ['name','require','供货商名称不能为空'],
        ['name','','名称已存在',self::EXISTS_VALIDATE,'unique'],
        //介绍不能为空
        ['intro','require','供货商介绍不能为空'],
        //排序只能为数字
        ['sort','number','排序必须为数字'],
    ];
    //3.写分页数据和分页代码
    public function getPageRe(array $cond=[]){
        //获取分页配置
        $pz=C('PAGE_SETTING');
        //获取总行数
        $count=$this->where($cond)->count();
        //创分页类
        $page=new \Think\Page($count,$pz['PAGE_SIZE']);//括号里传的是(总行数,每页显示数)
        //更改样式
        $page->setConfig('theme',$pz['PAGE_THEME']);
        //制造分页
        $page_html=$page->show();

        //查询数据显示page括号中传的是(第几页，每页条数)，1为默认第一页
        $rows=$this->where($cond)->page(I('get.p',1),$pz['PAGE_SIZE'])->select();
        return compact(['rows','page_html']);
    }

    //查找本表数据
    public function getList(){
        return $this->where(array('status'=>['gt',0]))->select();
    }

}