<?php

# 数据库类
class Db
{
    # 数据库配置
    protected $config ;

    # 连接对象
    protected $linkDb = null ;

    # 系统连接对象
    protected $sysDb  = null ;

    # 数据表名称
    protected $tab ;

    # 数据表前缀
    protected $tabFix ;

    # 数据库查询语句
    public $queryStr = '' ;

    # 查询字段
    protected $field = '*' ;

    # 数据表字段
    protected $tabField = '' ;

    # 查询条件
    protected $where = '' ;

    # 查询条件 WHERE GROUP ORDER
    protected $comWhere = '' ;

    # 查询表达式
    protected $selectSql = ' SELECT %FIELDS% FROM %TAB% %WHERE% %GROUP% %ORDER% %LIMIT% ' ;

    # 更新表达式
    protected $upSql = ' UPDATE %TAB% SET %DATA% %WHERE% ' ;

    # 删除表达式
    protected $delSql=' DELETE FROM %TAB% %WHERE% ' ;

    # 新增表达式
    protected $addSql = ' INSERT INTO %TAB% ( %FIELD% ) VALUES ( %VALUE% ) ' ;

    public function __construct($name='',$connect='')
    {

        # 连接配置
        $this->config = json_decode(DB_CONF,true);
        if(empty($this->config)) {
            pr_e('数据库配置引入错误');
        }
        # 表前缀
        $this->tabFix = ( $this->config['DB_FIX'] ? $this->config['DB_FIX'] : '' ) ;
        unset($this->config['DB_FIX']);
        # 数据表名
        if(!empty($name)) {
            $this->tab = ltrim($this->tabFix.$name) ;
        }

        # 当前数据库连接
        $this->connect($this->config) ;

    }

    # 数据库连接
    private function connect($config = []) {

        static $linkDb = [] ;
        # 参数判断
        if(empty($config)) {
            # 关闭数据库连接
            $this->linkDb = null ;
            unset($linkDb[0]);
            return ;
        }

        # 连接优化
        if(!empty($linkDb)) {
            $this->linkDb = $linkDb[0] ;
            return ;
        }

        # 字段检查
        $check = ['DB_TYPE','DB_HOST','DB_NAME','DB_ROOT','DB_PWD'];
        foreach($config as $key=>$value)
        {
            if(!in_array($key,$check)) {
                pr_e('数据库参数错误');
            }
        }

        try{
            # 连接参数
            $dbh = new PDO($config['DB_TYPE'].':host='.$config['DB_HOST'].';dbname='.$config['DB_NAME'], $config['DB_ROOT'],$config['DB_PWD']);
            # 编码设置
            $dbh->exec('set names utf8');
            $linkDb[0] = $dbh ;
            $this->linkDb =  $linkDb[0] ;
            if(isset($this->tab) && !empty($this->tab)) {
                # mysql系统连接
                $this->sysConnect($config) ;
                # 获取对应表字段
                $this->getTabField($config['DB_NAME']);


            }
        }catch(PDOException $e){
            throw new PDOException($e->getMessage())  ;
        }
        return ;
    }

    # mysql information_schema连接
    protected function sysConnect($config)
    {
        static $linkSys = [] ;
        # 连接优化
        if(!empty($linkSys)) {
            $this->sysDb = $linkSys[0] ;
            return ;
        }
        $sysDb = new PDO($config['DB_TYPE'].':host='.$config['DB_HOST'].';dbname=information_schema', $config['DB_ROOT'],$config['DB_PWD']);
        $sysDb->exec('set names utf8');
        $this->sysDb = $sysDb ;
        $linkSys[0]  = $sysDb ;
        return ;

    }

    # 获取对应表字段
    protected function getTabField($db)
    {
        $sql = " SELECT	GROUP_CONCAT(COLUMN_NAME) AS  tabField
                FROM `COLUMNS`
                WHERE `TABLE_SCHEMA` LIKE '%".$db."%'AND `TABLE_NAME` LIKE '%".$this->tab."%'
                GROUP BY TABLE_NAME
                LIMIT 0,300 " ;
        $info = $this->sysDb->query($sql);

        $r = '' ;
        if(!empty($info)) {
            while($row = $info->fetch(PDO::FETCH_ASSOC)){
                $r = $row ;
            }
        }
        $this->tabField = $r['tabField'] ;
        return ;
    }

    # 查询过滤字段
    protected function _field()
    {
        if(($this->field != '*') && $this->tabField) {
            $field = explode(',',$this->field);
            $tabField = explode(',',$this->tabField);
            $diff = array_diff($field,$tabField);
            if(!empty($diff)) {
                foreach($diff as $k => $v)
                {
                    unset($field[$k]) ;
                }
            }
            $this->field = implode(',',$field) ;
        }
    }

    # 查询数据
    public function select()
    {
        $this->_field();
        $sql   = str_replace(
            ['%FIELDS%','%TAB%','%WHERE%','%GROUP%','%ORDER%','%LIMIT%'],
            [$this->field,$this->tab,$this->where,'','',''],
            $this->selectSql
        ) ;

        $m =  $this->linkDb ->query($sql) ;
        if(false === $m) {
            pr_e($sql);
        }
        $this->queryStr = $sql ;
        $r = [] ;
        if(!empty($m)) {
            while($row = $m->fetch(PDO::FETCH_ASSOC)){
                $r[] = $row ;
            }
        }
        return $r ;
    }

    protected function _before()
    {
        if(!empty($this->where)){
            $this->comWhere = " WHERE ".$this->where ;
        }
    }


    # 查询字段
    public function field($field)
    {
        $this->field = $field ;
    }


    # 查询条件
    public function where($where)
    {
        $this->where = " WHERE ".$where ;
    }

    # 保存操作
    public function save($set)
    {
        return $this->_do(__FUNCTION__,func_get_args()) ;
    }

    # 删除操作
    public function del()
    {
        return $this->_do(__FUNCTION__,func_get_args()) ;
    }

    # 新增操作
    public function add($f,$v)
    {
        return $this->_do(__FUNCTION__,func_get_args()) ;
    }

    # 操作公类(增,删,改)
    protected function _do($f,$arv)
    {
        $sqlType    =   [
            'add'   =>str_replace(['%TAB%','%FIELD%','%VALUE%'],[$this->tab,@$arv[0],@$arv[1]],$this->addSql),
            'del'   =>str_replace(['%TAB%','%WHERE%'],[$this->tab,$this->where],$this->delSql) ,
            'save'  =>str_replace(['%TAB%','%DATA%','%WHERE%'],[$this->tab,@$arv[0],$this->where],$this->upSql),
        ]  ;
        $sql    =   $sqlType[$f];
        $data = $this->linkDb->prepare($sql);
        if(!empty($data))
            $this->queryStr = $data->queryString ;
        return $data->execute();
    }

    # 数据字段过滤
    public function _doField(&$d)
    {
        $tabField = explode(',',$this->tabField);
        $diff  = array_diff(array_keys($d),$tabField) ;
        if(!empty($diff)) {
            foreach($diff as $k => $v)
            {
                unset($d[$v]);
            }
        }
    }

}