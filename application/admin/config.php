<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // URL普通方式参数 用于自动生成
    'url_common_param'       => true,
    // URL参数方式 0 按名称成对解析 1 按顺序解析

     // 视图输出字符串内容替换
    'view_replace_str'       => [
    	'__SYSTEM__' => '/static/admin',
        '__ADMIN__'=> '/static/admin',
        '__INDEX__'=> '/static/index',
    ],

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],


      'Auth' => [
        'USER_ADMINISTRATOR' => '1', //超级管理员用户ID 不进行权限验证
        'NOT_AUTH_CONTROLLER' => 'index', //无需认证的控制器
        'NOT_AUTH_ACTION' => 'login,logout' , //无须认证的方法
    ],
    
    
];
