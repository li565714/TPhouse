<?php
namespace app\admin\controller;
//引用model

/**
 * =============================
 * TP5商城
 * 站内信控制器
 */
class Operation extends Base{   

    protected $model = '';
    
    public function _initialize(){
        parent::_initialize();
        $this->allowField = true;

        $this->model = model("advert");
    }

    /**
     * 导航
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function advlist(){

    	$this->model = model("advert");
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();

        $pid = input('pid');
        if( !$pid ){
            $pid = 0;
        }
        $list = $this->model->where( $where )->where( array('pid'=>$pid) )
                ->order($sort, $order)
                ->paginate(10 ,false, ['query'=>request()->param()]);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list' , $list);
        $this->assign('page', $page);
     
        return $this->fetch();
    }

    /**
     * 添加广告位栏目
     * @author lgp
     * @datetime 2018/04/12 17:33
     */
    public function add_column(){

        if ($this->request->isPost())
        {

            $this->model = model("advert");
            $params = $this->request->post("");
            $result = $this->model->save($params);
            if ($result !== false){
                $this->success('添加成功' , url('advlist'));
            } else {
                $this->error($this->model->getError());
            }

        }

        return $this->fetch( );   
    }

    /**
     * 编辑广告位内容
     * @author lgp
     * @datetime 2018/04/12 17:33
     */
    public function  edit_column(){
        $this->model = model("advert");
        $params = input('request.');
        $row = $this->model->get(input('id'));
        if ($this->request->isPost())
        {
            $params = $this->request->post("");
            $result = $row->allowField(true)->save($params);
            if ($result !== false){
                $this->success('编辑成功' , url('advlist'));
            } else {
                $this->error($this->model->getError());
            }
        }
        $this->assign("row", $row);
        return $this->fetch(  );   
    }

    /**
     * 添加广告位内容
     * @author lgp
     * @datetime 2018/04/12 17:33
     */
    public function addAdvert(){

        if ($this->request->isPost())
        {

            $this->model = model("advert");
            $params = $this->request->post("");
            $params['start_time'] = strtotime($params['start_time']);
            $params['end_time'] = strtotime($params['end_time']);
            $result = $this->model->save($params);
            if ($result !== false){
                $this->success('添加成功' , url('advlist'));
            } else {
                $this->error($this->model->getError());
            }

        }

        return $this->fetch( 'add');   
    }

    /**
     * 编辑广告位内容
     * @author lgp
     * @datetime 2018/04/12 17:33
     */
    public function  editAdvert(){
        $this->model = model("advert");
        $params = input('request.');
        $row = $this->model->get(input('id'));
        if ($this->request->isPost())
        {
            $params = $this->request->post("");
            $params['start_time'] = strtotime($params['start_time']);
            $params['end_time'] = strtotime($params['end_time']);
            $result = $row->allowField(true)->save($params);
            if ($result !== false){
                $this->success('编辑成功' , url('advlist'));
            } else {
                $this->error($this->model->getError());
            }
        }
        $this->assign("row", $row);
        return $this->fetch( 'edit' );   
    }

    /**
     * 删除
     */
    public function delAdvert()
    {
        $this->model = model("advert");
        $ids = input('ids/a');
        if ($ids)
        {
            $count = $this->model->destroy($ids);
            if ($count)
            {
                $this->success('删除成功');
            }
        }
        $this->error('Parameter ids can not be empty');
    }



}
