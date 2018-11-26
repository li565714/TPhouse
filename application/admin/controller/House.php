<?php
namespace app\admin\controller;

class House extends Base
{   

	protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

        $this->model = model('house');
        $this->allowField = true;

        //房屋配置
        $houseConfig = get_dict(5);
        $this->assign('houseConfig' , $houseConfig);

        //出租类型
        $config1 = get_dict(1);
        $this->assign('config1' , $config1);

        //装修类型
        $config2 = get_dict(2);
        $this->assign('config2' , $config2);

        //朝向
        $config4 = get_dict(4);
        $this->assign('config4' , $config4);

        //卧室类型
        $config6 = get_dict(6);
        $this->assign('config6' , $config6);

        //付款方式
        $config3 = get_dict(3);
        $this->assign('config3' , $config3);

    }
    /**
     * 租房管理
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function index(){

        $keyword = trim( input('keyword') );
        $start_time = input( 'start_time');
        $end_time = input( 'end_time');

        list($where, $sort, $order, $offset, $limit) = $this->buildparams( );

        $listModel = $this->model
                ->where( $where )
                ->order($sort, $order);
        if( $keyword ){
            $listModel->where( 'title|description' , 'like' , $keyword.'%');
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