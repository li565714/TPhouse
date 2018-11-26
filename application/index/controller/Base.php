<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Request;
use think\Lang;
class Base extends Controller
{

	protected $token = '';      //登录令牌

	protected $device_id = '';  //设备ID

    protected $resultInfo = '';  //错误信息


    public function _initialize()
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        $this->resultInfo = Lang::get('error_1000');

        $this->token = input( 'token' ); //令牌
        $this->device_id = input( 'device_id' ); //令牌
        

    }

    /**
     * 验证APP token
     * @author lgp
     * @datetime 2018-10-29
     */
    protected function checkToken( $token = '' ){

        $userTokenModel = model('user_token');
        $userToken = $userTokenModel->where( 'token' , $token )->find();
        if(!$userToken){
            $this->resultInfo = Lang::get('user_token_error_1002');
            return false;
        }
        return $userToken;
        

    }




}
