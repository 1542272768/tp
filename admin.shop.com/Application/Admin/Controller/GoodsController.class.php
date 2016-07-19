<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/29
 * Time: 9:40
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    /**
     * @var \Admin\Model\GoodsModel
     */
    //建本表类
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Goods');
    }

    //1.商品页面显示
     public function index(){
         //1.进行数据的搜索功能,将条件放进$cond,传递给后台查询
         //根据关键字查询
         $name=I('get.name');
         $cond = [];
         if($name){
             $cond['name']=['like','%'.$name.'%'];
         }
         //根据分类查询
         $goods_category_id=I('get.goods_category_id');
         if($goods_category_id){
             $cond['goods_category_id']=$goods_category_id;
             //如果,则需找到选中分类的左右节点加入条件即可
             //lft>=$lft  and rght<=$rght  语句用:$cond['goods_category_id'] = [];
         }
         //根据品牌查询
         $brand_id=I('get.brand_id');
         if($brand_id){
             $cond['brand_id']=$brand_id;
         }
         //根据促销状态查询!!!
         $goods_status=I('get.goods_status');
         if($goods_status){
             //二进制计算:与运算
             $cond[]='goods_status & '.$goods_status;
         }
         //根据上下架查询
         $is_on_sale=I('get.is_on_sale');
           //因为这有空字符串,我们这里只要0或1,使用strlen里面是0或1,则返回1,为空字符串则返回0
         if(strlen($is_on_sale)){
             $cond['is_on_sale']=$is_on_sale;
         }
         //查询出来数据前台显示
         $ro=$this->_model->getPageRe($cond);
         $this->assign($ro);

         //2.回显各种分类
         //商品分类
         $gcmodel=D('GoodsCategory');
         $rows=$gcmodel->getList();
         $this->assign('goods_categories',$rows);
         //商品品牌
         $bmodel=D('Brand');
         $rows1=$bmodel->getList();
         $this->assign('brands',$rows1);
         //商品促销状态
         $gstatus=[
             ['id'=>1,'name'=>'精品'],
             ['id'=>2,'name'=>'新品'],
             ['id'=>4,'name'=>'热销'],
         ];
         $this->assign('gstatus',$gstatus);
         //商品上下架
         $sale=[
             ['id'=>1,'name'=>'上架'],
             ['id'=>0,'name'=>'下架'],
         ];
         $this->assign('is_on_sale',$sale);

         //3.查询数据以分页方式回显
         $rows=$this->_model->getPageRe($cond);
         $this->assign($rows);//获取的是?,直接传变量
         $this->display();
     }

    //2.商品增加
    public function add(){
        //判断post是否发送
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->addGoods()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            //回显数据库的数据,以供选择
             //1.zTree回显商品分类,要以json回传
             $gcmodel=D('GoodsCategory');
             $rows=$gcmodel->getList();
             $this->assign('goods_categories',json_encode($rows));
            //2.回显商品品牌
            $bmodel=D('Brand');
            $rows1=$bmodel->getList();
            $this->assign('brands',$rows1);
            //3.回显供货商
            $smodel=D('Supplier');
            $rows2=$smodel->getList();
            $this->assign('suppliers',$rows2);

            //4.回显会员等级列表
            $mlmodel=D('MemberLevel');
            $member_levels=$mlmodel->getList();
            $this->assign('member_levels',$member_levels);

            $this->display();
        }
    }

    //3.商品修改
    public function edit($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->saveGoods()===false){
                $this->error(getErr($this->_model));
            }else{
                $this->success('编辑成功',U('index'));
            }
        }else{
            //查询数据回显
            //1.获取它表数据,并传递
            $row = $this->_model->getGoodsInfo($id);
            $this->assign('row', $row);

            //2.本表数据操作:zTree回显商品分类,要以json回传
            $gcmodel=D('GoodsCategory');
            $rows=$gcmodel->getList();
            $this->assign('goods_categories',json_encode($rows));
            //2.回显商品品牌
            $bmodel=D('Brand');
            $rows1=$bmodel->getList();
            $this->assign('brands',$rows1);
            //3.回显供货商
            $smodel=D('Supplier');
            $rows2=$smodel->getList();
            $this->assign('suppliers',$rows2);

            //4.回显会员等级列表
            $mlmodel=D('MemberLevel');
            $member_levels=$mlmodel->getList();
            $this->assign('member_levels',$member_levels);

            $this->display();
        }

    }

    //4.商品删除
    public function remove($id){
        //逻辑删除,让status为-1,name加_del即可.
        $cond=['id'=>$id,'status'=>-1,'name'=>['exp','concat(name,"_del")']];
        if($this->_model->setField($cond)===false){
            $this->error(getErr($this->_model));
        }else{
            $this->success('删除成功',U("index"));
        }

    }
    //5.商品图片删除
    public function removeGallery($id){
        $ggmodel=D('GoodsGallery');
        if($ggmodel->delete($id)===false){
            $this->error='删除失败';
        }else{
            $this->success('删除成功',U());
        }
    }

}