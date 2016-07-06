<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/25
 * Time: 21:45
 */

namespace Admin\Model;


use Think\Model;

class ArticleModel extends Model
{
    //1.开启批量验证
    protected $patchValidate=true;

    //2。开启自动验证
    protected $_validate=[
        //分类不能为空且不能重名
        ['name','require','分类名称不能为空'],
        ['name','','名称已存在',self::EXISTS_VALIDATE,'unique'],
        //排序只能为数字
        ['sort','number','排序必须为数字'],
    ];
    //2.1自动完成：注册时间入库
    protected $_auto=[
        //注册时间入库
        ['inputtime',NOW_TIME,self::MODEL_INSERT],
    ];
    //3.自定义建立权限添加方法
    public function addP($re){
        //0.防止add方法改变表数据，先存储数据
           $fid=$re;$ffid=$fid['fid'];$content=$fid['content'];
        //1.先把create接收到的数据添加进表
           $id=parent::add();//得到新建的主键id号
        //2.更新分类ID
        parent::save(array('id'=>$id,'article_category_id'=>$ffid));
        //3.article_content表更新内容
        $date=[
            'content'=>$content,
            'article_id'=>$id,
        ];
        $model=D('ArticleContent');
        $re=$model->add($date);
        if($re===false){
            $this->error='保存内容失败';
            return false;
        }
        return true;
   }

    //index页面查询所有数据
    public function getAll($re){
        //dump($re['pid']);exit;
//        $amodel=D('article');
//        $re1=$amodel->where("article_category_id=".$re['pid'])->find('name');
//        dump($re1);exit;
//        $sql="select name from article WHERE article_category_id=".$re['pid'];
//        $re1=$this->query($sql);
//        $sql="select name from article_category WHERE id=".$re['pid'];
//        $re2=$this->query($sql);
//        $qqq=compact(['re1', 're2']);
        $sql="select ac.name as acname,article.name from article_category as ac Left JOIN article ON article_category_id=".$re['pid'];
        $re1=$this->query($sql);
        dump($re1);exit;
        return $qqq;

    }

}