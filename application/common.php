<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 获取阿里云OSS图片地址
 * @parameter string path  图片地址
 * @parameter string width 图片裁剪宽度
 * @parameter string height 图片裁剪高度
 */
function get_oss_img_crop( $imgpath = '' , $width = 0 , $height = 0 ){

    //判断是否站外地址
    if( strpos($imgpath, 'http://') === 0  || strpos($imgpath, 'https://')=== 0 ){
        return $imgpath;
    }
    $rootPath = config('QCLOUD_COS.oss_img_url');
    if( !$imgpath ){
        //返回默认图片
        return config('ROOT_URL').'/uploads/logo.png';
    }
    //判断图片是否存在
    /*if(!file_exists_x( $rootPath.'/'.$imgpath ) ){
        //返回默认图片
        return C('ROOT_URL').'/uploads/logo.png';
    }*/
    if( $width  && $height ){
        if( $width == $height ){
            $imageUrl = $rootPath . '/' . $imgpath . '_' .$width . 'x' . $height ;
        } else{
            $imageUrl = $rootPath . '/' . $imgpath ;//. '?x-oss-process=image/resize,m_fill,w_'.$width.',h_'.$height.',limit_0/auto-orient,1/quality,q_100';
            $imageUrl = $rootPath . '/' . $imgpath .'?imageMogr2/thumbnail/'.$width.'x'.$height.'!/interlace/0|imageMogr2/gravity/center/crop/'.$width.'x'.$height;
        }
    } else {
        $imageUrl = $rootPath . '/' . $imgpath;
    }
    return $imageUrl;
    
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string 
 */
function think_salt_md5($str,$salt = '' ){
  return '' === $str ? '' : md5(md5($str) . $salt);
}

/**
 * 生成订单编号
 */
function get_order_sn(){
  return  date('YmdHis') . rand(10000000,99999999);
}


/**
 * 生成随机字符串
 */
function getSalt($length = 5){
   $str = null;
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
   }

   return $str;
  
}


// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}


/**
 * 获取用户名称
 * @param $id 栏目id
 */
function get_user_name( $id = 0 ){
    return  model('user')->where( array('id' => $id ))->value('nickname') ;
}

//获取字典 名称
function get_dict_name( $id ){
  return model('house_dict')->where( 'code' , $id)->value('title');
}

//根据类型获取获取字典名称
function get_dict_type_name( $id ){
  return model('house_dict')->where( 'code' , $id)->value('title');
}


//根据类型获取获取字典名称
function get_xq_name( $id ){
  return model('xiaoqu')->where( 'id' , $id)->value('name');
}



function get_dict( $type  = 0 ){
    $data = cache('cache_dict');
    if( !$data ){
        $data = model('HouseDict')->select();
        cache('cache_dict' , $data );
    }

    $result = [];
    foreach ($data as $key => $value ) {
        if( $value['type'] == $type ){
            $result[] = $value;
        }
    }
    return $result;

}

/**
 * @param $lng
 * @param $lat
 * @param float $distance 单位：km
 * @return array
 * 根据传入的经纬度，和距离范围，返回所有在距离范围内的经纬度的取值范围
 */
function location_range($lng, $lat,$distance = 0.5){
    $earthRadius = 6372.140;//单位km
    $d_lng =  2 * asin(sin($distance / (2 * $earthRadius)) / cos(deg2rad($lat)));
    $d_lng = rad2deg($d_lng);
    $d_lat = $distance/$earthRadius;
    $d_lat = rad2deg($d_lat);
    // return array(
    //     'lat_start' => $lat - $d_lat,//纬度开始
    //     'lat_end' => $lat + $d_lat,//纬度结束
    //     'lng_start' => $lng-$d_lng,//纬度开始
    //     'lng_end' => $lng + $d_lng//纬度结束
    // );

    return array(
        'left-top'=>array('lat'=>$lat + $d_lat,'lng'=>$lng-$d_lng),
        'right-top'=>array('lat'=>$lat + $d_lat, 'lng'=>$lng + $d_lng),
        'left-bottom'=>array('lat'=>$lat - $d_lat, 'lng'=>$lng - $d_lng),
        'right-bottom'=>array('lat'=>$lat - $d_lat, 'lng'=>$lng + $d_lng)
    );

}

/**
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @return int
 */
function getDistance($lat1, $lng1, $lat2, $lng2){

    //将角度转为狐度

    $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度

    $radLat2=deg2rad($lat2);

    $radLng1=deg2rad($lng1);

    $radLng2=deg2rad($lng2);

    $a=$radLat1-$radLat2;

    $b=$radLng1-$radLng2;

    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;


    if($s < 1 ){
        $s = round($s * 1000 ,0);
        return $s . '米';
    }
    $s = round($s , 2);
    return $s . '千米';

}


function get_house_description( $id = 0){
	return model('house_desc')->where('house_id',$id)->column( 'description' );
}
