<?php
namespace app\index\controller;

class User extends Base
{

    
    /**
     * 用户登陆
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function login(){
        
        $openid = input( 'openid' );
        $nickname = input( 'nickname' );
        $headimgurl = input( 'headimgurl' );
        $system = input( 'system' );
        $device_id = input( 'device_id' );
        $auth_type = input('auth_type');

        //判断是否存在当前用户
        $userInfo = model('user')->where('wx_openid' , $openid)->field('id')->find();
       
        if( !$userInfo ){
            $uid =model('user')->insertGetId([
                'nickname' => $nickname ,
                'wx_openid' => $openid ,
                'portrait' => $headimgurl ,
                'add_time' => time() ,
                'auth_type' => $auth_type
            ]);
        } else {
            $uid = $userInfo['id'];
        }
        $userTokenModel = model('user_token');
        $map['uid'] = $uid ;
        $map['device_id'] = $device_id;
        $map['system'] = $system;
        $userToken =$userTokenModel->where( $map )->find();

        $token = md5( $uid . $device_id . $system . time() );
        if( $userToken ){
            $userTokenModel->where('id' , $userToken['id'])->update( ['token' => $token ]);
        } else {
            $userTokenModel->insert( [
                'uid'   => $uid,
                'token' => $token ,
                'device_id' => $device_id ,
                'system' => $system ,
                'login_time' => time()
            ]);
        }

        $data = [
            'token' => $token ,
            'nickname' => $nickname , 
            'portrait' => $headimgurl
        ];
        return json( ['status' => 1000 , 'msg' => 'ok' , 'data' => $data ]);

    }



    /**
     * 新增房屋信息
     */
    public function createHouse(){
        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }

        $params = $this->request->instance()->param();

        $result = $this->validate($params,'House.create');
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->resultInfo = lang('param_error_1001');
            $this->resultInfo['msg'] = $result;
            return json( $this->resultInfo );
        }

        //小区
        $xiaoqu = model('xiaoqu')->where('name' , $params['xiaoqu'])->find();
        if( $xiaoqu ){
            $params['xq_id'] = $xiaoqu['id'];
        } else {
            $params['xq_id'] = model('xiaoqu')->insertGetId( ['name' =>$params['xiaoqu'] , 'lat' => $params['lat'] , 'lon'=> $params['lon'] ] );
        }

        $params['user_id'] = $userInfo['uid'];
        $this->model = model('house');
        $result = $this->model->allowField(true)->save( $params );
        if ($result !== false){
            $this->resultInfo = lang('error_1000');
        } else {
            $this->resultInfo = lang('error_1004');
        }
        
        return json( $this->resultInfo );
        
    }

    public function house(){
        
        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }
        
        $houseModel = model('house');

        $page = input('page' , 1);
        $page_size = input('page_size' , 20);
      

        $list = $houseModel->alias('h')
                ->join('xiaoqu xq','xq.id = h.xq_id' , 'LEFT')
                ->join('house_dict t','t.code = h.type_id' , 'LEFT')
                ->join('house_dict d','d.code = h.decorate_id' , 'LEFT')
                ->join('house_dict di','di.code = h.direction' , 'LEFT')
                ->join('house_dict rt','rt.code = h.room_type' , 'LEFT')
                ->join('house_dict py','py.code = h.pay_type' , 'LEFT')
                
                ->where('user_id' , $userInfo['uid'])
                ->field( 'h.* ,xq.name as xq_name ,t.title as type_name , d.title as decorate_name , di.title as direction_name  , rt.title as room_type_name , py.title as pay_type_name')
                ->order( 'add_time desc')
                ->paginate($page_size);

        $dictModel = model('house_dict');
        foreach ($list as $key => $value) {
           
            $list[$key]['imgs_path'] = [];
            //获取图片信息
            if( $value['imgs']){
                $img = explode(',', $value['imgs']);
                foreach ($img as $k => $val) {
                    $imgs_path[] = get_oss_img_crop( $val , 300 , 220 );
                } 
                $list[$key]['imgs_path'] = $imgs_path;   
            }
            
        }

        $data = array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $list );
        return json( $data );
    
    }

    public function collect(){
        
        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }
        
        $houseModel = model('house')->where('status' , 1);

        $page = input('page' , 1);
        $page_size = input('page_size' , 20);
      

        $list = $houseModel->alias('h')->join('user_collect','user_collect.house_id = h.id')
                ->join('xiaoqu xq','xq.id = h.xq_id' , 'LEFT')
                ->join('house_dict t','t.code = h.type_id' , 'LEFT')
                ->join('house_dict d','d.code = h.decorate_id' , 'LEFT')
                ->join('house_dict di','di.code = h.direction' , 'LEFT')
                ->join('house_dict rt','rt.code = h.room_type' , 'LEFT')
                ->join('house_dict py','py.code = h.pay_type' , 'LEFT')
                ->where('is_delete' , 0)
                ->where('status' , 1)
                ->where('uid' , $userInfo['uid'])
                ->field( 'h.* ,xq.name as xq_name ,t.title as type_name , d.title as decorate_name , di.title as direction_name  , rt.title as room_type_name , py.title as pay_type_name')
                ->order( 'user_collect.add_time desc')
                ->paginate($page_size);

        $dictModel = model('house_dict');
        foreach ($list as $key => $value) {
           
            $list[$key]['imgs_path'] = [];
            //获取图片信息
            if( $value['imgs']){
                $img = explode(',', $value['imgs']);
                foreach ($img as $k => $val) {
                    $imgs_path[] = get_oss_img_crop( $val , 300 , 220 );
                } 
                $list[$key]['imgs_path'] = $imgs_path;    
            }
            
        }

        $data = array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $list );
        return json( $data );
    }


    public function del_house(){
    	//验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }

        $params = $this->request->instance()->param();

        $result = $this->validate($params,'House.complaints');
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->resultInfo = lang('param_error_1001');
            $this->resultInfo['msg'] = $result;
            return json( $this->resultInfo );
        }

        model('house')->where('id' , $params['house_id'])->delete( );

        return json( $this->resultInfo );
    }


    public function community(){

        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }

        $communityModel = model('Community')->where('c.status' , 1)->where('uid' , $userInfo['uid']);
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

    public function del_community(){
        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );
        if( !$userInfo ){
            return json( $this->resultInfo );
        }

        $params = $this->request->instance()->param();


        model('Community')->where('id' , $params['id'])->delete( );

        return json( $this->resultInfo );
    }









}
