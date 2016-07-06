<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/5
 * Time: 12:56
 */

namespace Home\Controller;


use Think\Controller;

class CaptchaController extends Controller
{
   //生成验证码
    public function captcha(){
        $setting=['length'=>4];
        $verify=new \Think\Verify($setting);
        $verify->entry();
    }
}