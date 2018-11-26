<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


function callfun1($content,$key){
    return '回调函数1：'.$key.'-'.$content;
}

/**
 * 获取菜单信息
 * @author lgp
 */
function getAllMenus( &$data ){
    $node = model('Node');
    foreach ($data as $k => $v) {
        $where['type'] = 1;
        $where['pid'] = $v['id'];
        $children = $node->where($where)->order('sort asc')->select();
        if ($children) {
            getAllMenus($children);
            $data[$k]['children'] = $children;
        } else {
            continue;
        }
    }
    return $data;
}

/**
 * 获取菜单信息
 * @author lgp
 */
function getMenus( &$data ){
    $node = model('Node');
    foreach ($data as $k => $v) {
        $where['is_show'] = 1;
        $where['type'] = 1;
        $where['pid'] = $v['id'];
        $children = $node->where($where)->order('sort asc')->select();
        if ($children) {
            getMenus($children);
            $data[$k]['children'] = $children;
        } else {
            continue;
        }
    }
    return $data;
}


/**
 * 无限级分类  递归查询
 * @author lgp
 */
function getTree(&$data, $pid = 0, $count = 0){
    if (!isset($data['odl'])) {
        $data = array('new' => array(), 'odl' => $data);
    }
    foreach ($data['odl'] as $k => $v) {
        if ($v['parent_id'] == $pid) {
            $v['Count'] = $count;
            $data['new'][] = $v;
            unset($data['odl'][$k]);
            getTree($data, $v['id'], $count + 1);
        }
    }
    $data1 = $data['new'];
    return $data1;
}


function read_all_dir($dir){
    $result = array();
    $handle = opendir($dir);
    if ( $handle ) {
        while ( ($file = readdir($handle)) !== false) {
            if ( $file != '.' && $file != '..' ) {
                $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                if ( is_dir( $cur_path ) ) {
                    $result['dir'][$file] = read_all_dir($cur_path);
                } else {
                    $result['file'][] = $file;
                }
            }
        }
        closedir( $handle );
    }
    return $result;
}




/**
 * 生成菜单名称
 * @author lgp
 */
function build_tpl_title(){
    //获取当前控制器名称
    $controller = strtolower(think\Request::instance()->controller());
    //获取当前方法名称
    $action = strtolower(think\Request::instance()->action());

    $title = '';//model('node')->where( array('url' => $controller . '/' . $action ) )->field('title')->find();
    return $title;
    
}


/**
 * 生成头部功能按钮
 * @author lgp
 */
function build_toolbar($btns = NULL, $actions = null , $type = null ){

    //$auth = think\library\Auth::instance();
    $controller = strtolower(think\Request::instance()->controller());
    $btns = $btns ? $btns : '' ;
    $actions = $actions ? $actions :'' ;
    $btns = is_array($btns) ? $btns : explode(',', $btns);
    $actions = is_array($actions) ? $actions : explode(',', $actions);


    $btnAttr = [
        'add'     => [ url($controller . '/add') , '新增'],
        'edit'    => [ url($controller . '/edit') , '编辑'],
        'del'     => [ url($controller . '/del') , '删除'],
    ];
    // $btnAttr = array_merge($btnAttr, $attr);
    $html = [];
    foreach ($btns as $k => $v)
    {
        if(isset( $actions[$k] ) && $actions[$k] ){
            $btnAttr[trim($v) ][0] = $actions[$k];
        }
       
        //如果未定义或没有权限
        /*if (!isset($btnAttr[$v]) || ($v !== 'refresh' && !$auth->check("{$controller}/{$v}")))
        {
            continue;
        }*/
        list($event,  $text) = $btnAttr[trim($v) ];
        if(isset( $type[$k] ) && $type[$k] && $type[$k] == 'model' ){
            $html[] = '<button data-target="#modelPage_'.$v.'" type="button" data-toggle="modal"  data-page=" ' . $event . ' "   data-page-height="500"  class=" btn btn-primary btn-outline margin-bottom-10 btn-sm " > ' . $text . '</button>';
        }else if(isset( $type[$k] ) && $type[$k] && $type[$k] == 'url' ){
            $html[] = '<button type="button"  onclick=" location.href = \'' . $event . ' \'" " class=" btn btn-primary btn-outline margin-bottom-10 btn-sm " > ' . $text . '</button>';    
        } else {
            $html[] = '<button type="button"  onclick=" ' . $event . ' " class=" btn btn-primary btn-outline margin-bottom-10 btn-sm " > ' . $text . '</button>';    
        }
        
    }
    return implode(' ', $html);

}


/**
 * 生成功能按钮
 * @author lgp
 * @param $btns 按钮标识  'add,edit'  按钮名称识别语言包
 * @param $actions 按钮请求方法
 */
function build_tool_btn($btns = NULL, $actions = null , $type = null   ){
    //$auth = think\library\Auth::instance();
    $controller = strtolower(think\Request::instance()->controller());
    $btns    = $btns ? $btns : '' ;
    $actions = $actions ? $actions :'' ;
    $btns    = is_array($btns) ? $btns : explode(',', $btns);
    $actions = is_array($actions) ? $actions : explode(',', $actions);

    $btnAttr = [];
    $html = [];
    foreach ($btns as $k => $v)
    {
        $btnAttr[ trim($v) ] = [ ];
        if(isset( $actions[$k] ) && $actions[$k] ){
            $btnAttr[ trim($v) ][] = $actions[$k];
            $btnAttr[ trim($v) ][] = lang($v);
        }
       
        //如果未定义或没有权限
        /*if (!isset($btnAttr[$v]) || ($v !== 'refresh' && !$auth->check("{$controller}/{$v}")))
        {
            continue;
        }*/
        list($event,  $text) = $btnAttr[trim($v) ];
        if(isset( $type[$k] ) && $type[$k] && $type[$k] == 'model' ){
            $html[] = '<button data-target="#modelMenuPage_'.$v.'" type="button" data-toggle="modal"  data-page=" ' . $event . ' "  data-page-height="500" data-page-height="auto" class=" btn btn-primary btn-outline margin-bottom-10 btn-sm " > ' . $text . '</button>';
        }else if(isset( $type[$k] ) && $type[$k] && $type[$k] == 'url' ){
            $html[] = '<button type="button"  onclick=" location.href = \'' . $event . ' \'" " class=" btn btn-primary btn-outline margin-bottom-10 btn-sm " > ' . $text . '</button>';    
        }  else {
            $html[] = '<button type="button" onclick=" ' . $event . ' " class=" btn btn-primary btn-outline margin-bottom-10 btn-sm " > ' . $text . '</button>';
        }

        
    }
    return implode(' ', $html);

}


/**
 * 生成功能按钮
 * @author lgp
 * @param $btns 按钮标识  'add,edit'  按钮名称识别语言包
 * @param $actions 按钮请求方法
 */
function build_tool_more_btn($btns = NULL, $actions = null  ){
    //$auth = think\library\Auth::instance();
    $controller = strtolower(think\Request::instance()->controller());
    $btns    = $btns ? $btns : '' ;
    $actions = $actions ? $actions :'' ;
    $btns    = is_array($btns) ? $btns : explode(',', $btns);
    $actions = is_array($actions) ? $actions : explode(',', $actions);

    $btnAttr = [];
    $html = [];

    foreach ($btns as $k => $v)
    {
        $btnAttr[ trim($v) ] = [ ];
        if(isset( $actions[$k] ) && $actions[$k] ){
            $btnAttr[ trim($v) ][] = $actions[$k];
            $btnAttr[ trim($v) ][] = lang($v) ? lang($v) :$v;
        }
       
        //如果未定义或没有权限
        /*if (!isset($btnAttr[$v]) || ($v !== 'refresh' && !$auth->check("{$controller}/{$v}")))
        {
            continue;
        }*/
        list($event,  $text) = $btnAttr[trim($v) ];

        $html[] = '<li role="presentation"> <a  href="javascript:;"  onclick=" ' . $event . ' " role="menuitem" > ' . $text . '</a></li>';
    }
    return implode(' ', $html);

}

