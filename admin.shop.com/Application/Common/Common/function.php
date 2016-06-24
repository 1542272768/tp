<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/24
 * Time: 17:26
 */
//将模型的错误信息转换成一个有序列表。
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