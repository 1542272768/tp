<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/29
 * Time: 9:41
 */

namespace Admin\Model;


use Think\Model;

class GoodsModel extends Model
{
    //1.批量验证
    protected $patchValidate=true;
    //2.自动验证
    protected $_Validate=[
        ['name','require','商品名不能为空'],
        ['sn', '', '货号已存在', self::VALUE_VALIDATE,'unique'],//unique验证是否唯一
        ['goods_category_id', 'require', '商品分类不能为空'],
        ['brand_id', 'require', '品牌不能为空'],
        ['supplier_id', 'require', '供货商不能为空'],
        ['market_price', 'require', '市场价不能为空'],
        ['market_price', 'currency', '市场价不合法'],
        ['shop_price', 'require', '售价不能为空'],
        ['shop_price', 'currency', '售价不合法'],
        ['stock', 'require', '库存不能为空'],
    ];
    //3.自动完成$_auto
    protected $_auto=[
        //1.自动添加时间
      ['inputtime',NOW_TIME,self::MODEL_INSERT],
        //2.自动添加货号
      ['sn','createSn',self::MODEL_INSERT,'callback'],
        //3.自动添加商品状态,二进制值已数组存入
      ['goods_status','calcGoodsStatus',self::MODEL_BOTH, 'callback'],
    ];

    //求和,求出商品推荐类型的位运算值.
    protected function calcGoodsStatus($goods_status) {
        if(isset($goods_status)){
            return array_sum($goods_status);
        }else{
            return 0;
        }
    }

    //3.1创建货号方法
    public function createSn($sn){
        $this->startTrans();
        //如果自己写了sn,就不自动创建了
        if($sn){
            return $sn;
        }
        //生成规则:SN年月日编号:SN2016062800001
        //1.获取今天已经创建了商品编号
        $date=date('Ymd');
        $gnmodel=M('goods_num');
        //判断,进表
        if($num=$gnmodel->getFieldByDate($date,'num')){  //如果找到了,让num加1进表
            ++$num;
            $data=['date'=>$date,'num'=>$num];
            $flag=$gnmodel->save($data);
        }else{                                   //如果没找到了,让num新增
            $num=1;
            $data=['date'=>$date,'num'=>$num];
            $flag=$gnmodel->add($data);
        };
        if($flag===false){
            $this->rollback();
        }
        //2.拼接SN:SN+20160628+00001,左边0填充,然后返回$sn,去自动添加
        $sn='SN'.$date.str_pad($num,5,'0',STR_PAD_LEFT);
        return $sn;
    }

    //4.添加商品,事务在自动完成的创建sn的方法中开启,在这里提交或者回滚.
    public function addGoods(){
        //删除ID


        //1.操作本表:保存基本信息(记录本条记录的ID号:$goods_id,用于给其他表加数据)
        if(($goods_id=$this->add())===false){  //保存失败回滚
            $this->rollback();
            return false;
        }
        //2.操作其它表:保存商品详细描述
        $data=[
            'goods_id'=>$goods_id,
            'content'=>I('post.content','',false),//I('变量类型.变量名',['默认值'],['过滤方法']),这里的写法是表示不再进行任何的过滤。
        ];
        //建表操作
         $gimodel=M('GoodsIntro');
        if($gimodel->add($data)===false){
            $this->rollback();
            return false;
        }

        //3.保存相册图片信息进表
        $ggmodel=D('GoodsGallery');
        $pathes=I('post.path');
        //对上面的数组循环放值
        $data=[];
        foreach($pathes as $path){
            $data[]=[
              'goods_id'=>$goods_id,
               'path'=>$path
            ];
        }
        //传了相册数据,且添加了失败了就报错
        if($data && ($ggmodel->addall($data)===false)){
            $this->rollback();
            return false;
        };
        //事务提交
        $this->commit();
        return true;
    }

    //5.写index页面的数据显示与分页,在其中对商品状态进行处理
    public function getPageRe(array $cond=[]){
        //0.只查找状态为1的数据
        $cond=array_merge(['status'=>1],$cond);
        //1.创建分页:1.1获取总条数
        $count=$this->where($cond)->count();
        //1.2.获取分页代码
        $page_setting=C('PAGE_SETTING');//拿配置
        $page=new \Think\Page($count, $page_setting['PAGE_SIZE']);//总的,每页的
        $page->setConfig('theme',$page_setting['PAGE_THEME']);//设置
        $page_html=$page->show();//制造
        //1.3获取出分页数据
        $rows=$this->where($cond)->page(I('get.p'),$page_setting['PAGE_SIZE'])->select();

        //2.找出商品状态
        foreach($rows as $key=>$val){
            //检测各状态是否存在
            $val['is_best']=$val['goods_status'] & 1 ? true : false;  //为true则状态存在,为false则没有这个状态
            $val['is_new']=$val['goods_status'] &  2 ? true : false;
            $val['is_hot']=$val['goods_status'] &  4 ? true : false;
            //往$rows添加为true的is_best,is_new,is_hot
            $rows[$key]=$val;
        };
        return compact('rows', 'page_html');
    }

    //6.1编辑页面获取商品信息,包括详细介绍和相册.用以回显
    public function getGoodsInfo($id) {
        //1.获取商品的基本信息
        $row=$this->find($id);
        //2.获取到各状态,这里转化为json对象
        $row['goods_status'];
        $tmp = [];
        if($row['goods_status']&1){$tmp[]=1;}
        if($row['goods_status']&2){$tmp[]=2;}
        if($row['goods_status']&4){$tmp[]=4;}
        //找到状态放入属性中
        $row['goods_status'] = json_encode($tmp);unset($tmp);
        //3.商品介绍表获取详细描述
        $goods_intro_model = M('GoodsIntro');
        $row['content']=$goods_intro_model->getFieldByGoodsId($id,'content');
        //4.商品相册表获取相册地址
        $ggmodel = M('GoodsGallery');
        $row['galleries']=$ggmodel->getFieldByGoodsId($id,'id,path');
        return $row;
    }

    //6.2编辑商品列表信息入库
    public function saveGoods(){
        //定义最初值,开启事务
        $goodsId=$this->data;
        $this->startTrans();
        //1.保存本表信息
        if($this->save()===false){
            $this->rollback();
            return false;
        }
        //2.保存其它表信息(商品介绍表)
        $data=[
            'goods_id'=>$goodsId['id'],
            'content'=>I('post.content','',false)
        ];
        $gimodel=D('GoodsIntro');
       if($gimodel->save($data)===false){
           $this->rollback();
           return false;
       };
        //3.保存相册图片信息进表
        $ggmodel1=D('GoodsGallery');
        $pathes1=I('post.path');
        //对上面的数组循环存值
        $data=[];
        foreach($pathes1 as $path){
            $data[]=[
                'goods_id'=>$goodsId['id'],
                'path'=>$path
            ];
        }
        //传了相册数据,且保存失败了就报错
        if($data && ($ggmodel1->addall($data)===false)){
            $this->rollback();
            return false;
        };

        $this->commit();
        return true;
    }



}