<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/4
 * Time: 11:55
 */

namespace Admin\Controller;


use Think\Controller;

class CaptchaController extends Controller
{
    //生成验证码
     public function captcha(){
         $setting=[
           'length'=>4
         ];
         $verify=new \Think\Verify($setting);
         $verify->entry();
     }
}