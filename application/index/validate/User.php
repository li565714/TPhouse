<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    //验证规则
    protected $rule = [
        'nickname' => 'require|checkName',
        'password' => 'require|checkPassword',
    ];
    
    //错误消息
    protected $message = [
        'nickname.require'    => '请输入用户名',
        'password.require'    => '密码不能为空',
        'password.checkPassword'     => '两次密码不一致',
    ];

    //错误消息
    protected $scene= [
        'set_pwd' => ['password'],
        'reg'     => ['nickname','password'],
    ];
    /**
     * 验证两次密码
     * @param $value
     * @param $rule
     * @param $data
     * @return string
     */
    protected function checkPassword($value, $rule ,$data){
        if($value != $data['password2']){
            return false;
        }
        return true;
    }

    /**
     * 验证是否存在用户名
     * @param $value
     * @return string
     */
    protected function checkName($value){
        if(get_user_info($value,1)||get_user_info($value,2)){
            return '账号已存在';
        }
        return true;
    }
}
