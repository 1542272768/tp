<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/11
 * Time: 17:30
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller
{
    /**
     * var @var \Home\Model\OrderInfoModel
     */
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('OrderInfo');
    }

    //1.创建订单
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            //1.添加订单
            if($this->_model->addOrder()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('创建订单成功',U('Cart/flow3'));
            }
        }else{
            $this->error('拒绝直接访问');
        }
    }

    //2.查看订单状态
    public function index(){
        //1.判断是否需要展示商品分类(首页展示,其它页面折叠)
        $this->assign('show_category', false);

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

        //4.查询会员的订单回显
        $oimodel=D('OrderInfo');
        $rows=$oimodel->getList();
        $this->assign('rows',$rows);

        //5.查询状态的回显
        $this->assign('statuses',$this->_model->statuses);

        $this->display();
    }

    //3.用户完成收货操作
    public function finish($id){
        $order_info_model = M('OrderInfo');
        if($order_info_model->where(['id'=>$id])->setField('status',4)===false){
            $this->error(getErr($order_info_model));
        }else{
            $this->success('交易完成',U('index'));
        }
    }
}