<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/27
 * Time: 16:47
 */

namespace Admin\Controller;

use Think\Controller;

class UploadController extends Controller
{
    //上传图片
    public function uploadImg(){
        //获取上传配置文件
        $option=C('UPLOAD_SETTING');
        //创建上传类
        $upload= new \Think\Upload($option);
        //获取文件信息,并使用上传功能：$_FILES['file_data']=》$_FILES常量获取html页面传的name为file_data的数据
        $file_info=$upload->uploadOne($_FILES['file_data']);
        //判断文件上传成功与否（把logo地址保存进数据库）
        if($file_info){ //上传成功
            //判断是哪种上传方式(在配置中改上传方式)
            if($upload->driver=='Qiniu'){
                //取得设置好的url
                $file_url=$file_info['url'];  //???
            }else{
                //和配置文件中不一样，要用小写的savepath。
                $file_url=BASE_URL.'/'.$file_info['savepath'].$file_info['savename'];
            }
            //成功返回的信息
            $return=[
                'file_url'=>$file_url,
                'msg'=>'上传成功',
                'status'=>1
            ];
        }else{  //上传失败
            $return=[
                'file_url'=>'',
                'msg'=>$upload->getError(),
                'status'=>0
            ];
        }
        //转化成JSON对象返回给html页面
        $this->ajaxReturn($return);
    }
}