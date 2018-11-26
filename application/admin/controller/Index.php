<?php
namespace app\admin\controller;
use think\Config;
use think\Request;
class Index extends Base
{   
    
    //管理员后台登陆
    public function login(){
        
        if ($this->request->isPost()){
            $data = input();

            if(!captcha_check($data['verify'])){
                $this->error('验证码不正确');
            };
            $userModel = model('user');
            $result = $userModel->login($data['username'] , $data['password']);
            if( $result ){
                $this->success('登录成功' , url('index/index') );
            } else {
                $this->error($userModel->getError() );
            }
        } else{
            return $this->fetch();       
        }
        
    }

    public function logout(){
        session('admin_user' , null);
        $this->redirect('index/login' );
    }

    public function index(){
        return $this->fetch();
    }

    public function index_v3(){
        return $this->fetch();
    }


    public function editpwd(){

        if (Request::instance()->isPost()){
            $this->model = model('user');
            $this->allowField = true;

            $ids = input('id' , 0 );
            $password = input('password');
            $rpassword = input('rpassword');
            if( !$ids   ){
                $this->error('请选择要修改的内容');
            }
            if( !$password ){
                $this->error('密码不能为空');   
            }
            if( $password  != $rpassword){
                $this->error('确认密码不正确');   
            }
            $where['id'] = ['in' , $ids];
            $result = $this->model->where( $where )->update( array('password' => md5($password)) );

            if ($result !== false){
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }
        } else {
            return $this->fetch();
        }

        
    }




    

}



