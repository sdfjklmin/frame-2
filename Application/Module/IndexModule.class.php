<?php
class IndexModule
{
    public function getM()
    {

    }

    public function index()
    {
        $d = D('BUser')->where('id=3')->del();
//        $check = D('BUser')->save(['name'=>'战术','age'=>18,'id'=>3]);
//        D('BUser')->field('id,name,age')->select() ;
        pr($d,D()->endSql());
    }
}