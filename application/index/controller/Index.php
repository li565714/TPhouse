<?php
namespace app\index\controller;

class Index extends Base
{
    public function index(){

        //获取Banner
        $advert = model('advert')->where(array('pid' => 1) )->select();

        foreach ($advert as $key => $value) {
            //获取图片地址
            $advert[$key]['img_path'] = get_oss_img_crop($value['img_path'] , 500 , 500);
        }

        $data = array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $advert );

        return json( $data );
    }

    //获取字典信息
    public function getDict(){
        $type = input( 'type' );

        $dictModel = model('house_dict');

        $data = $dictModel->whereIn( 'type' , $type )->cache('dict_' . $type , 3600 )->field('id , title , code,type')->select();

        return json( array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $data ) );
    }

    //获取地铁筛选列表
    public function subway(){
        $pid = input( 'pid' , 0);

        $data = model('subway')->cache('subway_' . $pid , 3600 )->where( 'pid' , $pid)->select();

        return json( array( 'status' => 1000 , 'msg' => 'ok' ,'data' => $data ) );
    }


}
