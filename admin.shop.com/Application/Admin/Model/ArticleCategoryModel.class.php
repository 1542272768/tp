<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/25
 * Time: 10:28
 */

namespace Admin\Model;


use Think\Model;

class ArticleCategoryModel extends Model
{

//2.将请求中的数据更新
  public function saveMany($re){

//      $model=D('Article');
//      $data=array(
//          'name'=>$re['name'],
//          'intro'=>$re['intro'],
//          'sort'=>$re['sort'],
//          'status'=>$re['status'],
//          'article_category_id'=>$re['fid'],
//      );
      //$result=$model->where('id='.$re['id'])->save($data);
      //$re=$model->getLastSql();dump($re);exit;  放到article表中尝试？


      //2法：直接更新2个语句
      $res=$re;
      $sql="update article set name='".$res['name']."',intro='".$res['intro']."',article_category_id='".$res['fid']."',sort='".$res['sort']."',status='".$res['status']."' where id=".$res['id'];
      $this->execute($sql);

      $sql2="update article_content set content='".$res['content']."' where article_id=".$res['id'];
      $this->execute($sql2);

  }

}