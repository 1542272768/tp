<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/5
 * Time: 16:18
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller
{
    //1.注册页面点击获取手机验证码,url中写的,即跳到这个控制器下的方法中进行验证
    public function regSms($tel){  //$tel接收到的手机号码
        //dump(get_included_files());
        //利用阿里大鱼发送短信给注册者
        Vendor('Alidayu.TopSdk');         //引入阿里大鱼的类库:TopSdk.php
        $c            = new \TopClient;   //前面必须加\
        $c->appkey    = '23398908';       //阿里大鱼开发者控制台项目中的apk
        $c->secretKey = 'b6d022f141f01d6efc2c491813a31262'; //阿里大鱼开发者控制台项目中的stk
        $req          = new \AlibabaAliqinFcSmsNumSendRequest; //前面必须加\
        $req->setSmsType("normal");         //发送的短信类型,固定
        $req->setSmsFreeSignName("ym来了"); //阿里大鱼中验证码申请通过配置短信签名中的签名名称

        $code = \Org\Util\String::randNumber(100000, 999999);//生成随机验证码
        session('reg_tel_code',$code);  //放入session中,在MemberModel中做对比
        //设置短信发送内容与验证码
        $data = [
            'product'=>'YM商城欢迎您',   //模版中的${product}
            'code'=> $code,            //模版中的${code}
        ];
        $req->setSmsParam(json_encode($data));   //设置data的类型
        $req->setRecNum($tel);                   //得到的电话号码
        $req->setSmsTemplateCode("SMS_11540092");//阿里大鱼中验证码申请通过那栏的模版ID
        $resp         = $c->execute($req);       //执行操作
        dump($resp);

    }
}