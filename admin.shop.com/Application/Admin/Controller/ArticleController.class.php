<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/25
 * Time: 22:16
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleController extends Controller
{
    //添加文章分类
    public function add(){
        $model=D('Article');
        if(IS_POST){
            if($model->create()!==false){//接收数据成功
                if($model->addP(I('post.'))!==false){//自建方法提交所有数据进库表
                    $this->success('添加成功',U('ArticleCategory/index'));//返回权限列表显示
                }else{
                    $this->error(getErr($model));
                }
            }else{
                $this->error(getErr($model));
            }

        }else{
            //父级显示
            $model=D('ArticleCategory');
            $rows=$model->select();
            $this->assign('rows',$rows);
            $this->display('add');
        }
    }
}