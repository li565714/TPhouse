<?php
namespace app\index\controller;

use \think\Debug;
class House extends Base
{
    public function index(){

        
        $houseModel = model('house')->where(1);

        $page = input('page' , 1);
        $page_size = input('page_size' , 20);
        $is_hot = input( 'is_hot' );
        if( $is_hot ){
            $houseModel->where('is_hot' , $is_hot );
        }
        //线路
        $subway = input( 'subway' );
        if( $subway ){
            $subwayInfo = model('subway')->where( 'id' , $subway)->find();
            if( $subwayInfo){
                $lication = location_range( $subwayInfo['lng'] , $subwayInfo['lat'] , 1 );  //周围1KM的坐标，实际范围可能大于1KM 在1-1.5左右
                $houseModel->whereRaw(" lat<>0 and lat>{$lication['right-bottom']['lat']} and lat<{$lication['left-top']['lat']} ")
                           ->whereRaw(" lon>{$lication['left-top']['lng']} and lon<{$lication['right-bottom']['lng']}");
            }
            
        }
        //价格范围
        $amount = input('amount');
        if( $amount ) {
            $amounts = explode(',', $amount);
            if( $amounts ){
                $houseModel->whereBetween('house_amount' , $amounts );    
            }
        }

        //几室
        $room = input('room');
        if( $room ) {
            if( $room >= 5){
                $houseModel->where('room' , '>=' , $room );    
            } else {
                $houseModel->where('room' , $room );    
            }
           
        }

        
        //出租类型
        $house_type = input( 'house_type' , 16003 );
        if( $house_type ){
            $houseModel->where('house_type' , $house_type );
        }

        //出租类型
        $house_type = input( 'house_type' , 16003 );
        if( $house_type ){
            $houseModel->where('house_type' , $house_type );
        }

        //建筑类型  别墅 住宅
        $build_type = input( 'build_type' );
        if( $build_type ){
            $houseModel->where('build_type' , $build_type );
        }


        //出租类型
        $type_id = input( 'type_id' );

        if( $type_id ){
            $houseModel->where('type_id' , $type_id );
        }

        

        //装修类型
        $decorate_id = input( 'decorate_id' );
        if( $decorate_id ){
            $houseModel->where('decorate_id' , $decorate_id );
        }

        //卧室类型
        $room_type = input( 'room_type' );
        if( $room_type ){
            $houseModel->where('room_type' , $room_type );
        }
        //支付类型
        $pay_type = input( 'pay_type' );
        if( $pay_type ){
            $houseModel->where('pay_type' , $pay_type );
        }
        //关键词
        $keyword = input( 'keyword' );
        if( $keyword ){
            $houseModel->where('h.title|xq.name' , 'like' , '%'.$keyword.'%' );
        }

        $is_elevator = input('is_elevator');
        if( !empty($is_elevator)){
            $houseModel->where('is_elevator' , $is_elevator );
        }
        $direction = input( 'direction' );
        if( $direction ){
            $houseModel->where('direction' , $direction );
        }

        //来源
        $soure = input( 'soure' );
        if( $soure ){
            $houseModel->where('soure' , $soure );
        }
        // $count = $houseModel->where('is_delete' , 0)->where('status' , 1)->fetchSql(true)->count();             
        $list = $houseModel->alias('h')->join('xiaoqu xq','xq.id = h.xq_id' , 'LEFT')
                // ->join('house_dict t','t.code = h.type_id' , 'LEFT')
                // ->join('house_dict d','d.code = h.decorate_id' , 'LEFT')
                // ->join('house_dict di','di.code = h.direction' , 'LEFT')
                // ->join('house_dict rt','rt.code = h.room_type' , 'LEFT')
                // ->join('house_dict py','py.code = h.pay_type' , 'LEFT')
                ->where('is_delete' , 0)
                ->where('status' , 1)
                ->field( 'h.*,xq.name as xq_name')
                ->order('add_time desc')
                ->paginate($page_size );


        $dictModel = model('house_dict');
        $dictData = cache( 'cache_dict');
        if( !$dictData){
            $dictData = $dictModel->select();
            cache('cache_dict' , $dictData );
        }
      
        foreach ($list as $key => $value) {
            $list[$key]['phone'] = substr_replace($value['phone'],'****',3,4);
            
            //转换字典内容
            foreach ($dictData as $k => $val) {
                //type_id
                if( $value['type_id'] == $val['code'] ){
                    $list[$key]['type_name'] = $val['title'];
                    continue;
                }
                if( $value['decorate_id'] == $val['code'] ){
                    $list[$key]['decorate_name'] = $val['title'];
                    continue;
                }

                if( $value['direction'] == $val['code'] ){
                    $list[$key]['direction_name'] = $val['title'];
                    continue;
                }
                if( $value['room_type'] == $val['code'] ){
                    $list[$key]['room_type_name'] = $val['title'];
                    continue;
                }
                if( $value['pay_type'] == $val['code'] ){
                    $list[$key]['pay_type_name'] = $val['title'];
                    continue;
                }
            }


            $imgs_path = [];
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
        return json($data);
    }



    //获取房屋列表
    public function detail(){
        //验证用户令牌
        $userInfo = $this->checkToken( input('token') );

        $id = input( 'id' );
        if( !$id ){
            $this->resultInfo = lang('error_1000');
            return json( $this->resultInfo );
        }

        $data = model('house')->alias('h')->join('user','user.id = h.user_id' , 'LEFT')
                ->join('xiaoqu xq','xq.id = h.xq_id' , 'LEFT')
                ->join('house_dict t','t.code = h.type_id' , 'LEFT')
                ->join('house_dict d','d.code = h.decorate_id' , 'LEFT')
                ->join('house_dict di','di.code = h.direction' , 'LEFT')
                ->join('house_dict rt','rt.code = h.room_type' , 'LEFT')
                ->join('house_dict py','py.code = h.pay_type' , 'LEFT')
                ->join('house_dict hy','hy.code = h.build_type' , 'LEFT')
                ->join('house_desc','house_desc.house_id = h.id' , 'LEFT')
                ->field( 'h.* ,user.nickname , user.portrait ,xq.name as xq_name , xq.lat,xq.lon,t.title as type_name , d.title as decorate_name , di.title as direction_name  , rt.title as room_type_name , py.title as pay_type_name,house_desc.description,hy.title as build_type_name')
                ->where('h.status' , 1)
                ->where('is_delete' , 0)
                ->where('h.id' , $id)->find();
        if( $data ){
            //获取出租类型
            $data['config'] = explode( ',', $data['config_id']); //$dictModel->whereIn( 'code' , $value['config_id'] )->column( 'code');

            //获取图片地址
            if( $data['imgs'] ){
                $imgs = explode(',', $data['imgs']);   
                foreach ($imgs as $value) {
                    $imgPath[] = get_oss_img_crop($value);
                }
                
            }
            $data['description'] = htmlspecialchars_decode($data['description']);
            $data['imgs'] = $imgPath; 
            $data['is_collect'] = 0;  //判断用户是否收藏
            $data['is_check']   = 0;  //验证用户是否登录
            if( $userInfo ){
                $data['is_check'] = 1;
                //验证是否收藏
                $user_collect = model('user_collect')->where( 'uid' , $userInfo['uid'])->where( 'house_id' , $id)->count();
                if( $user_collect ){
                    $data['is_collect'] = 1;
                }
            } else {
                //未登录时 手机号脱敏
                $data['phone'] = substr_replace($data['phone'],'****',3,4);
            }


            //判断是否采集
            if( $data['soure_id'] ){
                $collect_soure = config('web.collect_soure');
                $data['portrait'] = $collect_soure[$data['soure']];
            }


            //获取距离附近的地铁
            $lication = location_range( $data['lon'] , $data['lat'] , 1 );

            $subway = model('subway')->whereRaw(" lat<>0 and lat>{$lication['right-bottom']['lat']} and lat<{$lication['left-top']['lat']} ")
                       ->whereRaw(" lng>{$lication['left-top']['lng']} and lng<{$lication['right-bottom']['lng']}")
                       ->where('pid' ,'<>' , 0)->group( 'lat , lng')->select();
            foreach ($subway as $key => $value) {
                $map[] = '距离' . $value['address'] . $value['name'] . getDistance( $data['lat'] , $data['lon'] , $value['lat'] , $value['lng'] );
            }
            $data['map'] = $map;
            
        }

        if( $data ){
            return json( array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $data ));
        } else {
            $this->resultInfo = lang('error_1200');
            return json( $this->resultInfo );
        }
        

    }


    /**
     * 房屋举报
     */
    public function complaints(){
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

        $params['uid'] = $userInfo['uid'];
        $params['num'] =  1;
        $this->model = model('complaints');
        $result = $this->model->allowField(true)->save( $params );
        if ($result !== false){
            //更新举报次数
            model('house')->setIncComplaints( $params['house_id'] );
            
            $this->resultInfo = lang('error_1000');
        } else {
            $this->resultInfo = lang('error_1004');
        }
        
        return json( $this->resultInfo );
        
    }

    public function collect(){
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

        $data = model('user_collect')->where( 'uid' , $userInfo['uid'])->where( 'house_id' , $params['house_id'])->find();
        if( !$data ){
            $addData = array(
                'uid' => $userInfo['uid'] , 
                'house_id' => $params['house_id'] , 
                'add_time' => time() , 
            );
            model('user_collect')->insert( $addData );
        } else{
            model('user_collect')->where('id' , $data['id'])->delete( $addData );
            $this->resultInfo = lang('error_1100');
        }

         return json( $this->resultInfo );


    }




}
