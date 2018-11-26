<?php
namespace app\admin\model;
/**
 * 基础控业务处理
 */
use think\Model;
use think\Request;
class User extends Model {


    //错误信息
    protected $errMsg = '';
    //获取错误信息
    public function getError(){
        return $this->errMsg;
    }

    /**
     * 登陆
     */
    public function login($username = '' , $password = ''){
        if(!$username  || !$password ){
            $this->errMsg = '账户密码不能为空';
            return false;
        }
        //获取用户信息
        $user = $this->where( array('username' => $username ) )->field( 'id , username ,salt, password,is_sys ')->find();

        if( !$user  || $user['is_sys'] != 1){
            $this->errMsg = '账户不存在或被禁用';
            return false;
        }
        //判断密码是否正确
        if( think_salt_md5($password , $user['salt']) == $user['password']  ){
            $this->autoLogin( $user );
            return true;
        } else {
            $this->errMsg = '密码错误';
            return false;
        }

    }

    /**
     * 登陆成功
     */ 
    private function autoLogin( $data = array() ){
        $last_login_time = time();
        $last_client_ip  = Request()->ip();
        $admin_user = [
            'uid' => $data['id'] ,
            'username' => $data['username'] , 
            'last_login_time' => $last_login_time ,
            'last_login_ip'  => $last_client_ip , 
        ];
        session('admin_user' , $admin_user );


        return false;
    }
    
}