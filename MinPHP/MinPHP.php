<?php

# 定义常量 针对MinPHP目录
defined('APP_PATH')  or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('MIN_PATH')  or define('MIN_PATH', dirname(__FILE__).'/'); # MinPHP
defined('CORE_PATH') or define('CORE_PATH',MIN_PATH.'core/') ; # MinPHP/core
defined('COM_PATH')  or define('COM_PATH',MIN_PATH.'common/') ; # MinPHP/common
defined('LANG_PATH') or define('LANG_PATH',MIN_PATH.'lang/') ; # MinPHP/common
defined('MAKE_FILE') or define('MAKE_FILE',true) ; # 框架目录生成
# 运行文件引入
require CORE_PATH.'App.class.php' ;