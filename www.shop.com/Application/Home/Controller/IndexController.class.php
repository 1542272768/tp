<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    //0.对于需要获取商品分类和帮助文章的数据进行初始化获得
    protected function _initialize(){
    //1.判断是否需要展示商品分类(首页展示,其它页面折叠)
        if(ACTION_NAME =='index'){
            $show_category=true;
        }else{
            $show_category=false;
        }
        $this->assign('show_category', $show_category);

    //2.商品分类和帮助文章,不会频繁发生变化,但是请求又较为频繁,所以我们进行缓存
        //2.1 商品分类放入缓存中
        if(!$goods_categories = S('goods_categories')){  //如果商品分类的没有一个名叫goods_categories的数据缓存
            //创类查表,把数据放入缓存中
            $gcmodel=D('GoodsCategory');
            $goods_categories=$gcmodel->getList('id,name,parent_id');
            //把数据放入goods_categories中缓存1个小时
            S('goods_categories',$goods_categories,3600);
        }
        $this->assign('goods_categories', $goods_categories);

        //2.2 帮助文章放入缓存
        if(!$article_list=S('article_list')){
            //创类查表,把数据放入S?
            $acmodel=D('Article');
            $article_list=$acmodel->getHelpList();
            S('help_article_list', $article_list,3600);
        }
        $this->assign('help_article_list', $article_list);

    //3.获取用户登陆信息,页面右上角显示
        $this->assign('userinfo',login());

    }
    //1.前台首页
    public function index(){
        //1.1初始化数据展示

        //1.2展示商品的状态
        $goods_model = D('Goods');
        $data=[
          'goods_best_list'=>$goods_model->getListByGoodsStatus(1),
          'goods_new_list'=>$goods_model->getListByGoodsStatus(2),
          'goods_hot_list'=>$goods_model->getListByGoodsStatus(4),
        ];
        $this->assign($data);

        $this->display();
    }

    //2.商品显示页面
    public function goods($id){
        //获取商品详情
        $gmodel=D('Goods');
        //如果要看的这个商品不存在时
        if(!$row=$gmodel->getGoodsInfo($id)){  //根据ID查找商品详情
            $this->error('您查看的商品离家出走了,下次动作快点哟',U('index'));
        }
        $this->assign('row',$row);
        $this->display();
    }

}