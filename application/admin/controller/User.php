<?php
namespace app\admin\controller;

/**
 * =============================
 * TP5商城
 * 用户控制器
 */
class User extends Base{   

    /**
     * User模型对象
     */
    protected $model = null;

    public function _initialize(){
        parent::_initialize();
        $this->model = model('User');
    }

    /**
     * 用户管理
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function index(){

        $where = [];
        $keyword = input('keyword');

        list($where2, $sort, $order, $offset, $limit) = $this->buildparams();

        $list = $this->model
                ->where( array('username|nickname|phone' => array('like' , '%'.$keyword . '%')) )
                ->field(['password', 'salt', 'token'], true)
                ->order($sort, $order)
                ->paginate(10 ,false, ['query'=>request()->param()]);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list' , $list);
        $this->assign('page', $page);
            
        return $this->fetch();
    }

    /**
     * 用户管理
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function adminindex(){

        $where = [];
        $keyword = input('keyword');

        list($where, $sort, $order, $offset, $limit) = $this->buildparams();

        
        $list = $this->model
                ->where( array('id'=> array('like' , '1')) )
                ->field(['password', 'salt', 'token'], true)
                ->order($sort, $order)
                ->paginate(10 ,false, ['query'=>request()->param()]);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list' , $list);
        $this->assign('page', $page);
            
        return $this->fetch('index');
    }


    
    //编辑用户
    public function edit(){
        $this->model = model("user");
        $params = input('request.');
        $row = $this->model->get(input('ids'));
        if ($this->request->isPost())
        {
            $params = $this->request->post("");

            if( isset($params['password'])){
                if( $params['password'] != $params['rpassword'] ){
                    $this->error( '确认密码不相同');
                }

                $salt = getSalt(5);
                $password = think_salt_md5($params['password'] , $salt);
                $params['password'] = $password;
                $params['salt'] = $salt;
                
            }
            $result = $row->allowField(true)->save($params);
            if ($result !== false){
                $this->success('编辑成功' , url('index'));
            } else {
                $this->error($this->model->getError());
            }
        }
        $this->assign("row", $row);
        return $this->fetch( );   
    }


}
