<?php
namespace app\admin\model;
/**
 * 基础控业务处理
 */
use think\Model;
use think\Db;
class Advert extends Model {
    protected $autoWriteTimestamp = true;

     // 定义时间戳字段名
    protected $createTime = 'add_time';
    protected $updateTime = 'last_time';
    
}