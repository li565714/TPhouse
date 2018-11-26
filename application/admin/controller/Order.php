<?php
namespace app\admin\controller;
use think\Config;
class Order extends Base
{   
    
    protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

        $this->model = model('data');
        $this->allowField = true;
    }

    public function index(){
       
        $keyword = trim(input('keyword'));
        
        list($where, $sort, $order, $offset, $limit) = $this->buildparams('title');
        $listModel = $this->model
                ->where( $where )
                ->order("shen asc , id desc ", $order);

        if( $keyword ){
            $listModel->where('name|phone','like',$keyword.'%');
        } 
        $list =$listModel->paginate(10 ,false, ['query'=>request()->param()]);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list' , $list);
        $this->assign('page', $page);
        return $this->fetch();
    }


    public function details(){
        $id = input('id' , 0 );
        $data = $this->model->find( $id );

        $this->assign('data' , $data);
        return $this->fetch();
    }

    public function updateStatus(){
        list($where2, $sort, $order, $offset, $limit) = $this->buildparams();

        $ids = input('ids' , 0 );
        $status = input('status' , 1);
        if( !$ids ){
            $this->error('请选择要修改的内容');
        }
        $where['id'] = ['in' , $ids];

        $data = $this->model->find( $ids );

        $result = $this->model->where( $where )->update( array('shen' => $status) );

        if ($result !== false){
            require_once(S_ROOT.'/SUBMAIL/SUBMAILAutoload.php');
            $submail->setTo($data['phone']);
            $submail->SetContent('【蚂蚁金服】您的资料已经审核成功，请及时与客服人员联系。');
            $xsend=$submail->send();
            

            $this->success('编辑成功');
        } else {
            $this->error('编辑失败');
        }

    }


    

}



