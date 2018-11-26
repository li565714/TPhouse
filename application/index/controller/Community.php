<?php
namespace app\index\controller;

class Community extends Base
{
    public function index(){

        
        $communityModel = model('Community')->where('c.status' , 1);
        $page = input('page' , 1);
        $page_size = input('page_size' , 20);
       

        //类型
        $type = input( 'type' );
        if( $type ){
            $communityModel->where('type' , $type );
        }


        $list = $communityModel->alias('c')->join('user','user.id = c.uid')
                ->field( 'c.* ,user.nickname , user.portrait')
                ->order( 'add_time desc')
                ->paginate($page_size);

        foreach ($list as $key => $value) {
            $list[$key]['imgs_path'] = [];
            //获取图片信息
            if( $value['imgs']){
                $img = explode(',', $value['imgs']);
                foreach ($img as $k => $val) {
                    $imgs_path[$k]['crop_img'] = get_oss_img_crop( $val , 300 , 300 );
                    $imgs_path[$k]['img']  = get_oss_img_crop( $val);
                } 
                $list[$key]['imgs_path'] = $imgs_path;
            }
            
        }


        $data = array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $list );
        return json( $data );
    }


    /**
     * 新增需求信息
     */
    public function create(){
        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }

        $params = $this->request->instance()->param();


        $params['uid'] = $userInfo['uid'];
        $params['status'] = 1;
        $this->model = model('Community');
        $result = $this->model->allowField(true)->save( $params );
        if ($result !== false){
            $this->resultInfo = lang('error_1000');
        } else {
            $this->resultInfo = lang('error_1004');
        }
        
        return json( $this->resultInfo );
        
    }



    



}
