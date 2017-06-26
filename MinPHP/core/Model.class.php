<?php
class Model
{

    # Dd对象
    protected $db  ;

    public function __construct($name='',$linkNum= 0)
    {
        $this->dbLink($name,$linkNum= 0);

    }

    # 数据库对象
    protected function dbLink($name='',$linkNum= 0)
    {
        static $_link = [] ;
        if(!empty($_link) && empty($name)) {
            $this->db = $_link[$linkNum];
            return ;
        }
        if(isset($_link[$name])) {
            $this->db = $_link[$name] ;
            return ;
        }
        $db = new Db($name) ;
        $_link[$linkNum] = $db ;
        $_link[$name] = $db ;
        $this->db = $db ;
        return ;
    }

    # 查询字段
    public function field($str='')
    {
        $field = '*' ;
        if(!empty($str)) {
            if(!is_string($str)) {
              pr_e('字段信息只支持字符串');
            }
            $field = explode(',',$str) ;
            $field = implode(',',$field) ;
        }
        $this->db->field($field);
        # 有连贯性操作的时候返回当前对象
        return $this ;
    }

    # 查询条件
    public function where($str='')
    {
        if(empty($str)) {
           pr_e('缺少条件参数');
        }
        $where = '' ;
        if(is_string($str)) {
            $where = $str ;
        }elseif(is_array($str)) {
            pr($str);
        }
        $this->db->where($where) ;
        return $this ;
    }


    # 查询数据
    public function select()
    {
        return $this->db->select();
    }

    # 获取SQL
    public function endSql()
    {
        return $this->db->queryStr ;
    }

    # 保存数据
    public function save($d=[])
    {
        if(empty($d) || !is_array($d))
            pr_e('参数错误') ;
        $this->db->_doField($d);
        $where  = '' ;
        $set    =   '' ;
        foreach($d as $k => $v)
        {
            if($k == 'id') {
                $where = ' id = '.$v ;
            }else{
                $set .= $k.' = "'.$v.'",' ;
            }
        }
        $set = rtrim($set,',') ;
        $this->db->where($where) ;
        return $this->db->save($set);
    }

    # 删除
    public function del()
    {
        return $this->db->del();
    }

    # 新增
    public function add($d=[])
    {
        if(empty($d))
            pr_e('缺少新增参数');
        $this->db->_doField($d);
        $field = '' ;
        $value = '' ;
        foreach($d as $k => $v)
        {
            $field .= '`'.$k.'`,' ;
            $value .= '"'.$v.'",' ;
        }
        $field = rtrim($field,',');
        $value = rtrim($value,',');
        return $this->db->add($field,$value);
    }
}