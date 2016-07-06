<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/7/5
 * Time: 10:32
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller
{
    /**
     * @var \Home\Model\MemberModel
     */
    //0.建本表类
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Member');
    }
    //1.注册页显示
    public function reg(){
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(getErr($this->_model));
            }
            if($this->_model->addMember() === false){
                $this->error(getErr($this->_model));
            }
            $this->success('注册成功,请到邮箱中激活您的邮件',U('index'),5);

        }else{
            $this->display();
        }
    }
    //2.发送给别人的邮件中的激活账户操作
    public function active($email,$register_token){
        //写条件,查询表中是否有一个记录的email和token和传过来的一致的,'status'=>0未激活状态
        $cond=['email'=>$email,'register_token'=>$register_token,'status'=>0];
        //进行查询
        if($this->_model->where($cond)->count()){  //查询成功
            //设置状态为1
            $this->_model->where($cond)->setField('status',1);
            $this->success('激活成功',U('Index/index'));
        }else{
            $this->error('验证失败',U('Index/index'));
        }

    }



}