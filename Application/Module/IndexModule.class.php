<?php
class IndexModule
{
    public function getM()
    {

    }

    public function index()
    {
//        $data = [
//            'name'=>'test',
//            'age'=>'10',
//            'address'=>'min data',
//        ] ;
//        $d = D('BUser')->add($data);
//        $d = D('BUser')->save(['name'=>'梦醒时分','age'=>10,'id'=>14,'abc222'=>'abc1','def2323'=>'def2']);
          $d = D('BUser')->field('test,abc,age,def,id,ghi')->select() ;
        pr($d,D()->endSql());
    }
}