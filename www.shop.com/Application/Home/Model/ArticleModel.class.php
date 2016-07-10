<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/7
 * Time: 18:39
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model
{
    //查询出分类文章标题与下级详细文章内容的数据
    public function getHelpList(){
         //1.先获取标题
        $acmodel=D('ArticleCategory');
        $acs=$acmodel->where(['status'=>1,'is_help'=>1])->getField('id,name');
        //2.在获取标题下的详细文章
        $data=[];
        foreach($acs as $key=>$val){
            //文章model中直接查找需要的字段
            $articles=$this->field('id,name')->order('sort')->limit(6)->where(['article_category_id'=>$key,'status'=>1])->select();
            //把值放入data中
            $data[$val]=$articles;
        }
        return $data;
    }
}