<?php
# 核心基类,不允许实例化
abstract class Action{

    protected $view = null ;
    protected $wVar = array() ;
    public function __construct()
    {
    }

    # 页面显示
    public function dis($str='',$cache=false)
    {
        $v = MONITOR ; # 文件夹
        $h = METHOD ;     # 请求页面

        if($str) {
            if(strpos($str,'/') === false) {
                $h = $str ;
            }else{
                $i = explode('/',$str) ;
                if(count($i)!=2) {
                    pr_e('format error');
                }else{
                    $v = $i[0] ;
                    $h = $i[1] ;
                }
            }

        }
        $local = VIEW_PATH.$v.'/'.$h.'.html' ;
        if($cache){
            #code 缓存处理
        }
        # 文件存在
        if(file_exists($local)) {
            require_once $local ;
        }else{
            pr_e('not find this html');
        }
    }


    # 页面赋值
    public function assign($name,$value='')
    {
        $this->wVar[$name] = $value ;
    }



}