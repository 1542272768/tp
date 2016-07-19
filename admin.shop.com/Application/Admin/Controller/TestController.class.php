<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/14
 * Time: 14:03
 */

namespace Admin\Controller;

use Think\Controller;

//一.PHP多线程的应用
class TestController extends Controller{
     //1.测试pthreads开启与否
    public function test(){
         $obj=new TestThread();
         $obj->start();
     }
    //2.多线程发送邮件(配合function中sendMail方法使用,里面的aa412300963@163.com作为服务器)
    public function sendMail() {
        $addresses = ['1542272768@qq.com','1028584630@qq.com'];//接收的邮箱地址
        $start     = microtime(true);//开始时间
        //给每个地址循环发送邮件
        $pool      = [];
        foreach ($addresses as $address) {
            $obj    = new TestThread($address, '哟呵,袁爷您来了', '小乔,快来招呼袁大爷');
            $pool[] = $obj;
            $obj->start();
        }
        $end = microtime(true);//结束时间
        echo '共耗时' . ($end - $start) . ' s';
    }


    //二.coreseek中文查找技术的应用
    public function css($keyword = '机') {
        header('Content-Type:text/html; charset=utf-8');
        //引入sphinx的类库
        vendor('Sphinx.sphinxapi');
        //创类,连接服务器
        $spinx     = new \SphinxClient();
        $spinx->SetServer('127.0.0.1', 9312);
        $spinx->SetLimits(0, 50);                //查询条数
        $spinx->SetMatchMode(SPH_MATCH_ANY);     //只需一个关键字匹配

        //这步查询相关的表需要去coreseek\bin\csft.conf进行配置,cmd窗口拉入searchd.exe启动服务.
        $rst       = $spinx->Query($keyword, '*'); //查询到所有关键字

        $goods_ids = array_keys($rst['matches']);  //取出键名
        $model     = M('Goods');
        $list      = $model->where(['id' => ['in', $goods_ids]])->select();//循环输出

        $options   = array(
            'before_match'    => '<span style="color:red;background:lightblue">',
            'after_match'     => '</span>',
            'chunk_separator' => '...',
            'limit'           => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
        );
        //关键字高亮
        $keywords = array_keys($rst['words']);
        foreach ($list as $index => $item) {
            $list[$index] = $spinx->BuildExcerpts($item, 'mysql', implode(',', $keywords), $options); //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
        }
        var_dump($list);
    }

}


//创建类 ,用于继承接口\Thread,方法的实现
class TestThread extends \Thread{
    //1.定义私有变量
    private $email,$subject,$content;
    //2.使用构造方法,new时调用
    public function __construct($email,$subject,$content){
        $this->email=$email;
        $this->subject=$subject;
        $this->content=$content;
    }
    //3.调用方法
   public function run(){
       echo __FILE__;
       sendMail($this->email,$this->subject,$this->content);
   }
}