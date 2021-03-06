<?php
namespace app\index\validate;

use think\Validate;

class House extends Validate
{
    //验证规则
    protected $rule = [
        'title' => 'require',
        'user_id' => 'require',
        'house_amount' => 'require',
        'house_price' => 'require',
        'years' => 'require',
        'house_cq' => 'require',
        'type_id' => 'require',
        'room'    => 'require',
        'hall'    => 'require',
        'who'     => 'require',
        'phone'   => 'require',
        'house_id'=> 'require',
    ];
    
    //错误消息
    protected $message = [
        
    ];

    //错误消息
    protected $scene= [
        'create' => ['title' , 'house_amount' , 'type_id' , 'phone' , 'room' , 'hall' , 'who' ],
        'create2' => ['title' , 'house_amount' , 'house_price' , 'years' , 'house_cq' , 'phone' , 'room' , 'hall' , 'who' ],
        'reg'     => ['title'],
        'complaints' => ['house_id']
    ];

}
