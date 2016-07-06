<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/5
 * Time: 10:48
 */

namespace Home\Model;


use Think\Model;

class MemberModel extends Model
{
    //0.批量验证
    protected $patchValidate=true;
    //自动验证
    protected $_validate=[
        ['username','require','用户名不能为空'],
        ['username','','用户名已存在',self::EXISTS_VALIDATE,'unique'],  //唯一
        ['password','require','密码不能为空'],
        ['password','6,16','密码必须6-16位',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不同',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱不能为空'],
        ['email','email','邮箱不合法'],
        ['email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'],
        ['tel','require','手机号码不能为空'],
        ['tel','/^1[34578]\d{9}$/','手机号码不合法',self::EXISTS_VALIDATE,'regex'],
        ['checkcode','require','验证码不能为空'],
        ['checkcode','ckcode','验证码不正确',self::EXISTS_VALIDATE,'callback'],
        //验证手机验证号
        ['captcha','require','手机验证码不能为空'],
        ['captcha','ckc','手机验证码不正确',self::EXISTS_VALIDATE,'callback']
    ];
    //验证图片验证码
    protected function ckcode($code){
        $verify=new \Think\Verify();
        return $verify->check($code);
    }
    //验证手机验证码
    protected function ckc($code){
        //如果注册页面输入的验证码和session中的相等
        if($code==session('reg_tel_code')){
            //清空session里的值
            session('reg_tel_code',null);
            return true;
        }else{
            return false;
        }
    }
    //自动完成入表的字段
    protected $_auto=[
        ['add_time',NOW_TIME],//注册时间
        ['salt','\Org\Util\String::randString',self::MODEL_INSERT,'function'],//生成随机盐
        ['register_token','\Org\Util\String::randString',self::MODEL_INSERT,'function',32],//生成手机的toekn
        ['ststus',0],//没有通过邮件验证的账号是禁用账户,状态为0,成功的是1
    ];

    //1.添加会员
    public function addMember(){
        //1.添加用户信息入表
        //对传送过来的密码加盐加密
        $this->data['password']=salt_mcrypt($this->data['password'],$this->data['salt']);
        //为eamil验证作准备,将数据加入$this->data中
        $register_token=$this->data['register_token']; //自动生成的register_token
        $email = $this->data['email'];                //注册页面的传来的email号码

        if($this->add()===false){
            return false;
        }
        //2.邮件激活操作,调用function中的sendMail方法
            //定义sendMail需要的参数
            $email;///????
            $url=U('Member/active',['email'=>$email,'register_token'=>$register_token],true,true);
            $subject = '欢迎注册YM商城';
            $content = '欢迎您注册我们的网站,请点击<a href="'.$url.'">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />' . $url;

            $re=sendMail($email,$subject,$content);
            //对sendMail的返回值status判断,
            if($re['status']){  //成功时
                return true;
            }else{
                $this->error = $re['msg'];
                return false;
            }
    }

}