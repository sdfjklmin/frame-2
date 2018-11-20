<?php
namespace core ;

class App
{

	# 执行方法
	public static function run()
	{
		# 优先级
		self::setCharset();		# 字符
		self::setConst() ;		# 常量
		self::buildLib() ;		# 引入文件
		self::langSet()	;		# 语言设置
		self::makeFile() ;		# 生成系统文件
		self::setError();		# 错误
		self::iniUrl();			# url
		self::disController();	# 控制器

	}

	# 字符设置
	private static function setCharset()
	{
		header('content-type:text/html;charset=utf-8');
		# 跨域设置
		header('Access-Control-Allow-Origin: * ');

	}

	# 常量设置
	private static function setConst()
	{
		defined('APP_LIC') 		or 	define('APP_LIC',APP_PATH.'Application/') ;
		defined('APP_COM_PATH') or 	define('APP_COM_PATH',APP_LIC.'Common/') ;
		defined('CONTROL_PATH') or 	define('CONTROL_PATH',APP_LIC.'Controller/') ;
		defined('MODULE_PATH') 	or 	define('MODULE_PATH',APP_LIC.'Module/') ;
		defined('VIEW_PATH') 	or 	define('VIEW_PATH',APP_LIC.'View/') ;

	}

	# 自动创建目录结构
	private static function makeFile()
	{
		if(true === MAKE_FILE) {
			$dir	= 	scandir(APP_PATH) ;
			$level	= 	['Application'];
			$level2	=	['Controller','Module','View','Common'] ;
			foreach($dir as $k=>$v)
			{
				if(in_array($v,$level)) {
					if(is_dir($dir[$k])) {
						$dir2 = scandir($dir[$k]) ;
						foreach($dir2 as $n=>$m) {
							if(in_array($m,$level2)) {
								foreach($level2 as $r=>$t) {
									if($m == $t) {
										unset($level2[$r]) ;
									}
								}
							}
						}
					}else{
						# 创建目录结构
						self::makeDir($level2) ;
					}
				}else{
					# 创建目录结构
					self::makeDir($level2) ;
				}
			}
			if(!empty($level2)) {
				foreach($level2 as $x) {
					if(!file_exists(APP_PATH.'Application/'.$x)) {
						if(false == mkdir(APP_PATH.'Application/'.$x) ) {
							exit('生成目录结构错误');
						}
					}
				}
			}
		}
	}

	# 生成文件夹
	protected static function makeDir($level2)
	{
		if(!file_exists(APP_PATH.'Application')) {
			if(false == mkdir(APP_PATH.'Application') ) {
				exit('生成目录结构错误');
			}else{
				if(!empty($level2)) {
					foreach($level2 as $x) {
						if(!file_exists(APP_PATH.'Application/'.$x)) {
							if(false == mkdir(APP_PATH.'Application/'.$x) ) {
								exit('生成目录结构错误');
							}
						}
					}
				}
			}
		}
	}

	# 设置错误信息
	private static function setError()
	{
	}

	# 解析url
	private static function iniUrl()
	{
		# 非参数模式 web.com/index/abc?c=Index&a=index
		if(isset($_SERVER['REDIRECT_URL'])) {
			# $_SERVER['QUERY_STRING']  c=Index&a=index
			$checkUrl = explode('/',$_SERVER['REDIRECT_URL']) ;
			if(empty($checkUrl[0])) {
				unset($checkUrl[0]) ;
				sort($checkUrl) ;
			}
			if(count($checkUrl) !==2) {
				pr_e('请求格式错误');
			}
			$plat = 'Admin' ;
			$monitor = ucfirst(strtolower($checkUrl[0])) ;
			$method  = strtolower($checkUrl[1]) ;

		}else{
			# 参数模式
			$plat 		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : 'Admin';
			$monitor 	= isset($_REQUEST['c']) ? ucfirst(strtolower($_REQUEST['c'])) : 'Index';
			$method 	= isset($_REQUEST['a']) ? $_REQUEST['a'] : 'index';
		}
		# 定义常量
		define('PLAT', $plat); //Admin或者Home 这里没有设置前后台项目
		define('MONITOR', $monitor);//指定控制器或者Index
		define('METHOD', $method);//指定方法或者index
	}

	# 分发控制器
	private static function disController()
	{

		//拿到对应的平台，控制器和方法
		$module = MONITOR . 'Controller';
		$action = METHOD;
		//对应文件引入
		if(!file_exists(CONTROL_PATH . MONITOR . 'Controller.class.php'))
			pr_e('无法加载模块:'.MONITOR,0);
		require_once CONTROL_PATH . MONITOR . 'Controller.class.php';
		//当前是Core空间，需要转到对应的控制器空间，空间命名规则：平台+对应属性。
		//new \Home\Controller\IndexController();
		//$module =  '\\controllers\\' . $module;//构建一个完全限定名称访问的路径
		if(!class_exists($module))
			pr_e('控制器'.$module.'不存在');
		$module = new $module;
		if(!method_exists($module,$action))
			pr_e('非法操作:'.$action,0);
		$module->$action();
	}

	# 引入项目所需要的文件
	private static function buildLib()
	{
		# 公共方法
		require_once(COM_PATH.'/common.php');
		# 系统类
		$list = [
			CORE_PATH.'/Action.class.php' ,
			CORE_PATH.'/Model.class.php' ,
			CORE_PATH.'/Db.class.php' ,
			//CORE_PATH.'/Error.class.php' ,
			CORE_PATH.'/Log.class.php' ,
			CORE_PATH.'/View.class.php' ,
		] ;
		foreach($list as $key=>$value) {
			if(is_file($value)) {
				require_once($value);
			}
		}
		# 连接配置
		$db_conf = require_once(APP_PATH.'config.php');
		if(!empty($db_conf)) {
			define('DB_CONF',json_encode($db_conf,true)) ;
		}
	}

	# 语言设置
	private static function langSet()
	{
		//require_once LANG_PATH.'tip.php' ;
	}
}

# 运行入口
App::run() ;
return ;