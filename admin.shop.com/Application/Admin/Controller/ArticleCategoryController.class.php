<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/25
 * Time: 10:27
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleCategoryController extends Controller
{
     //1.主页面显示父文章与子文章
    public function index(){
        //下拉框选择时显示
           if(IS_POST){
               $pid=I('post.pid');//得到要查询的父类id值，用来查他的子类有哪些
               //显示下拉的父类文章
               $model=D('ArticleCategory');
               $row=$model->select();
               $name=$model->where('id='.$pid)->find();//父类名字传过去
               $this->assign('name',$name);
               $this->assign('row',$row);

               $model=D('ArticleCategory');
               $row=$model->select();
               $this->assign('row',$row);
               //按确定键后显示子类文章  where('article_category_id='.$pid)->select();
               $model=D('Article');
               $rows=$model->where('article_category_id='.$pid)->select();
               $this->assign('rows',$rows);

               $this->display('index');
           }else{
               //进入时首页：显示下拉的父类文章
               $model=D('ArticleCategory');
               $row=$model->select();
               $this->assign('row',$row);
               //按确定键后显示子类文章
               $model=D('Article');
               $rows=$model->where('article_category_id=1')->select();
               $this->assign('rows',$rows);
               $this->display('index');
           }

    }
    //2.查看子文章内容
    public function read($id){
        //显示文章标题
          $model=D('Article');
          $rows=$model->where('id='.$id)->select();
          $this->assign('rows',$rows);
          //显示文章内容
          $model=D('ArticleContent');
          $row=$model->find($id);
          $this->assign($row);//关联数组直接传
          $this->display();
    }
    //3.更新子文章内容
    public function edit($id)
    {

       if(IS_POST){
        $model = D('ArticleCategory');
        if ($model->create() === false) {
               $this->error(getErr($model));
           }
//            //将请求中的数据更新到数据表中
           //$re=I('post.');dump($re);
            if ($model->saveMany(I('post.')) === false) {
                $this->error(getErr($model));
            }else {
                $this->success("更新成功!", U("index"));
            }
        }else{
            //显示父级文章标题
            $model = D('ArticleCategory');
            $r = $model->select();
            $this->assign('r', $r);
            //显示文章标题与简介与排序
            $model = D('Article');
            $rows = $model->where('id=' . $id)->find($id);
            //var_dump($rows);
            $this->assign('rows', $rows);
            //显示文章内容
            $model = D('ArticleContent');
            $row = $model->find($id);
            $this->assign($row);//关联数组直接传
            $this->display('edit');
          }
    }

    //4.删除子文章
    public function remove($id){
        //创类操作
        $model=D('Article');
        $result = $model->delete($id);
        if($result!==false){
            $this->success("删除成功!",U("index"));
        }else{
            $this->error("删除失败!");
        }

    }


}