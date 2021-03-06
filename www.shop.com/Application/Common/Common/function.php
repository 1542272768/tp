<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/24
 * Time: 17:26
 */
//1.将模型的错误信息转换成一个有序列表。
function getErr(\Think\Model $model){
    //先获取到错误
    $errors=$model->getError();
    //如果不是数组，则转化成数组
    if(!is_array($errors)){
        $errors=[$errors];
    }
    //如果是数组，则遍历输出后返回
    $html='<ol>';
    foreach($errors as $error){
        $html.='<li>'.$error.'</li>';
    }
    $html.='</ol>';
    return $html;
}

/**
 * 将一个关联数组转换成下拉列表
 * @param array  $data        关联数组,二维数组.
 * @param string $name_field  提示文本的字段名.
 * @param string $value_field value数据的字段名.
 * @param string $name        表单控件的name属性.
 * @return string 下拉列表的html代码.
 */
//2.写一个公用的下拉框,里面传(传入的数组值,select的name和class名,显示内容,option中的value值,默认值)
function arr2select(array $data,$name_field = 'name',$value_field = 'id',  $name = '',$default_value=''){
    $html='<select name="'.$name. '" class="' . $name . '">';
    $html.='<option value=""> 请选择 </option>';
    foreach($data as $key=>$val) {
        //编辑时显示选中的
        //由于get和post提交的数据都是字符串,(数字0和空字符串是相等),所以将遍历的数据变成string,然后强制类型转换后进行比较.
        if ((string)$val[$value_field] === $default_value) {
            $html .= '<option value="' . $val[$value_field] . '" selected="selected">' . $val[$name_field] . '</option>';
        } else {   //
            $html .= '<option value="' . $val[$value_field] . '">' . $val[$name_field] . '</option>';
        }
      }
    $html .= '</select>';
    return $html;
}

//3.加盐加密
function salt_mcrypt($password,$salt){
    return md5(md5($password).$salt);
}

//4.用户登录时的数据保存到session中:获取和设置用户session
function Login($data=null){   //$data默认为空
    if(is_null($data)){      //如果传过来的$data为null,则建立一个名叫USERINFO的session
        return session('USERINFO');
    }else{    //如果传过来的$data不为null,则把$data放入session
        session('USERINFO',$data);
    }
}

//5.每次登录时将用户可用的权限路径放入session中
function permission_pathes($data=null){
    if(is_null($data)){  //如果传过来的$data为空,则建立一个名叫USERINFO的session
        $pathes=session('PERMISSION_PATHES');
        //如果未登录,$pathes为null,但是后面我们要用到array_mergy需要是数组,所以我们这里把它转化了
        if(!is_array($pathes)){   //如果不是数组,就把它变成空数组.
            $pathes = [];
        }
        return $pathes;
    }else{
        session('PERMISSION_PATHES',$data);  //放入登录时传入的值
    }
}
//6.每次登录时将用户可用的权限ID放入session中
function permission_ids($data=null){
    if(is_null($data)){     //如果传过来的$data为空,则建立一个名叫USERINFO的session
        $pids=session('PERMISSION_PIDS');
        //如果未登录,$pids为null,但是后面我们要用到array_mergy需要是数组,所以我们这里把它转化了
        if(!is_array($pids)){
            $pids = [];
        }
        return $pids;
    }else{
        session('PERMISSION_PIDS',$data);  //放入登录时传入的值
    }
}

//7.和6要谁
function permission_pids($data=null){
    if(is_null($data)){
        $pids = session('PERMISSION_PIDS');
        if(!is_array($pids)){
            $pids = [];
        }
        return $pids;
    }else{
        session('PERMISSION_PIDS',$data);
    }
}

//8.利用PHPmail发送邮件给别人的操作
function sendMail($email,$subject,$content){
    //引入PHPmail
    Vendor('PHPMailer.PHPMailerAutoload');
    //创类
    $mail=new \PHPMailer();
    $mail->isSMTP();                          // 用SMTP方式方式邮件
    $mail->Host       = 'smtp.163.com';       //填写发送邮件的服务器地址,固定
    $mail->SMTPAuth   = true;                 // 使用smtp验证
    $mail->Username   = 'aa412300963@163.com';  // 发件人账号名(邮箱帐号)
    $mail->Password   = 'GG189189';            // 授权码(邮箱密码)
    $mail->SMTPSecure = 'ssl';                 // 使用协议,具体是什么根据你的邮件服务商来确定
    $mail->Port       = 465;                  // 使用的端口

    $mail->setFrom('aa412300963@163.com', 'YM商城');  //发件邮箱,发件人名字,注意:邮箱地址必须和上面的$mail->Username一致
    $mail->addAddress($email);                     // 收件人
    $mail->isHTML(true);                           // 是否是html格式的邮件

    $mail->Subject = $subject;   //发送的标题
    $mail->Body    = $content;   //发送的正文
    $mail->CharSet = 'UTF-8';    //发送的格式

    //调用发送
    if($mail->send()){  //如果发送成功
        return ['status'=>1, 'msg'=>'发送成功',];//返回信息,设置为激活状态
    }else{
        return ['status'=>0, 'msg'=>$mail->ErrorInfo,];
    }
}

//9.建立redis类,获得redis
function get_redis(){
         $redis=new Redis(); //建类
         $redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));  //连接
         return $redis;
    }

//10.number_format(变量,保留2位,以点分割,千位分隔符):
//金钱表示形式：100 表示为 100.00()
function nf($num){
    return number_format($num,2,'.','');
}

