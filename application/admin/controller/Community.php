<?php
namespace app\admin\controller;

class Community extends Base
{

	protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

        $this->model = model('Community');
        $this->allowField = true;
      
    }
    /**
     * 社区管理
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function index(){

        $keyword = trim( input('keyword') );
        $start_time = input( 'start_time');
        $end_time = input( 'end_time');
        $type = input('type');

        list($where, $sort, $order, $offset, $limit) = $this->buildparams( );

        $listModel = $this->model
                ->where( $where )
                ->order($sort, $order);
        if( $keyword ){
            $listModel->where( 'content' , 'like' , $keyword.'%');
        }
        if( $type ){
        	$listModel->where( 'type' , $type);	
        }

        if( $start_time  && $end_time){
            $listModel->where( 'add_time' , 'BETWEEN' , array($start_time , $end_time ) );
        }
        $list = $listModel->paginate(10 ,false, ['query'=>request()->param()]);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list' , $list);
        $this->assign('page', $page);
        return $this->fetch();
    }
}