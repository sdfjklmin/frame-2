<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/11/20
 * Time: 10:06
 */

class Loading
{
    /** 自动加载类
     * @param $class
     * @throws Exception
     */
    public static function autoload($class)
    {
        $path = str_replace('_', '/', $class) . '.class.php';
        var_dump($path); echo '<br>';
        if(!$path) {
            throw new Exception('无效的引入地址!');
        }
        include_once($path);
    }

}
//sql自动加载
//spl_autoload_register(array('Loading', 'autoload'));

