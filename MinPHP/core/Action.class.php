<?php
# 核心基类,不允许实例化
abstract class Action{

    protected $view = null ;
    protected $wVar = array() ;
    public function __construct()
    {
        $this->_view();
    }

    protected function _view()
    {
        static $_view = [] ;
        if(!empty($_view)) {
            $this->view = $_view[0] ;
            return ;
        }
        $view = new View() ;
        $this->view = $view ;
        $_view[0]   = $view ;
        return ;
    }

    # 页面显示
    public function dis($str='',$cache=false)
    {
        $this->view->dis($str,$cache) ;
    }


    # 页面赋值
    public function assign($name,$value='')
    {
        $this->view->assign($name,$value) ;
    }



}