<?php
namespace app\admin\controller;
use think\Config;
class Configs extends Base{   
    
    protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

        $this->model = model('config');
        $this->allowField = true;
    }

    public function index(){
       
        $data = $this->model->find();
        $this->assign( 'data' ,  $data);

        return $this->fetch();
    }





    

}



