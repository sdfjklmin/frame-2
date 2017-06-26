<?php
class IndexController extends Action
{
    public function show()
    {
        $this->dis() ;
    }
    public function index()
    {
        $d = module('Index')->index() ;
        $this->assign('info',$d) ;
        $this->dis('show') ;
    }

    public function abc()
    {
        echo 'this is abc' ;
    }
    public function look()
    {
        $this->dis('Test/test');
    }

    public function getM()
    {
        $this->dis('Test/test');

    }
}