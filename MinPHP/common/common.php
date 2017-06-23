<?php

/*系统方法*/

# 调试打印
function pr()
{
    if(func_get_args()) {
        echo '<pre>' ;
        foreach(func_get_args() as $v) {
            echo '[type]'.gettype($v),'<br>' ;
            echo '[data]' ;
            if(is_bool($v)) {
                var_dump($v) ;
            }else{
                print_r($v) ;
            }
            echo '<br><br>' ;
        }
    }
    exit ;
}

# 错误信息
function pr_e($s,$t=1)
{
    if(empty($e) && !isset($s)) {
        exit('Error : not find this arg');
    }
    $tip = "<span style='color: red'>".[0=>'',1=>'Fatal error : ',2=>'Warning : ',3=>'Notice : '][$t]."</span>".$s ;
    exit($tip);
}

# module实例化
function module($m='')
{
    # 无参数
    if(empty($m) && !isset($m)) {
        pr_e('not find this arg') ;
    }else{
        # 指定实例
        $local  =  MODULE_PATH.$m.'Module.class.php' ;
        $module = $m.'Module' ;
        if(file_exists($local)) {
            require_once $local ;
            return new $module ;
        }else{
            pr_e('not find '.$m.' module');
        }

    }
}

# 文件引入优化
function require_cache($filename)
{
    static $_requireFile = [] ;
    # 检查是否已经调用
    if(!isset($_requireFile[$filename])) {
        # 判断文件是否存在
        if(file_exists($filename)) {
            require $filename ;
            # 引入成功后添加标示
            $_requireFile[$filename] = true ;
        }else{
            $_requireFile[$filename] = false ;
        }
    }
    return $_requireFile[$filename] ;
}

# 数据库实例化
function D($mod='')
{
    # 默认判断
    if(empty($mod)) {
        return new Model;
    }
  /* 这里不能用静态,每次创建对象的
    时候可能会去改变对应的属性
    static $_mod = [] ;
    if(isset($_mod[$mod])) {
        return $_mod[$mod] ;
    }*/

    # 转换字符串格式
    $mod2 = strFormat($mod) ;
    $rMod =  new Model($mod2);
    // $_mod[$mod] = $rMod ;
    return $rMod ;
}

# 字符串转换 BUserTest b_user_test
function strFormat($name){
    $temp_array = array();
    for($i=0;$i<strlen($name);$i++){
        $ascii_code = ord($name[$i]);
        if($ascii_code >= 65 && $ascii_code <= 90){
            if($i == 0){
                $temp_array[] = chr($ascii_code + 32);
            }else{
                $temp_array[] = '_'.chr($ascii_code + 32);
            }
        }else{
            $temp_array[] = $name[$i];
        }
    }
    return implode('',$temp_array);
}

# 获取系统参数

function C($a)
{
    if(is_string($a)) {

    }

    if(is_array($a)) {

    }
}

/*错误方法*/
function _404()
{

}

function set_http_code($code)
{
    # 常见状态码
    static $_code = [
                '200'   =>  'OK',
                '301'   =>  'Moved Permanently',
                '302'   =>  'Found',
                '304'   =>  'Not Modified',
                '307'   =>  'Temporary Redirect',
                '400'   =>  'Bad Request',
                '401'   =>  'Unauthorized',
                '403'   =>  'Forbidden',
                '404'   =>  'Not Found',
                '410'   =>  'Gone',
                '500'   =>  'Internal Server Error',
                '501'   =>  'Not Implemented',
    ] ;
    $_code[$code] ;
}

