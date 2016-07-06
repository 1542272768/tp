<?php
/**
 * Created by PhpStorm.
 * User: ym
 * Date: 2016/6/28
 * Time: 12:44
 */

namespace Admin\Logic;
//use MediaCore\Lib\Db\connectors\DbMysql;
//use MediaCore\Lib\Db\connectors\viod;

class MYSQLLogic implements DbMysql   //自写实现接口，继承其方法，在这写对数据的添加操作
{
//添加新数据时首先用到了getRow,getOne,query,insert这几个方法
//echo __METHOD__;dump(func_get_args());echo '<hr />';看用到哪些方法
    /**
     * DB connect
     *
     * @access public
     *
     * @return resource connection link
     */
    public function connect()
    {
        // TODO: Implement connect() method.
    }

    /**
     * Disconnect from DB
     *
     * @access public
     *
     * @return viod
     */
    public function disconnect()
    {
        // TODO: Implement disconnect() method.
    }

    /**
     * Free result
     *
     * @access public
     * @param resource $result query resourse
     *
     * @return viod
     */
    public function free($result)
    {
        // TODO: Implement free() method.
    }

    /**
     * Execute simple query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return resource|bool query result
     */
    public function query($sql, array $args = array())
    {
//        echo __METHOD__;
//        dump(func_get_args());
//        echo '<hr />';

        //1.获取所有数据,用以进行拼接
        $args=func_get_args();
        //2.弹出第一条sql语句,用数据中的值进来替换
        $sql=array_shift($args);
        //用正则方法分割sql语句,用以拼接语句
        $params=preg_split('/\?[TFN]/',$sql);
        //删除上面的最后个无用的空元素
        array_pop($params);
        //3.遍历取出值,放入sql1语句中:把$args的值放入$params中拼接
        $sql='';
        foreach($params as $key=>$val){
            $sql.=$val.$args[$key];
        }
        //执行update操作进行更新(query方法就是update)
        return M()->execute($sql);

    }

    /**
     * Insert query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false last insert id
     */
    public function insert($sql, array $args = array())
    {
//        echo __METHOD__;
//        dump(func_get_args());
//        echo '<hr />';

        //执行最终的插入数据操作
        //1.获取所有的数据
        $args=func_get_args();
        //获取args中的第0,1,2个数据
        $sql=$args[0];
        $tableName=$args[1];
        $params=$args[2];
        //拼接sql前半句语句:insert into googscategory set
        $sql=str_replace('?T',$tableName,$sql);
        //拼接sql后半句语句,把值放入
        $data=[];
        foreach($params as $key=>$val){
            $data[]=$key.'="'.$val.'"';
        }
        //完成最终的sql的语句
        $sql=str_replace('?%',implode(',',$data),$sql);
        //执行sql语句
        if(M()->execute($sql)!==false){
            return M()->getLastInsID();  //获取最后执行的ID
        }else{
            return false;
        }

    }

    /**
     * Update query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false affected rows
     */
    public function update($sql, array $args = array())
    {
//        echo __METHOD__;
//        dump(func_get_args());
//        echo '<hr />';
    }

    /**
     * Get all query result rows as associated array
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAll($sql, array $args = array())
    {
        // TODO: Implement getAll() method.
    }

    /**
     * Get all query result rows as associated array with first field as row key
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAssoc($sql, array $args = array())
    {
        // TODO: Implement getAssoc() method.
    }

    /**
     * Get only first row from query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array
     */
    public function getRow($sql, array $args = array())
    {
//        echo __METHOD__;
//        dump(func_get_args());
//        echo '<hr />';

        //1.获取所有数据,用以进行拼接
        $args=func_get_args();
        //2.弹出第一条sql语句,用数据中的值进来替换
        $sql=array_shift($args);
        //用正则方法分割sql语句,用以拼接语句
        $params=preg_split('/\?[TFN]/',$sql);
        //删除上面的最后个无用的空元素
        array_pop($params);
        //3.遍历取出值,放入sql1语句中:把$args的值放入$params中拼接
        $sql='';
        foreach($params as $key=>$val){
            $sql.=$val.$args[$key];
        }
        //执行sql语句进行查找第一行数据(getRow方法的要求)返回的是null
        $rows=M()->query($sql);
        return array_shift($rows);
    }

    /**
     * Get first column of query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array one level data array
     */
    public function getCol($sql, array $args = array())
    {
        // TODO: Implement getCol() method.
    }

    /**
     * Get one first field value from query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return string field value
     */
    public function getOne($sql, array $args = array())
    {
//        echo __METHOD__;
//        dump(func_get_args());
//        echo '<hr />';

        //1.获取所有数据,用以进行拼接
        $args=func_get_args();
        //2.弹出第一条sql语句,用数据中的值进来替换
        $sql=array_shift($args);
        //用正则方法分割sql语句,用以拼接语句
        $params=preg_split('/\?[TFN]/',$sql);
        //删除上面的最后个无用的空元素
        array_pop($params);
        //3.遍历取出值,放入sql1语句中:把$args的值放入$params中拼接
        $sql='';
        foreach($params as $key=>$val){
            $sql.=$val.$args[$key];
        }
        //执行sql语句进行查找
        $rows=M()->query($sql);
        //拿出第一行第一列的值(getOne这个方法的要求)
        $row=array_shift($rows);
        return array_shift($row);

    }
}