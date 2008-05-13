<?php
/**
 +------------------------------------------------------------------------------
 * 数据访问类
 +------------------------------------------------------------------------------
 * @package   Model
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class BaseModel extends Model
{//类定义开始

    /**
     +----------------------------------------------------------
     * 根据条件禁用表数据
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function forbid($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->execute('update '.$table.' set status=0 where status=1 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件禁用表数据
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function resume($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->execute('update '.$table.' set status=1 where status=0 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }
}//类定义结束
?>